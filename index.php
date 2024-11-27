<?php
include_once 'connect.php';
$conn = connect();

// Initialize a variable for the notification message
$notification_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form inputs
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $feedback = trim($_POST['feedback']);

    // Initialize an array to store errors
    $errors = [];

    // Validate required fields
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (empty($email)) {
        $errors[] = "Email is required.";
    }
    if (empty($feedback)) {
        $errors[] = "Feedback is required.";
    }

    // Get the current date in the Philippines
    date_default_timezone_set('Asia/Manila'); // Set timezone to the Philippines
    $current_date = date('Y-m-d'); // Format: YYYY-MM-DD HH:MM:SS

    // Check for errors before proceeding
    if (empty($errors)) {
        // Prepare the SQL statement for inserting feedback with the date
        $stmt = $conn->prepare("INSERT INTO review (Name, Email, review, date) VALUES (?, ?, ?, ?)");

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param('ssss', $name, $email, $feedback, $current_date);

            // Execute the statement
            if ($stmt->execute()) {
                // Successfully inserted feedback
                $notification_message = "Your feedback has been submitted successfully!";
                header("Location: index.php?notification=" . urlencode($notification_message));
                exit();
            } else {
                $errors[] = "Error inserting feedback: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $errors[] = "Error preparing statement: " . $conn->error;
        }
    }

    // Handle errors (if any)
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p>Error: $error</p>";
        }
    }
    
}
if (isset($_GET['reference_id'])) {
    $reference_id = $_GET['reference_id'];
} else {
    $reference_id = null; // Set as null if it's not in the URL
}
if (isset($_GET['name'])) {
    $name = $_GET['name'];
} else {
    $name = null; // Set as null if not provided
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    $email = null; // Set as null if not provided
}

