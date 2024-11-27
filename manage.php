<?php
session_start(); // Start the session
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
        .warning {
        color: red;
        font-weight: bold;
        font-size:20px;
        margin: 20px 0;
    }
    .footer .box-container{
            margin-left: 5%;
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
            <a href="manage.php" style=" color: red; font-weight: 600;">manage bookings</a>
        </nav>

        <div id="login-btn">
            
        </div>
    </header>

    <section class="home1">
        <div class="image-container">
            <img src="image/bg1.1.jpg" alt="Car Rental" />
        </div>
        <div class="home-content">
            <h1>manage your booking</h1>
        </div>
        
    </section>

    <section class="manage-booking-section">
        <h3>Use your email address and booking ID to view reservation details.</h3>
        <br><br>
        <!-- Check for warning message -->
        <?php if (isset($_SESSION['warning'])): ?>
            <div class="warning">
                <?php
                    echo $_SESSION['warning'];
                    unset($_SESSION['warning']); // Clear the message after displaying
                ?>
            </div>
        <?php endif; ?>
    
        <!-- Email Address Input -->
        <div class="form-grou">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" class="form-input" placeholder="Enter your email address">
        </div>
    
        <!-- Booking Reference ID Input -->
        <div class="form-grou">
            <label for="booking-id" class="form-label">Booking Reference ID</label>
            <input type="text" id="booking-id" class="form-input" placeholder="Enter your booking reference ID">
        </div>
    
        <!-- View Booking Button -->
        <a href="step5.php?email=" id="viewBookingButton">
            <button class="btn view-booking-btn">Manage</button>
        </a>
    </section>
    

    
</body>

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
    document.getElementById('viewBookingButton').onclick = function() {
        const email = document.getElementById('email').value;
        const bookingId = document.getElementById('booking-id').value;
        this.href = `step5.php?email=${encodeURIComponent(email)}&booking_id=${encodeURIComponent(bookingId)}`;
    };
</script>

</body>
</html>