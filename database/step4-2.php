<?php
include_once 'connect.php';
$conn = connect();
session_start();

$pickup_date = $_GET['pickup_date'];
$pickup_time = $_GET['pickup_time'];
$return_date = $_GET['return_date']; // Retrieve the return date
$day = $_GET['day'];
$car_name = $_GET['car_name'];
$price = $_GET['price'];
$car_image = $_GET['car_image'];
$car_model = $_GET['car_model'];
$renter_name = $_GET['renter_name'];
$renter_email = $_GET['renter_email'];
$renter_contact = $_GET['renter_contact'];
$extra_price = $_GET['extra_price'];
$total_price = $_GET['total_price'];


// Retrieve car information from the database using car_id
$sql = "SELECT car_name, model, image, accessories, overview FROM vehicles WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $car_id);
$stmt->execute();
$stmt->bind_result($car_name, $car_model, $car_image, $car_accessories, $car_overview);
$stmt->fetch();
$stmt->close();

// Initialize variables for error messages and success messages
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_booking'])) {
    // Retrieve and sanitize form inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $payment_method = trim($_POST['payment_method']);

    // Retrieve booking details from session
    $pickup_date = $_SESSION['pickup_date'] ?? '';
    $pickup_time = $_SESSION['pickup_time'] ?? '';
    $return_date = $_SESSION['return_date'] ?? '';
    $return_time = $_SESSION['return_time'] ?? '';
    $rental_duration = intval($_SESSION['day'] ?? 0);

    // Ensure the retrieved car rental fee and extra price are floats
    $car_rental_fee = floatval(htmlspecialchars($_GET['price']));
    $extra_price = floatval(htmlspecialchars($_GET['extra_price']));

    // Calculate total price
    $total_price = $rental_duration * $car_rental_fee + $extra_price; // Ensure you calculate it here

    // Validate required fields
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    }
    if (empty($contact)) {
        $errors[] = "Contact number is required.";
    }
    if (empty($address)) {
        $errors[] = "Address is required.";
    }
    if (empty($city)) {
        $errors[] = "City is required.";
    }
    if (empty($payment_method)) {
        $errors[] = "Payment method is required.";
    }
    if (!isset($_POST['agree_terms'])) {
        $errors[] = "You must agree to the rental terms.";
    }

    // Handle Valid ID upload
    if (isset($_FILES['id-upload']) && $_FILES['id-upload']['error'] === UPLOAD_ERR_OK) {
        $valid_id_tmp_path = $_FILES['id-upload']['tmp_name'];
        $valid_id_name = basename($_FILES['id-upload']['name']);
        $valid_id_size = $_FILES['id-upload']['size'];
        $valid_id_type = strtolower(pathinfo($valid_id_name, PATHINFO_EXTENSION));

        // Allowed file types for Valid ID
        $allowed_id_types = ['jpg', 'jpeg', 'png'];

        if (!in_array($valid_id_type, $allowed_id_types)) {
            $errors[] = "Invalid file type for Valid ID. Only JPG, JPEG, and PNG are allowed.";
        }

        if ($valid_id_size > 5 * 1024 * 1024) { // 5MB limit
            $errors[] = "Valid ID file size exceeds the 5MB limit.";
        }

        if (empty($errors)) {
            // Generate a unique file name to prevent overwriting
            $valid_id_new_name = uniqid('valid_id_', true) . '.' . $valid_id_type;
            $valid_id_upload_dir = "uploads/valid_ids/";
            if (!is_dir($valid_id_upload_dir)) {
                mkdir($valid_id_upload_dir, 0755, true);
            }
            $valid_id_destination = $valid_id_upload_dir . $valid_id_new_name;

            // Move the uploaded file to the destination directory
            if (!move_uploaded_file($valid_id_tmp_path, $valid_id_destination)) {
                $errors[] = "Failed to upload Valid ID.";
            }
        }
    } else {
        $errors[] = "Valid ID is required.";
    }

    //Handle Selfie upload (Base64 encoded string)
    if (!empty($_POST['selfie'])) {
        $selfie_data = $_POST['selfie'];

        list($type, $selfie_data) = explode(';', $selfie_data);
        list(, $selfie_data) = explode(',', $selfie_data);
        $selfie_decoded = base64_decode($selfie_data);

        if ($selfie_decoded === false) {
            $errors[] = "Invalid selfie image data.";
        } else {
            if (strpos($type, 'image/png') !== false) {
                $selfie_extension = 'png';
            } elseif (strpos($type, 'image/jpeg') !== false) {
                $selfie_extension = 'jpg';
            } else {
                $errors[] = "Unsupported selfie image type.";
            }

            if (empty($errors)) {
                $selfie_new_name = uniqid('selfie_', true) . '.' . $selfie_extension;
                $selfie_upload_dir = "uploads/";
                if (!is_dir($selfie_upload_dir)) {
                    mkdir($selfie_upload_dir, 0755, true);
                }
                $selfie_destination = $selfie_upload_dir . $selfie_new_name;

                if (!file_put_contents($selfie_destination, $selfie_decoded)) {
                    $errors[] = "Failed to save selfie image.";
                }
            }
        }
    } else {
        $errors[] = "Selfie is required.";
    }



    // Check for errors
    if (empty($errors)) {
        // Generate a random reference ID (6 characters long, mix of letters and numbers)
        $reference_id = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
    
        // Prepare the SQL statement for inserting the booking with reference_ID
        $stmt = $conn->prepare("INSERT INTO booking (name, address, city, email, contact, valid_id, selfie, image, car_name, model, price, from_date, until_date, pickup_time, drop_time, payment_method, reference_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
        if ($stmt) {
            // Bind parameters and ensure total_price is a float
            $stmt->bind_param(
                'ssssssssssdssssss',
                $name,
                $address,
                $city,
                $email,
                $contact,
                $valid_id_new_name,
                $selfie_new_name,
                $car_image, // New car image path
                $car_name,
                $car_model,
                $total_price, // Make sure total_price is a float
                $pickup_date,
                $return_date,
                $pickup_time,
                $return_time,
                $payment_method,
                $reference_id
            );
    
            // Include PHPMailer files
            require 'PHPMailer/class.phpmailer.php';
            require 'PHPMailer/class.smtp.php';
            require 'PHPMailer/class.pop3.php'; // Include if you need POP3 support
    
            $mail = new PHPMailer();
    
            //$mail->SMTPDebug = 3; // Enable verbose debug output (for testing only)
    
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'bertgumapz@gmail.com'; // SMTP username
            $mail->Password = 'fbosaubaexglpoeg'; // SMTP password (ensure this is your app-specific password)
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to
    
            $mail->setFrom('bertgumapz@gmail.com', 'Chadoyven Car Rental'); // Sender's email
            $mail->addAddress($email, $name); // Recipient's email
            $mail->isHTML(true); // Set email format to HTML
    
            if ($stmt->execute()) {
                // Prepare the email content
                $subject = "Booking Confirmation";
                $message = "<p>Dear $name,</p>";
                $message .= "<p>Thank you for your booking. Here are your booking details:</p>";
                $message .= "<ul>";
                $message .= "<li><strong>Car Name:</strong> $car_name</li>";
                $message .= "<li><strong>Model:</strong> $car_model</li>";
                $message .= "<li><strong>Price:</strong> ₱" . number_format($total_price, 2) . "</li>";
                $message .= "<li><strong>From:</strong> $pickup_date</li>";
                $message .= "<li><strong>Pickup Time:</strong> $pickup_time</li>";
                $message .= "<li><strong>Until:</strong> $return_date</li>";
                $message .= "<li><strong>Drop Time:</strong> $return_time</li>";
                $message .= "<li><strong>Payment Method:</strong> $payment_method</li>";
                $message .= "<li><strong>Reference ID:</strong> $reference_id</li>";
                $message .= "</ul>";
                $message .= "<p>If you have any questions, feel free to contact us.</p>";
                $message .= "<p>Best Regards,<br>Chadoyven Car Rental</p>";
    
                // Assign the email subject and body to PHPMailer
                $mail->Subject = $subject;
                $mail->Body = $message;
                $mail->AltBody = strip_tags($message); // Plain text version for non-HTML mail clients
    
                // Try to send the email
                if ($mail->send()) {
                    // Redirect to another file and pass the reference_id as a GET parameter
                    header("Location: index.php?reservation=success&reference_id=" . urlencode($reference_id) . "&name=" . urlencode($name) . "&email=" . urlencode($email));
                    exit();
                } else {
                    $errors[] = "Error sending confirmation email: " . $mail->ErrorInfo;
                }
                
                
            } else {
                $errors[] = "Error confirming booking: " . $stmt->error;
            }
    
            $stmt->close();
        } else {
            $errors[] = "Error preparing statement: " . $conn->error;
        }
    }
    
}

