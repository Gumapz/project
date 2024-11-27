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
    
    <header class="header">

        <div id="menu-btn" class="fas fa-bars"></div>

        <a class="logo">
            <img src="image/logo.jpg" alt="Car Rental Logo">
            <p style="color:black; ">Chadoyven Car Rental</p>
        </a>
        <nav class="navbar">
            <a href="#home">HOME</a>
            <a href="user_vehicle.php">VEHICLES</a>
            <a href="user_about.php">ABOUT</a>
            <a href="user_contact.php">CONTACT</a>

            
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


    <section class="home" id="home">
        <h1 class="home-parallax" data-speed="-2">Find your car</h1>
        <img class="home-parallax" data-speed="5" src="image/car.png" alt="">
        <a href="#" class="btn home-parallax" data-speed="7 "> explore cars</a>

    </section>

    <section class="icons-container">

        <div class="icons">
            <i class="fas fa-car"></i>
            <div class="content">
                <h3>0</h3>
                <p>Available Car</p>
            </div>
        </div>

        <div class="icons">
            <i class="fas fa-car"></i>
            <div class="content">
                <h3>0</h3>
                <p>Cars Rented</p>
            </div>
        </div>

        <div class="icons">
            <i class="fas fa-users"></i>
            <div class="content">
                <h3>0</h3>
                <p>clients Reviews</p>
            </div>
        </div>

    </section>

    <section class="vehicles" id="vehicles">
        <h1 class="heading"> our <span> vehicles</span> </h1>
        <div class="swiper vehicles-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide box">
                    <img src="image/car1.jpg" alt="">
                    <div class="content">
                        <h3>new model</h3>
                        <div class="price"><span>Price: </span> 10000/-</div>
                        <p>
                            new
                            <span class="fas fa-circle"></span> 2021
                            <span class="fas fa-circle"></span> automatic
                            <span class="fas fa-circle"></span> petrol
                            <span class="fas fa-circle"></span> 183mph
                        </p>
                        <a href="#" class="btn">check out</a>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/car2.jpg" alt="">
                    <div class="content">
                        <h3>new model</h3>
                        <div class="price"><span>Price: </span> 10000/-</div>
                        <p>
                            new
                            <span class="fas fa-circle"></span> 2021
                            <span class="fas fa-circle"></span> automatic
                            <span class="fas fa-circle"></span> petrol
                            <span class="fas fa-circle"></span> 183mph
                        </p>
                        <a href="#" class="btn">check out</a>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/car3.jpg" alt="">
                    <div class="content">
                        <h3>new model</h3>
                        <div class="price"><span>Price: </span> 10000/-</div>
                        <p>
                            new
                            <span class="fas fa-circle"></span> 2021
                            <span class="fas fa-circle"></span> automatic
                            <span class="fas fa-circle"></span> petrol
                            <span class="fas fa-circle"></span> 183mph
                        </p>
                        <a href="#" class="btn">check out</a>
                    </div>
                </div>
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
                <p>We are your leading car companions here in Gingoog City. We offer you a reliable, comfortable, and affordable cars. Make your trips better by renting well-maintained cars at a lowest rate.</p>
                <a href="#" class="btn">read more</a>
            </div>
        </div>
    </section>


    <!-- <section class="newsletter">
        <h3>subscribe for latest updates</h3>
        <p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Corporis, veniam incidunt? Perferendis, et qui facere ad tempora maxime assumenda reiciendis ut totam dolore architecto minus, iusto sit? Fuga, impedit ipsa!</p>
        <form action="">
            <input type="email" placeholder="enter your email" name="" id=" ">
            <input type="submit" class="subscribe" name="" id="">
        </form>
    </section> -->

    <section class="reviews" id="reviews">
        <h1 class="heading">client's <span>review</span></h1>
        <div class="swiper reviews-slider">
            <div class="swiper-wrapper">
                <div class="swiper-slide box">
                    <img src="image/car.png" alt="">
                    <div class="content">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officiis deserunt placeat aspernatur iusto vero iure officia dolorem, veniam corrupti voluptatum quis accusamus expedita quia temporibus, animi adipisci dicta corporis eligendi?</p>
                        <h3>robert</h3>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/car.png" alt="">
                    <div class="content">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officiis deserunt placeat aspernatur iusto vero iure officia dolorem, veniam corrupti voluptatum quis accusamus expedita quia temporibus, animi adipisci dicta corporis eligendi?</p>
                        <h3>robert</h3>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/car.png" alt="">
                    <div class="content">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officiis deserunt placeat aspernatur iusto vero iure officia dolorem, veniam corrupti voluptatum quis accusamus expedita quia temporibus, animi adipisci dicta corporis eligendi?</p>
                        <h3>robert</h3>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide box">
                    <img src="image/car.png" alt="">
                    <div class="content">
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officiis deserunt placeat aspernatur iusto vero iure officia dolorem, veniam corrupti voluptatum quis accusamus expedita quia temporibus, animi adipisci dicta corporis eligendi?</p>
                        <h3>robert</h3>
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
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