// Include HTML for the feedback form and notification
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Car Rental System</title>
    <link rel="icon" type="image/x-icon" href="image/logo.jpg">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>
    <style>
        .car-unavailable{
            font-size: 14px;
            font-weight: 600;
            color: red;
        }

        /* General Modal Styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; 
            z-index: 100; /* Ensures it's on top */
            left: 0;
            top: 0;
            width: 100%;
            height: auto;
            overflow: auto; /* Enable scrolling if needed */
            transition: all 0.3s ease-in-out;
        }

        /* Modal Content Box */
        .modal-content {
            background-color: #fff;
            margin: 10% auto; /* Centered vertically and horizontally */
            padding: 20px;
            border-radius: 10px;
            width: 90%; /* Full width on smaller screens */
            max-width: 600px; /* Max width for large screens */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            animation: slideIn 0.5s ease-out;
        }

        /* Modal Animation */
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Modal Header */
        .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .modal-content .modal-header h2 {
            font-size: 20px;
            color: #333;
        }

        /* Modal Body */
        .modal-content .modal-body {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Modal Footer */
        .modal-content .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-content .modal-footer .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* Buttons */
        .modal-content .btn {
            background-color: #007bff; /* Primary blue color */
            color: white;
            font-weight: bold;
        }

        .modal-content .btn:hover {
            background-color: #0056b3;
        }

        .modal-content .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.5);
        }

        /* Secondary Button (Cancel) */
        #closeFeedbackModal {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ccc;
        }

        #closeFeedbackModal:hover {
            background-color: #e2e6ea;
            border-color: #bfc5cb;
        }

        /* Form Styles */
        .modal-content .form-group {
            margin-bottom: 15px;
        }

        .modal-content .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }

        .modal-content input[type="text"], .modal-content input[type="email"], .modal-content textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
            color: #555;
        }

        .modal-content input[type="text"]:focus, .modal-content input[type="email"]:focus, .modal-content textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        /* Textarea Style */
        .modal-content textarea {
            resize: vertical; /* Allows vertical resizing */
            height: 120px;
        }

        /* Responsive Design for Smaller Screens */
        @media screen and (max-width: 600px) {
            .modal-content {
                width: 95%; /* Smaller screen adjustments */
            }
        }

        /* Notification Modal Styles */
        #notificationModal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            top: 50%; /* Center vertically */
            left: 50%; /* Center horizontally */
            transform: translate(-50%, -50%); /* Offset by half its width and height */
            background-color: white; /* White background */
            border: 1px solid #ccc; /* Light grey border */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            padding: 20px; /* Space inside the modal */
            z-index: 1000; /* On top of other content */
            width: 300px; /* Fixed width */
            text-align: center; /* Center text inside */
        }

        /* Notification Message Styles */
        #notificationMessage {
            margin-bottom: 15px; /* Space below the message */
            font-size: 18px; /* Font size */
            color: #333; /* Dark grey color */
        }

        /* Button Styles */
        #notificationModal button {
            padding: 10px 15px; /* Space inside the button */
            background-color: #007bff; /* Bootstrap primary color */
            color: white; /* White text color */
            border: none; /* No border */
            border-radius: 4px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth transition */
        }

        #notificationModal button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        #error-message {
            color: red;
            font-size: 15px;
            display: none; /* Hidden by default */
            margin-top: 5px;
            font-weight: bold;
        }
        #error-message2 {
            color: red;
            font-size: 15px;
            display: none; /* Hidden by default */
            margin-top: 5px;
            font-weight: bold;
        }
        .footer .box-container{
            margin-left: 5%;
        }
        #calendar {
            width: 90%; /* Full width of its parent container */
            height: 250px; /* Set a fixed height */
            font-size: 15px;
        }
        .popup-content {
            max-height: 80vh; /* Set the maximum height (80% of the viewport height) */
            overflow-y: auto; /* Enable vertical scrollbar when content exceeds max-height */
            box-sizing: border-box; /* Ensures padding is included in the height calculation */
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
            <a href="index.php" style=" color: red; font-weight: 600;">home</a>
            <a href="vehicle.php">vehicles</a>
            <a href="about.php">about</a>
            <a href="contact.php">contact</a>
            <a href="manage.php">manage bookings</a>
        </nav>

        <div id="login-btn">
            
        </div>
    </header>

    <div class="login-form-container">

        <span class="fas fa-times" id="close-login-form"></span>

        <form action="">
            <h3>user login</h3>
            <input type="email" placeholder="email" class="box">
            <input type="password" placeholder="password" class="box">
            <p>forget your password <a href="#">click here</a></p>
            <input type="submit" value="login now" class="btn">
            <p>don't have an account <a href="#">create one</a></p>
            
        </form>
    </div>

    <section class="home" id="home">
        
        <div class="home-content">
            <h1>Choose your car</h1>
            <p class="subtitle">Rent now</p>
            <form action="step2.php" method="post" class="booking-form">
                <div class="form-rows">
                    <div class="form-groups">
                        <label for="pickup-date" class="form-label1">Pick-up Date</label>
                        <input type="date" id="pickup-date" class="form-inputs" name="pickup_date" required>
                    </div>
                    <div class="form-groups">
                        <label for="pickup-time" class="form-label1">Pick-up Time</label>
                        <input type="time" id="pickup-time" class="form-inputs" name="pickup_time" required>
                    </div>
            
                    <div class="form-groups">
                        <label for="day" class="form-label1">Duration Day(s)</label>
                        <input type="number" id="day" class="form-inputs" name="day" min="1" max="99" value="1" required>
                        <span id="error-message" style="background: white; color: red; display: none;">The limit for renting the car is 99 days.</span>
                    </div>
                </div>
                <div class="form-rows">
                    <div class="form-groups">
                        <label for="name" class="form-label1">Fullname</label>
                        <input type="text" id="name" class="form-input1" name="name" required>
                    </div>
                    <div class="form-groups">
                        <label for="email" class="form-label1">Email</label>
                        <input type="email" id="email" class="form-input1" name="email" required>
                    </div>
                    <div class="form-groups">
                        <label for="number" class="form-label1">Contact</label>
                        <input type="tel" id="contact" class="form-input1" name="contact" 
                            inputmode="numeric" pattern="\d{11}" title="Please enter a valid 11-digit phone number" required>
                    </div>
                </div>
                <div class="form-rows">
                    <button type="submit" class="form-button">rent a car</button>
                </div>
            </form>

        </div>
    </section>
    
    

    <!-- <section class="icons-container">

        <div class="icons">
            <i class="fas fa-car"></i>
            <div class="content">
                <h3>0</h3>
                <p>cars rented</p>
            </div>
        </div>

        <div class="icons">
            <i class="fas fa-users"></i>
            <div class="content">
                <h3>150+</h3>
                <p>review clients</p>
            </div>
        </div>

        <div class="icons">
            <i class="fas fa-car"></i>
            <div class="content">
                <h3>9</h3>
                <p>cars display</p>
            </div>
        </div>

    </section> -->

    <!-- Vehicles Section -->
    <?php
        include_once 'connect.php';
        $conn = connect();

        if ($conn) {
            $sql = "SELECT id, car_name, price, model, image, status, available, end_date FROM vehicles";
            $result = $conn->query($sql);
        } else {
            echo "Database connection failed.";
        }
    ?>
    <section class="featured" id="featured">
            <h1 class="heading"><span>featured</span> cars</h1>
            <div class="swiper featured-slider">
            <div class="swiper-wrapper">
            <?php
                if ($result && $result->num_rows > 0) {
                    $counter = 0; // Initialize a counter
                    $threshold1 = 5; // Set the number of cars to display in the first section
                    $threshold2 = 10; // Set the number of cars to display in the second section

                    $today = date('Y-m-d'); // Get today's date

                    // Loop through the result set and display cars in the first section
                    while ($row = $result->fetch_assoc()) {
                        $id = $row['id'];
                        $imagePath = $row['image'];
                        $car_name = $row['car_name'];
                        $price = $row['price'];
                        $model = $row['model'];
                        $status = $row['status'];
                        $start_date = $row['available'];
                        $end_date = $row['end_date'];

                        if ($counter < $threshold1) { // Display cars in the first section
                            ?>
                            <div class="swiper-slide box">
                                <img src="admin/uploads/<?php echo $imagePath; ?>" alt="">
                                <h3><?php echo $car_name; ?></h3>
                                <div class="price">₱<?php echo number_format($price, 2); ?>/day</div>
                                
                                <?php if ($status == 1): ?>
                                <!-- Car available -->
                                <button class="btn" onclick="openPopup(<?php echo $id; ?>, '<?php echo $car_name; ?>', <?php echo $price; ?>, '<?php echo $model; ?>', null, '<?php echo $imagePath; ?>')">Rent Now</button>
                            <?php else: ?>
                                <!-- Car unavailable -->
                                <p class="car-unavailable">Unavailable: from <?php echo date('F j, Y', strtotime($start_date)); ?> - to <?php echo date('F j, Y', strtotime($end_date)); ?></p>
                                <button class="btn" onclick="openPopup(<?php echo $id; ?>, '<?php echo $car_name; ?>', <?php echo $price; ?>, '<?php echo $model; ?>', '<?php echo $end_date; ?>', '<?php echo $imagePath; ?>')">Rent Now</button>
                            <?php endif; ?>
                            </div>
                            <?php
                        }

                        $counter++; // Increment the counter
                    }
                } else {
                    echo "No cars found.";
                }
                ?>
            </div>
                <div class="swiper-pagination"></div>
            </div>

            <div class="swiper featured-slider">
                <div class="swiper-wrapper">
                    <?php
                        if ($result && $result->num_rows > 0) {
                            mysqli_data_seek($result, 0); // Reset the result pointer
                            $counter = 0; // Reinitialize the counter

                            // Loop through the result set and display cars in the second section
                            while ($row = $result->fetch_assoc()) {
                                $id = $row['id'];
                                $imagePath = $row['image'];
                                $car_name = $row['car_name'];
                                $price = $row['price'];

                                if ($counter >= $threshold1 && $counter < $threshold2) { // Display cars in the second section
                                    ?>
                                    <div class="swiper-slide box">
                                        <img src="admin/uploads/<?php echo $imagePath; ?>" alt="">
                                        <h3><?php echo $car_name; ?></h3>
                                        <div class="price">₱<?php echo number_format($price, 2); ?>/day</div>
                                        <?php if ($status == 1): ?>
                                            <!-- Car available -->
                                            <button class="btn" onclick="openPopup(<?php echo $id; ?>, '<?php echo $car_name; ?>', <?php echo $price; ?>, '<?php echo $model; ?>', null, '<?php echo $imagePath; ?>')">Rent Now</button>
                                        <?php else: ?>
                                            <!-- Car unavailable -->
                                            <p class="car-unavailable">Unavailable: from <?php echo date('F j, Y', strtotime($start_date)); ?> - to <?php echo date('F j, Y', strtotime($end_date)); ?></p>
                                            <button class="btn" onclick="openPopup(<?php echo $id; ?>, '<?php echo $car_name; ?>', <?php echo $price; ?>, '<?php echo $model; ?>', '<?php echo $end_date; ?>', '<?php echo $imagePath; ?>')">Rent Now</button>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                }

                                $counter++; // Increment the counter
                            }
                        }
                    ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>


            <div class="swiper featured-slider">
                <div class="swiper-wrapper">
                    <?php
                        if ($result && $result->num_rows > 0) {
                            mysqli_data_seek($result, 0); // Reset the result pointer
                            $counter = 0; // Reinitialize the counter

                            // Loop through the result set and display cars in the third section
                            while ($row = $result->fetch_assoc()) {
                                $id = $row['id'];
                                $imagePath = $row['image'];
                                $car_name = $row['car_name'];
                                $price = $row['price'];

                                if ($counter >= $threshold2) { // Display cars in the third section
                                    ?>
                                    <div class="swiper-slide box">
                                        <img src="admin/uploads/<?php echo $imagePath; ?>" alt="">
                                        <h3><?php echo $car_name; ?></h3>
                                        <div class="price">₱<?php echo number_format($price, 2); ?>/day</div>
                                        <?php if ($status == 1): ?>
                                            <!-- Car available -->
                                            <button class="btn" onclick="openPopup(<?php echo $id; ?>, '<?php echo $car_name; ?>', <?php echo $price; ?>, '<?php echo $model; ?>', null, '<?php echo $imagePath; ?>')">Rent Now</button>
                                        <?php else: ?>
                                            <!-- Car unavailable -->
                                            <p class="car-unavailable">Unavailable: from <?php echo date('F j, Y', strtotime($start_date)); ?> - to <?php echo date('F j, Y', strtotime($end_date)); ?></p>
                                            <button class="btn" onclick="openPopup(<?php echo $id; ?>, '<?php echo $car_name; ?>', <?php echo $price; ?>, '<?php echo $model; ?>', '<?php echo $end_date; ?>', '<?php echo $imagePath; ?>')">Rent Now</button>
                                        <?php endif; ?>
                                    </div>
                                    <?php
                                }

                                $counter++; // Increment the counter
                            }
                        }
                    ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </section>

    <section class="services" id="services">
        <h1 class="heading"> our <span>services</span></h1>
        <div class="box-container">
            <div class="box">
                <i class="fas fa-car"></i>
                <h3>car rent</h3>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Eum qui mollitia minus amet repellendus fuga id, officia non esse, reprehenderit cupiditate, molestiae soluta repellat facere aperiam atque quas ullam ad!</p>
                <a href="#" class="btn">read more</a>
            </div>
        </div>
    </section>

    <?php
        // Fetch feedback data
        $sql = "SELECT Name, Email, review, date FROM review ORDER BY date DESC"; // Adjust as needed
        $result = $conn->query($sql);

        // Check if the query was successful
        if ($result === false) {
            // Handle the error
            echo "Error: " . $conn->error; // Display the error message
        } else {
            ?>
            <section class="reviews" id="reviews">
                <h1 class="heading">client's <span>review</span></h1>
                <div class="swiper reviews-slider">
                    <div class="swiper-wrapper">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="swiper-slide box">
                                    <img src="image/user.png" alt="Client's Review">
                                    <div class="content">
                                        <p><?php echo htmlspecialchars($row['review']); ?></p> <!-- Changed to 'review' -->
                                        <h3><?php echo htmlspecialchars($row['Name']); ?></h3>
                                        <h4 style="font-size: 13px;"><?php echo htmlspecialchars($row['Email']); ?></h4>
                                        <h5 style="font-size: 13px;"><?php
                                            // Check if the date field is available
                                            if (isset($row['date'])) {
                                                // Create a DateTime object from the date string
                                                $date = new DateTime($row['date']);
                                                // Format the date to display the month name, day, and year
                                                echo htmlspecialchars($date->format('F j, Y')); // Example: "October 13, 2024"
                                            }
                                        ?></h5>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="swiper-slide box">
                                <div class="content">
                                    <p>No reviews available.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </section>
            <?php
        }

        ?>

        <div id="reservationSuccessModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Reservation Successful!</h2>
                </div>
                <div class="modal-body">
                    <p>Your reservation has been successfully completed.</p>
                    <p>Check your email for the information of your booking.</p>
                    <?php if (isset($reference_id) && !empty($reference_id)) { ?>
                        <p><strong>Your Reference ID: <?php echo htmlspecialchars($reference_id); ?></strong></p>
                        <p>Note: Used your Reference ID to Access your booking details in the page MANAGE BOOKING.</p>
                    <?php } else { ?>
                        <p><strong>Reference ID not found.</strong></p>
                    <?php } ?>
                    <p>Would you like to provide feedback about your experience?</p>
                </div>
                <div class="modal-footer">
                    <button id="feedbackYes" class="btn">Yes</button>
                    <button id="feedbackNo" class="btn">No, thanks</button>
                </div>
            </div>
        </div>


        <!-- Notification Modal -->
        <div id="notificationModal" >
            <p id="notificationMessage"></p>
            <button onclick="closeModal()">OK</button>
        </div>
        <!-- Booking Form -->
        <div id="popupForm" class="popup-overlay" style="display:none;">
            <div class="popup-content">
                <span style="font-size: 50px;" class="close-btn" onclick="closePopup()">&times;</span>
                <br>
                <br>
                <br>
                <form action="step3-2.php" method="post" class="booking-form">
                    <input type="hidden" id="car_id" name="car_id" value="">
                    <input type="hidden" id="car_name" name="car_name" value="">
                    <input type="hidden" id="price" name="price" value="">
                    <input type="hidden" id="model" name="model" value="">
                    <input type="hidden" id="car_image" name="car_image" value="">

                    <div id="calendar" class="form-groups" style="margin-top: -20px;"></div>

                    <div class="form-rows">
                        <div class="form-groups">
                            <label style="color: black;" for="pickup-date" class="form-label1">Pick-up Date</label>
                            <input type="date" id="pickup-date2" class="form-inputs" name="pickup_date" required>
                        </div>
                        <div class="form-groups">
                            <label style="color: black;" for="pickup-time" class="form-label1">Pick-up Time</label>
                            <input type="time" id="pickup-time" class="form-inputs" name="pickup_time" required>
                        </div>
                        <div class="form-groups">
                            <label style="color: black;" for="day" class="form-label1">Duration Day(s)</label>
                            <input type="number" id="day" class="form-inputs" name="day" min="1" max="99" value="1" required>
                            <span id="error-message2" style="color: red; display: none;">The limit for renting the car is 99 days.</span>
                        </div>
                    </div>
                    <div class="form-rows">
                        <div class="form-groups">
                            <label style="color: black;" for="name" class="form-label1">Full Name</label>
                            <input type="text" id="name" class="form-input1" name="name" required>
                        </div>
                        <div class="form-groups">
                            <label style="color: black;" for="email" class="form-label1">Email</label>
                            <input type="email" id="email" class="form-input1" name="email" required>
                        </div>
                        <div class="form-groups">
                            <label style="color: black;" for="contact" class="form-label1">Contact No.</label>
                            <input type="tel" id="contact" class="form-input1" name="contact" 
                            inputmode="numeric" pattern="\d{11}" title="Please enter a valid 11-digit phone number" required>
                        </div>
                    </div>
                    <button type="submit" class="btn">Submit</button>
                </form>
            </div>
        </div>



    <section class="footer">
        <div class="box-container">
            <div class="box">
                <h3>Quick Links</h3>
                <a href="reservation.php"><i class="fas fa-arrow-right"></i> reservation</a>
                <a href="index.php"><i class="fas fa-arrow-right"></i> Home </a>
                <a href="vehicle.php"><i class="fas fa-arrow-right"></i> Vehicles </a>
            </div>
            <div class="box">
                <h3>Quick Links</h3>
                <a href="about.php"><i class="fas fa-arrow-right"></i> About Us </a>
                <a href="contact.php"><i class="fas fa-arrow-right"></i> Contact </a>
                <a href="manage.php"><i class="fas fa-arrow-right"></i> manage booking </a>
            </div>

            <div class="box">
                <h3>Contacts</h3>
                <a href="#"><i class="fas fa-mobile"></i> 09353540437 </a>
                <a href="#"><i class="fas fa-envelope"></i> chadoyve@gmail.com </a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> Motomull Gingoog, Gingoog, 9014 Misamis Oriental </a>
            </div>

            <div class="box">
                <h3>Social Media</h3>
                <a href="#"><i class="fab fa-facebook-f"></i> facebook </a>
            </div>
        </div>

        <div class="credit"> create by me web designer | all rights reserved!</div>

    </section>

    <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Check if the reservation was successful by looking at the query parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('reservation') === 'success') {
                // Show the reservation success modal
                document.getElementById('reservationSuccessModal').style.display = 'flex';
            }

            // Handle the 'Yes' button to open the feedback modal
            document.getElementById('feedbackYes').addEventListener('click', function() {
                document.getElementById('reservationSuccessModal').style.display = 'none';
                document.getElementById('feedbackModal').style.display = 'flex';
            });

            // Handle the 'No' button to close the success modal
            document.getElementById('feedbackNo').addEventListener('click', function() {
                document.getElementById('reservationSuccessModal').style.display = 'none';
            });

            // Handle closing the feedback modal
            document.getElementById('closeFeedbackModal').addEventListener('click', function(event) {
                event.preventDefault(); // Prevent form submission
                document.getElementById('feedbackModal').style.display = 'none';
            });
        });
        function showNotification(message) {
            document.getElementById('notificationMessage').innerText = message;
            document.getElementById('notificationModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('notificationModal').style.display = 'none';
            document.getElementById('feedbackForm').style.display = 'block'; // Show feedback form when closing modal
        }

        <?php if (isset($_GET['notification'])): ?>
            window.onload = function() {
                showNotification("<?php echo addslashes($_GET['notification']); ?>");
            };
        <?php endif; ?>

        var today = new Date().toISOString().split('T')[0];
        // Set the min attribute of the date input to today's date
        document.getElementById('pickup-date').setAttribute('min', today);


        let calendarInitialized = false; // Flag to track if the calendar has been initialized

function openPopup(carId, carName, price, model, endDate, carImage) {
    // Set the values of the hidden inputs for the booking form
    document.getElementById('car_id').value = carId;
    document.getElementById('car_name').value = carName;
    document.getElementById('price').value = price;
    document.getElementById('model').value = model;
    document.getElementById('car_image').value = carImage;

    // Show the popup form
    document.getElementById('popupForm').style.display = 'block';
    // Disable dates after the end date for the unavailable car
    const pickupDateInput = document.getElementById('pickup-date');
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Set to the start of the day

        if (endDate) {
            const endDateObj = new Date(endDate);
            const minDate = new Date(endDateObj);
            minDate.setDate(endDateObj.getDate() + 1); // Pick-up date must be the day after the end date

            // If the end date is less than today, set minimum to today
            if (endDateObj < today) {
                pickupDateInput.setAttribute('min', today.toISOString().split('T')[0]);
            } else {
                pickupDateInput.setAttribute('min', minDate.toISOString().split('T')[0]);
            }
        } else {
            // If the car is available, set the minimum pickup date to today
            pickupDateInput.setAttribute('min', today.toISOString().split('T')[0]);
        }

    // Initialize FullCalendar only once
    if (!calendarInitialized) {
        $('#calendar').fullCalendar({
            events: [
                {
                    title: 'Unavailable',
                    start: moment().format('YYYY-MM-DD'),
                    end: endDate,
                    color: 'red',
                    rendering: 'background'
                }
            ],
            editable: false,
            droppable: false,
            selectable: false,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            }
        });
        calendarInitialized = true;
    } else {
        // Update the calendar with new event data without re-initializing
        $('#calendar').fullCalendar('removeEvents'); // Remove previous events
        $('#calendar').fullCalendar('addEventSource', [
            {
                title: 'Unavailable',
                start: moment().format('YYYY-MM-DD'),
                end: endDate,
                color: 'red',
                rendering: 'background'
            }
        ]);
    }
    popupContentElement.insertAdjacentHTML('afterbegin', popupContent);

}


    const dayInput = document.getElementById('day');
    const errorMessage = document.getElementById('error-message');

    dayInput.addEventListener('input', function() {
        if (dayInput.value > 99) {
        errorMessage.style.display = 'inline'; // Show the error message
        dayInput.value = 99; // Automatically set the value to 7
        } else {
        errorMessage.style.display = 'none'; // Hide the error message
        }
    });

    
    const errorMessage2 = document.getElementById('error-message2');

    dayInput.addEventListener('input', function () {
        if (dayInput.value > 99) {
            errorMessage2.style.display = 'inline'; // Show the error message
            dayInput.value = 99; // Automatically set the value to 7
        } else {
            errorMessage2.style.display = 'none'; // Hide the error message
        }
    });

    function validatePhoneNumber() {
    var phoneNumber = document.getElementById("contact").value;
    
    // Regular expression to match a 10-digit phone number
    var regex = /^\d{11}$/;
    
    if (!regex.test(phoneNumber)) {
        alert("Please enter a valid 11-digit phone number.");
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}

    </script>


</body>
</html>