// HTML Form to Confirm Booking
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental System</title>
    <link rel="icon" type="image/x-icon" href="image/logo.jpg">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .booking-details-box1 {
            padding: 2rem; /* Internal padding */
            width: 25%; /* Adjust this width as needed */
            height: 500px;
            max-height: 300px; /* Maximum height for the box */
            background-color: #fff;
            border: 2px solid #ccc; /* Visible border */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow for depth */
            border-radius: 8px;
            margin-top: 2rem; /* Space between the dropdowns and the box */
            margin-left: 12rem; /* Align the box to the left */
            overflow-y: auto; /* Enable vertical scrolling if content exceeds max height */
        }
        hr{
            border: 0;
            height: 2px;
            width: 100%;
            background: #ccc;
            margin: 15px 0 10px;
        }

        /* Add your CSS styles here */
        .error { color: red; }
        .success { color: green; }
        .content-container { /* Styles for the container */ }
        .form-group1 { margin-bottom: 15px; }
        .form-input { width: 100%; padding: 8px; }
        .button-section { margin-top: 20px; }
        .continue-btn { padding: 10px 20px; }

        .content-container {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }
        /* Extra Small Screens (very small phones, e.g., 320px width) */
        @media (max-width: 375px) {
            .summary-left, .summary-right {
                width: 50%;
                text-align: left; /* Stack items */
                padding: 0.5rem 0;
            }

            .button-section {
                flex-direction: column;
                margin-left: 0;
                margin-right: 0;
            }

            .back-btn, .continue-btn {
                width: 100%; /* Full width for small screens */
                padding: 0.8rem;
            }
            .content-container {
                flex-direction: column; /* Stack items vertically */
            }

            /* Ensure .booking-details-box1 is displayed below .car-details-container */
            .car-details-container {
                order: -1; /* Sets .car-details-container above */
            }
            .booking-details-box1 {
                order: 1; /* Sets .booking-details-box1 below */
                width: 90%;
                margin-left: 3%;
            }
            .car-details-box{
                width: 360px;
                margin-left: 40%;
            }
            
        }

        /* Large Phones and Small Tablets (between 376px and 768px) */
        @media (min-width: 376px) and (max-width: 768px) {
            .summary-left, .summary-right {
                width: 50%;
            }

            .button-section {
                margin-left: 0;
                margin-right: 0;
            }

            .continue-btn {
                width: 100%;
                margin-top: 1rem;
            }
            .back-btn {
                width: 15%;
                margin-top: 1rem;
            }
            .content-container {
                flex-direction: column; /* Stack items vertically */
            }

            /* Ensure .booking-details-box1 is displayed below .car-details-container */
            .car-details-container {
                order: -1; /* Sets .car-details-container above */
            }

            .booking-details-box1 {
                order: 1; /* Sets .booking-details-box1 below */
                width: 90%;
                margin-left: 3%;
            }
            .car-details-box{
                width: 430px;
                margin-left: 40%;
            }
        }

        /* Large Tablets (768px and up) */
        @media (min-width: 769px) and (max-width: 992px) {
            .summary-left, .summary-right {
                width: 50%; /* Split width on larger tablets */
            }

            .button-section {
                flex-direction: row;
                justify-content: space-between;
                margin-left: 5rem;
                margin-right: 5rem;
            }

            .back-btn, .continue-btn {
                width: auto;
            }
            .content-container {
                flex-direction: column; /* Stack items vertically */
            }

            /* Ensure .booking-details-box1 is displayed below .car-details-container */
            .car-details-container {
                order: -1; /* Sets .car-details-container above */
            }

            .booking-details-box1 {
                order: 1; /* Sets .booking-details-box1 below */
                width: 50%;
                margin-left: 3%;
            }
            .car-details-box{
                width: 530px;
                margin-left: 40%;
            }
        }
    </style>
