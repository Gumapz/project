<?php
include_once 'connect.php';
$conn = connect();
session_start();

// Get values from the URL
$rental_duration = htmlspecialchars($_GET['rental_duration']);
$car_rental_fee = htmlspecialchars($_GET['car_rental_fee']);
$extra_price = htmlspecialchars($_GET['extra_price']);
$total_price = htmlspecialchars($_GET['total_price']);



// Initialize variables for car details
$car_id = null;
$car_name = 'Not specified';
$car_model = 'Not specified';
$car_image = 'Not specified';
$car_seat = 'Not specified';
$car_fuel = 'Not specified';
$car_accessories = 'Not specified';
$car_overview = 'Not specified';

// Check if car details are passed via URL
if (isset($_GET['car_id'])) {
    $car_id = htmlspecialchars($_GET['car_id']);
}
if (isset($_GET['car_name'])) {
    $car_name = htmlspecialchars($_GET['car_name']);
}
if (isset($_GET['car_model'])) {
    $car_model = htmlspecialchars($_GET['car_model']);
}
if (isset($_GET['car_image'])) {
    $car_image = htmlspecialchars($_GET['car_image']);
}
if (isset($_GET['car_seat'])) {
    $car_seat = htmlspecialchars($_GET['car_seat']);
}
if (isset($_GET['car_fuel'])) {
    $car_fuel = htmlspecialchars($_GET['car_fuel']);
}
if (isset($_GET['car_accessories'])) {
    $car_accessories = htmlspecialchars($_GET['car_accessories']);
}
if (isset($_GET['car_overview'])) {
    $car_overview = htmlspecialchars($_GET['car_overview']);
}




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
    $rental_duration = intval($_SESSION['rental_duration'] ?? 0);
    $car_name = $_SESSION['car_name'] ?? '';
    $car_model = $_SESSION['car_model'] ?? '';
    $car_fuel = $_SESSION['car_fuel'] ?? '';
    $car_seats = $_SESSION['car_seats'] ?? '';
    $car_rental_fee = floatval($_SESSION['car_rental_fee'] ?? 0);
    $extra_price = floatval($_SESSION['extra_price'] ?? 0);
    $total_price = floatval($_SESSION['total_price'] ?? 0);

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
            if (move_uploaded_file($valid_id_tmp_path, $valid_id_destination)) {
                // File uploaded successfully
            } else {
                $errors[] = "Failed to upload Valid ID.";
            }
        }
    } else {
        $errors[] = "Valid ID is required.";
    }

    // Handle Selfie upload (Base64 encoded string)
    if (!empty($_POST['selfie'])) {
        $selfie_data = $_POST['selfie'];

        // Decode the Base64 string
        list($type, $selfie_data) = explode(';', $selfie_data);
        list(, $selfie_data) = explode(',', $selfie_data);
        $selfie_decoded = base64_decode($selfie_data);

        if ($selfie_decoded === false) {
            $errors[] = "Invalid selfie image data.";
        } else {
            // Determine the image type
            if (strpos($type, 'image/png') !== false) {
                $selfie_extension = 'png';
            } elseif (strpos($type, 'image/jpeg') !== false) {
                $selfie_extension = 'jpg';
            }  else {
                $errors[] = "Unsupported selfie image type.";
            }

            if (empty($errors)) {
                // Generate a unique file name
                $selfie_new_name = uniqid('selfie_', true) . '.' . $selfie_extension;
                $selfie_upload_dir = "uploads/selfies/";
                if (!is_dir($selfie_upload_dir)) {
                    mkdir($selfie_upload_dir, 0755, true);
                }
                $selfie_destination = $selfie_upload_dir . $selfie_new_name;

                // Save the selfie image to the server
                if (file_put_contents($selfie_destination, $selfie_decoded)) {
                    // Selfie saved successfully
                } else {
                    $errors[] = "Failed to save selfie image.";
                }
            }
        }
    } else {
        $errors[] = "Selfie is required.";
    }

    // If no errors, proceed to insert data into the database
    if (empty($errors)) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO booking (name, address, city, email, contact, valid_id, selfie, car_name, model, fuel, seats, price, from_date, until_date, pickup_time, drop_time, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }

        // Bind the parameters to the SQL statement
        $stmt->bind_param(
            "sssssssssssissssss",
            $name,
            $address,
            $city,
            $email,
            $contact,
            $valid_id_destination,
            $selfie_destination,
            $car_name,
            $car_model,
            $car_fuel,
            $car_seats,
            $total_price,
            $pickup_date,
            $return_date,
            $pickup_time,
            $return_time,
            $payment_method
        );

        // Execute the statement
        if ($stmt->execute()) {
            $success = "Booking confirmed and saved successfully!";
            // Optionally, you can clear the session or redirect the user
            // session_unset();
            // session_destroy();
            header("Location: index.php");
            // exit();
        } else {
            $errors[] = "Database insertion failed: " . htmlspecialchars($stmt->error);
        }

        // Close the statement
        $stmt->close();
    }

    // Close the database connection
    $conn->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental System</title>
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
    </style>
