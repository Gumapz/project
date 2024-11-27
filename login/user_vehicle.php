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
            <a href="user_contact.php">contact</a>
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

    <?php
        include_once 'connect.php';
        $conn = connect();

        if ($conn) {
            $sql = "SELECT id, car_name, price, image FROM vehicles";
            $result = $conn->query($sql);
        } else {
            echo "Database connection failed.";
        }
    ?>

        <!-- Vehicles Section -->
        <section class="featured" id="featured">
            <h1 class="heading"><span>featured</span> cars</h1>
            <div class="swiper featured-slider">
            <div class="swiper-wrapper">
                <?php
                    if ($result && $result->num_rows > 0) {
                        $counter = 0; // Initialize a counter
                        $threshold1 = 5; // Set the number of cars to display in the first section
                        $threshold2 = 10; // Set the number of cars to display in the second section

                        // Loop through the result set and display cars in the first section
                        while ($row = $result->fetch_assoc()) {
                            $id = $row['id'];
                            $imagePath = $row['image'];
                            $car_name = $row['car_name'];
                            $price = $row['price'];

                            if ($counter < $threshold1) { // Display cars in the first section
                                ?>
                                <div class="swiper-slide box">
                                    <img src="../admin/uploads/<?php echo $imagePath; ?>" alt="">
                                    <h3><?php echo $car_name; ?></h3>
                                    <div class="price">₱<?php echo $price; ?></div>
                                    <a href="user_car-details.php?id=<?php echo $id; ?>" class="btn">check out</a>
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
                <center>
                    <h1 style="font-size: 50px;">Choose Your Car</h1>
                </center>
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
                                        <img src="../admin/uploads/<?php echo $imagePath; ?>" alt="">
                                        <h3><?php echo $car_name; ?></h3>
                                        <div class="price">₱<?php echo $price; ?></div>
                                        <a href="user_car-details.php?id=<?php echo $id; ?>" class="btn">check out</a>
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
                <center>
                    <h1 style="font-size: 50px;">Choose Your Car</h1>
                </center>
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
                                        <img src="../admin/uploads/<?php echo $imagePath; ?>" alt="">
                                        <h3><?php echo $car_name; ?></h3>
                                        <div class="price">₱<?php echo $price; ?></div>
                                        <a href="user_car-details.php?id=<?php echo $id; ?>" class="btn">check out</a>
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


    <!-- Footer -->
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

    <!-- Include Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <script src="js/user_script.js"></script>
    <script>
        // Initialize Swiper for featured-slider
        var swiper = new Swiper(".featured-slider", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop:true,
            grabCursor:true,
            centeredSlides:true,
            autoplay: {
                delay: 9500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            breakpoints: {
                640: {
                    slidesPerView: 1,
                },
                760: {
                    slidesPerView: 2,
                },
                991: {
                    slidesPerView: 3,
                },
            },
        });
    </script>
</body>
</html>