</head>
<body>
    <header class="header">

        <div id="menu-btn" class="fas fa-bars"></div>

        <a href="index.php" class="logo">
            <img style="border-radius: 10px;" src="image/logo.jpg" alt="Car Rental Logo">
            <h2> Chadoyven Car Rental</h2>
        </a>
        <nav class="navbar">
            <a href="reservation.php">reservations</a>
            <a href="index.php">home</a>
            <a href="vehicle.php">vehicles</a>
            <a href="about.php">about</a>
            <a href="contact.php">contact</a>
            <a href="manage.php">manage bookings</a>
        </nav>

        <div id="login-btn">
            
        </div>
    </header>

    <section class="home1">
        <div class="image-container">
            <img src="image/bg1.1.jpg" alt="Car Rental" />
        </div>
        <div class="home-content">
            <h1>Rent a Car</h1>
            <div class="subtitle">Choose your car</div>
        </div>
        
    </section>


    <section class="step2-section">
        <div class="step2-header">
            <!-- Left: Step Text and Step Dropdown -->
            <div class="left-content">
                <div class="step-text">
                    <h2>Step 4:  Fill in your details to confirm your reservation.</h2>
                </div>
                <div class="dropdown1">
                    <button class="dropbtn1">Step 4 of 5 - identification</button>
                    <div class="dropdown1-content">
                        <a style="font-weight:bold" href="#">Step 1 - When</a>
                        <a style="font-weight:bold" href="#">Step 2 - Choose Car</a>
                        <a style="font-weight:bold" href="#">Step 3 - Price</a>
                        <a style="font-weight:bold" href="#">Step 4 - Identification</a>
                        <a href="#">Step 5 - Finish</a>
                    </div>
                    
                </div>
            </div>
        </div>
    
        <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success">
            <?= htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="content-container">
            <!-- Booking Details Box -->
            <div class="booking-details-box1">
                <div class="booking-details">
                    <h3>Booking Details</h3>
                        <p>Pick-up Date: <?= htmlspecialchars($pickup_date); ?></p>
                        <p>Pick-up Time: <?= htmlspecialchars($pickup_time); ?></p>
                        <p>Return Date: <?= htmlspecialchars($return_date); ?></p>
                        <p>Return Time: <?= htmlspecialchars($pickup_time); ?></p>
                        <p>Rental Duration: <?= htmlspecialchars($day); ?> Day(s)</p>

                    <br>
                    <h3>Car Selected</h3>
                    <div style="width: 100%;" class="car-details-container">
                        <p><?= htmlspecialchars($car_name); ?></p>
                        <div class="car-image">
                            <img src="admin/uploads/<?= htmlspecialchars($car_image); ?>" alt="<?= htmlspecialchars($car_name); ?>" style="width: 300px;" />
                        </div>
                        <p><strong>Car Model:</strong> <?= htmlspecialchars($car_model); ?></p>
                    </div>
                </div>
            </div>

            <!-- Payment Summary Box -->
            <div class="car-details-container">
                <div class="car-details-box">
                    <h4 class="car-name">Payment Summary</h4>
                    
                    <!-- Payment Summary Table -->
                    <div class="payment-summary">
                        <!-- Left side: Descriptions -->
                        <div class="summary-left">
                            <p>Rental Duration:</p>
                            <p>Car Rental Fee:</p>
                            <p>Extra Price:</p>
                            <hr>
                            <p>Total Price:</p>
                        </div>

                        <!-- Right side: Prices -->
                        <div class="summary-right">
                            <p><?= htmlspecialchars($day); ?> Day(s)</p>
                            <p>₱<?= number_format($price, 2); ?></p>
                            <p>₱<?= number_format($extra_price, 2); ?></p>
                            <hr>
                            <p>₱<?= number_format($total_price, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="personal-details-section">
        <div class="details-box">
            <!-- Personal Details Header -->
            <h3 class="details-title">Personal Details</h3>

            <!-- Form Fields for Personal Information -->
            <div class="form-row">
                <div class="form-group1">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($renter_name); ?>" class="form-input" required>
                </div>

                <div class="form-group1">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($renter_email); ?>" class="form-input" required>
                </div>

                <div class="form-group1">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="contact" placeholder="Enter your phone number" value="<?= htmlspecialchars($renter_contact); ?>" class="form-input" required>
                </div>
            </div>

            <!-- Upload ID Section -->
            <div class="form-group1">
                <label for="id-upload">Upload Valid ID (National/Driver's ID)</label>
                <input type="file" id="id-upload" name="id-upload" class="form-input" accept=".jpg, .jpeg, .png, .gif, .pdf" required>
            </div>

            <!-- Camera Capture Section -->
            <div class="form-group1">
                <label for="selfie-capture">Capture Selfie</label>
                <video id="video" width="90%" autoplay></video>
                <button type="button" id="capture-btn" class="form-input">Get Selfie</button>
                <canvas id="canvas" style="display:none;"></canvas>
                <img id="selfie" alt="Captured Selfie" style="display:none; margin-top: 10px; width: 100%; border-radius: 5px;"/>
                <input type="hidden" name="selfie" id="selfie-data" required>
            </div>
        </div>

        <!-- Address Box -->
        <div class="address-box">
            <h3 class="address-title">Fill in Your Address</h3>
            <div class="form-row">
                <div class="form-group1">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" placeholder="Enter your address" class="form-input" required>
                </div>
                <div class="form-group1">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="Enter your city" class="form-input" required>
                </div>
            </div>
        </div>

        <!-- Payment Method Box -->
        <div class="payment-box">
            <h3 class="payment-title">Payment Method</h3>
            <div class="form-group1">
                <label for="payment-method">Select Payment Method</label>
                <select id="payment-method" name="payment_method" class="form-input" required>
                    <option value="">Choose a payment method</option>
                    <option value="gcash">GCash</option>
                    <!-- Add more payment methods if needed -->
                </select>
            </div>
        </div>

        <!-- Rental Terms Box -->
        <div class="terms-box">
            <h3 class="terms-title">Rental Terms</h3>
            <div class="form-group1">
                <label>
                    <input type="checkbox" id="agree-terms" name="agree_terms" class="form-input" required>
                    I have read and agree to the rental terms
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <section>
            <div class="button-section">
                <button type="submit" name="confirm_booking" class="continue-btn">Confirm Booking</button>
            </div>
        </section>
    </section>
</form>
    <br>
</body>
<section class="footer">
    <div class="credit"> create by me web designer | all rights reserved!</div>
</section>

<script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>
<script>
// Access the video element
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const selfieImage = document.getElementById('selfie');
const captureBtn = document.getElementById('capture-btn');
const selfieData = document.getElementById('selfie-data');

// Access the camera
navigator.mediaDevices.getUserMedia({ video: true })
    .then((stream) => {
        video.srcObject = stream; // Set the video source to the camera stream
        video.play();
    })
    .catch((error) => {
        console.error('Error accessing the camera: ', error);
        alert('Unable to access the camera. Please allow camera access or use a different device.');
    });

// Capture the selfie
captureBtn.addEventListener('click', () => {
    // Set the canvas size to match the video size
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    // Draw the current video frame to the canvas
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Get the image data from the canvas
    const dataURL = canvas.toDataURL('image/png');
    selfieImage.src = dataURL; // Set the captured image as the source of the img element
    selfieImage.style.display = 'block'; // Show the captured selfie

    // Set the Base64 string to the hidden input
    selfieData.value = dataURL;

    // Optionally, hide the video and capture button after capture
    video.style.display = 'none';
    captureBtn.style.display = 'none';
});

</script>


</body>
</html>