</head>
<body>
    <header class="header">

        <div id="menu-btn" class="fas fa-bars"></div>

        <a href="index.html" class="logo">
            <img style="border-radius: 10px;" src="image/logo.jpg" alt="Car Rental Logo">
            <h2> Chadoyven Car Rental</h2>
        </a>
        <nav class="navbar">
            <a href="#reservations">reservations</a>
            <a href="index.html">home</a>
            <a href="vehicle.html">vehicles</a>
            <a href="about.html">about</a>
            <a href="contact.html">contact</a>
            <a href="manage.html">manage bookings</a>
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
                    <?php if (isset($_SESSION['name'])): ?>
                        <p>Pick-up Date: <?= htmlspecialchars($_SESSION['pickup_date']); ?></p>
                        <p>Pick-up Time: <?= htmlspecialchars($_SESSION['pickup_time']); ?></p>
                        <p>Return Date: <?= htmlspecialchars($_SESSION['return_date']); ?></p>
                        <p>Return Time: <?= htmlspecialchars($_SESSION['return_time']); ?></p>
                        <p>Rental Duration: <?= htmlspecialchars($_SESSION['rental_duration']); ?> Day(s)</p>
                    <?php else: ?>
                        <p>No booking details available.</p>
                    <?php endif; ?>

                    <br>
                    <h3>Car Selected</h3>
                    <div style="width: 100%;" class="car-details-container">
                        <p><?= htmlspecialchars($car_name); ?></p>
                        <div class="car-image">
                            <img src="admin/uploads/<?= htmlspecialchars($car_image); ?>" alt="<?= htmlspecialchars($car_name); ?>" style="width: 300px;" />
                        </div>
                        <p><strong>Car Model:</strong> <?= htmlspecialchars($car_model); ?></p>
                        <p><strong>Fuel Type:</strong> <?= htmlspecialchars($car_fuel); ?></p>
                        <p><strong>Seats:</strong> <?= htmlspecialchars($car_seats); ?></p>
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
                            <p><?= htmlspecialchars($rental_duration); ?> Day(s)</p>
                            <p>₱<?= number_format($car_rental_fee, 2); ?></p>
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
                    <input type="text" id="name" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($_SESSION['name']); ?>" class="form-input" required>
                </div>

                <div class="form-group1">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($_SESSION['email']); ?>" class="form-input" required>
                </div>

                <div class="form-group1">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="contact" placeholder="Enter your phone number" value="<?= htmlspecialchars($_SESSION['contact']); ?>" class="form-input" required>
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
                <video id="video" width="100%" autoplay></video>
                <button type="button" id="capture-btn" class="form-input">Get Selfie</button>
                <canvas id="canvas" style="display:none;"></canvas>
                <img id="selfie" alt="Captured Selfie" style="display:none; margin-top: 10px;"/>
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
                <a href="step3.php?car_id=<?= urlencode($_GET['car_id'] ?? ''); ?>" class="back-btn">Back</a>
                <button type="submit" name="confirm_booking" class="continue-btn">Confirm Booking</button>
            </div>
        </section>
    </section>
</form>
    
    
    




    <section class="company-info">
        <h2>Our Company</h2>
        <p>
            Welcome to [Your Company Name]! We are dedicated to providing the best car rental services to ensure you have a smooth and enjoyable experience. Our fleet includes a variety of vehicles to suit your needs, whether you're traveling for business or leisure. Our team is committed to excellent customer service and is here to assist you at every step of your journey.
        </p>
        <p>
            Contact us for more information or to make a reservation today!
        </p>
    </section>
    

    
</body>





<section class="footer">
    <div class="box-container">
        <div class="box">
            <h3>quick links</h3>
            <a href="#"><i class="fas fa-arrow-right"></i> home </a>
            <a href="#"><i class="fas fa-arrow-right"></i> home </a>
            <a href="#"><i class="fas fa-arrow-right"></i> home </a>
            <a href="#"><i class="fas fa-arrow-right"></i> home </a>
        </div>

        <div class="box">
            <h3>quick links</h3>
            <a href="#"><i class="fas fa-phone"></i> +64864864 </a>
            <a href="#"><i class="fas fa-phone"></i> +64864864 </a>
            <a href="#"><i class="fas fa-envelope"></i> gmail </a>
            <a href="#"><i class="fas fa-map-marker-alt"></i> gingoog, ph </a>
        </div>

        <div class="box">
            <h3>quick links</h3>
            <a href="#"><i class="fab fa-facebook-f"></i> facebook </a>
        </div>
    </div>

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