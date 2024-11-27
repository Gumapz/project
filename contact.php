<?php
include_once 'connect.php';
$conn = connect();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));
    
    // Set the default timezone to the Philippines
    date_default_timezone_set('Asia/Manila');
    
    // Get the current date in the Philippines
    $submission_date = date('Y-m-d'); // Format: YYYY-MM-DD

    // Prepare an SQL statement
    $sql = "INSERT INTO review (Name, Email, review, date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("ssss", $name, $email, $message, $submission_date);

    // Execute the statement
    if ($stmt->execute()) {
        $alertMessage = "Your feedback has been submitted successfully!";
		$alertType = "success";
    } else {
        $alertMessage = "Error adding feedback.";
		$alertType = "error";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
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
        .alert {
			padding: 15px;
			margin-bottom: 20px;
			border: 1px solid transparent;
			border-radius: 4px;
			position: relative;
		}

		.alert-success {
			color: #155724;
			background-color: #d4edda;
			border-color: #c3e6cb;
		}

		.alert-error {
			color: #721c24;
			background-color: #f8d7da;
			border-color: #f5c6cb;
		}

		.alert-dismissible .close {
			position: absolute;
			top: 0;
			right: 10px;
			color: inherit;
			border: none;
			background: none;
			font-size: 20px;
			cursor: pointer;
		}
        .footer .box-container{
            margin-left: 5%;
        }
        /* Contact Section Styles */
.con {
    background-color: #f8f9fa; /* Light background for contrast */
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    padding: 20px 30px; /* Inner padding for spacing */
    max-width: 500px; /* Width for better layout on larger screens */
    margin: 20px auto; /* Center alignment */
    text-align: left; /* Left-align text */
    font-family: Arial, sans-serif; /* Font choice */
    color: #2C3E50; /* Text color for readability */
}

.con p {
    margin: 10px 0; /* Spacing between lines */
    font-size: 1.5rem; /* Slightly larger font size */
}

.con p strong {
    color: #3498DB; /* Color for labels to make them stand out */
    font-weight: bold;
}

/* Responsive design for smaller screens */
@media (max-width: 600px) {
    .con {
        padding: 15px; /* Reduce padding on smaller screens */
        font-size: 1rem; /* Adjust font size */
    }
}

    </style>
</head>
<body>

    <!-- Header -->
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
            <a href="contact.php" style=" color: red; font-weight: 600;">contact</a>
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
            <h1>contact us</h1>
        </div>
        
    </section>

   

    <section class="contact" id="contact">
        <div class="row">
            <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31541.188529247756!2d125.09431299076111!3d8.819047440031277!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33002f4fddcfe2cd%3A0x1da96807bd1ae653!2sCHADOYVEN%20RENT%20A%20CAR%20BUSINESS!5e0!3m2!1sen!2sph!4v1723697336201!5m2!1sen!2sph" allowfullscreen="" loading="lazy"></iframe>
                <div class="con">
                <p><strong>Phone: </strong>09353540437</p>
                <p><strong>Email: </strong> chadoyven@gmail.com</p>
                <p><strong>Address: </strong> Motomull Gingoog, Gingoog, 9014 Misamis Oriental</p>
                <form action="contact.php" method="POST">
                    <h3>Send Feedback</h3>
                    <?php if (!empty($alertMessage)): ?>
                        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible">
                            <?php echo $alertMessage; ?>
                            <button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
                        </div>
                    <?php endif; ?>
                    <input type="text" name="name" placeholder="name" class="box" required>
                    <input type="email" name="email" placeholder="email" class="box" required>
                    <textarea name="message" placeholder="message" class="box" cols="30" rows="10" required></textarea>
                    <input type="submit" value="send message" class="btn">
                </form>
                </div>
                

        </div>

    </section>

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
</body>
</html>
