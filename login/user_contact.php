<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: ../index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chadoyven Car Rental</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../image/logo.jpg">
    <link rel="stylesheet" href="css/user_style.css">

    <style>
        .user-pic{
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            margin-left: 30px;
        }

        .user-info img{
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <!-- <div class="message">
        <img src="image/m.png" alt="">
    </div> -->

    <!-- Header -->
    <header class="header">
        <div id="menu-btn" class="fas fa-bars"></div>
        <a class="logo">
            <img src="image/logo.jpg" alt="Car Rental Logo">
            <p style="color:black; ">Chadoyven Car Rental</p>
        </a>
        <nav class="navbar">
            <a href="user_index.php">home</a>
            <a href="user_vehicle.php">vehicles</a>
            <a href="user_about.php">about</a>
            <a href="#contact">contact</a>
        </nav>
        <img src="../uploads/<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" class="user-pic" onclick="toggleMenu()">

            
            <div class="sub-menu-wrap" id="subMenu">
                <div class="sub-menu">
                    <div class="user-info">
                        <img src="../uploads/<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" alt="">
                        <h2><?php echo htmlspecialchars($_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname']); ?></h2>
                    </div>
                    <hr>

                    <a href="profile.php" class="sub-menu-link">
                        <img src="image/user.png" alt="">
                        <p>Edit Profile</p>
                        <span>></span>
                    </a>

                    <a href="" class="sub-menu-link">
                        <img src="image/booking.png" alt="">
                        <p>My Booking</p>
                        <span>></span>
                    </a>

                    <a href="../index.php" class="sub-menu-link">
                        <img src="image/logout.png" alt="">
                        <p>Logout</p>
                        <span>></span>
                    </a>
                </div>
            </div>
    </header>

    <section class="contact" id="contact">
        <h1 class="heading"><span>contact </span>us</h1>
        <div class="row">
            <iframe class="map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31541.188529247756!2d125.09431299076111!3d8.819047440031277!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33002f4fddcfe2cd%3A0x1da96807bd1ae653!2sCHADOYVEN%20RENT%20A%20CAR%20BUSINESS!5e0!3m2!1sen!2sph!4v1723697336201!5m2!1sen!2sph" allowfullscreen="" loading="lazy"></iframe>
            <form action="">
                <h3>get in touch</h3>
                <input type="text" placeholder="name" class="box">
                <input type="email" placeholder="email" class="box">
                <input type="number" placeholder="number" class="box">
                <textarea placeholder="message" class="box" cols="30" rows="10"></textarea>
                <input type="submit" value="send message" class="btn">
            </form>
        </div>

    </section>

    <section class="footer">
        <div class="box-container">
            <div class="box">
                <h3>Quick Links</h3>
                <a href="user_index.php"><i class="fas fa-arrow-right"></i> Home </a>
                <a href="user_vehicle.php"><i class="fas fa-arrow-right"></i> Vehicles </a>
                <a href="user_about.php"><i class="fas fa-arrow-right"></i> About Us </a>
                <a href="user_contact.php"><i class="fas fa-arrow-right"></i> Contact </a>
            </div>

            <div class="box">
                <h3>Contacts</h3>
                <a href="#"><i class="fas fa-phone"></i> +64864864 </a>
                <a href="#"><i class="fas fa-mobile"></i> +64864864 </a>
                <a href="#"><i class="fas fa-envelope"></i> gmail </a>
                <a href="#"><i class="fas fa-map-marker-alt"></i> gingoog, ph </a>
            </div>

            <div class="box">
                <h3>Social Media</h3>
                <a href="#"><i class="fab fa-facebook-f"></i> facebook </a>
            </div>
        </div>

        <div class="credit"> create by me web designer | all rights reserved!</div>

    </section>











    <script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
    <script src="js/user_script.js"></script>
</body>
</html>
