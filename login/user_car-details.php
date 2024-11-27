<?php
include_once 'connect.php';
$conn = connect();

session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If the user is not logged in, redirect to the login page
    header('Location: ../index.php');
    exit();
}


// Check if database connection is successful
if ($conn) {
    // Get the car ID from the URL
    $car_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Prepare and execute the query to fetch car details based on the ID
    $sql = "SELECT car_name, car_brand, overview, price, seat, fuel, model, image, accessories FROM vehicles WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $car_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        // Fetching image data
        $row = $result->fetch_assoc();
        $imagePath = $row['image'];
        $car_name = $row['car_name'];
        $car_brand = $row['car_brand'];
        $overview = $row['overview'];
        $price = $row['price'];
        $seat = $row['seat'];
        $fuel = $row['fuel'];
        $model = $row['model'];
        $accessories = explode(',', $row['accessories']);
    } else {
        echo "No details found for this car.";
    }
} else {
    echo "Database connection failed.";
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
        .gcash{
            width: 50px;
            margin-top: 2%;
            margin-bottom: 1%;
        }
    </style>
</head>
<body>
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

    <section class="vehicle-details-container">
        <!-- Left Box -->
        <div class="left-box">
            <img src="../admin/uploads/<?php echo $imagePath; ?>" alt="Car Image">
            <h2><?php echo $car_name; ?></h2>
            
            <!-- Hidden field to store car ID -->
            <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">

            <!-- Small Columns for Car Info -->
            <div class="car-info">
                <div class="info-box"><i class="fas fa-calendar-alt"></i><span><?php echo $model; ?><br>Year</span></div>
                <div class="info-box"><i class="fas fa-cogs"></i><span><?php echo $fuel; ?><br>Fuel</span></div>
                <div class="info-box"><i class="fas fa-user-plus"></i><span><?php echo $seat; ?><br>Seats</span></div>
            </div>

            <!-- Tabs Box -->
            <div class="tab-box">
                <div class="tabs">
                    <button class="tab-btn active" data-target="#overview">Overview</button>
                    <button class="tab-btn" data-target="#accessories">Accessories</button>
                </div>
                <div class="tab-content">
                    <div id="overview" class="tab-pane active">
                        <p><?php echo $overview; ?></p>
                    </div>
                    <div id="accessories" class="tab-pane">  
                        <table class="accessories-table">
                            <?php foreach ($accessories as $accessory): ?>
                                <tr>
                                    <td style="color: black"><?php echo htmlspecialchars($accessory); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <!-- Right Box -->
        <div class="right-box">
            <h3>Rent Price/12hrs</h3>
            <div class="price">₱<?php echo $price; ?>.00</div>
            <form action="review.php" method="POST" enctype="multipart/form-data">
                <!-- Hidden field for car ID -->
                <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">

                <!-- Other form fields -->
                <p>From Date:</p>
                <input type="date" name="fromDate" class="box" required>
                <p>Pick Up Time:</p>
                <input type="time" name="pickUpTime" id="pickUpTimeInput" class="box" required>
                <input type="hidden" name="formattedPickUpTime" id="formattedPickUpTime">
                <p>To Date:</p>
                <input type="date" name="toDate" class="box" required>
                <p>Message:</p>
                <textarea name="message" class="box" placeholder="Additional message or special requests"></textarea>
                <input type="submit" value="Review your Booking" class="btn">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabPanes.forEach(pane => pane.classList.remove('active'));

                    button.classList.add('active');
                    document.querySelector(button.getAttribute('data-target')).classList.add('active');
                });
            });
        });



        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent form submission

            const fromDate = document.getElementById('fromDate').value;
            const pickUpTime = document.getElementById('pickUpTime').value;
            const toDate = document.getElementById('toDate').value;
            const message = document.getElementById('message').value;

            // Redirect to the review page with the data
            window.location.href = `review.php?fromDate=${fromDate}&pickUpTime=${pickUpTime}&toDate=${toDate}&message=${message}`;
        });


        document.getElementById('pickUpTimeInput').addEventListener('change', function() {
            const timeValue = this.value;
            const formattedTime = formatTimeWithAMPM(timeValue);
            document.getElementById('formattedPickUpTime').value = formattedTime;
        });

        function formatTimeWithAMPM(timeString) {
            const [hour, minute] = timeString.split(':');
            let ampm = 'AM';
            let formattedHour = parseInt(hour);

            if (formattedHour >= 12) {
                ampm = 'PM';
                if (formattedHour > 12) {
                    formattedHour -= 12;
                }
            } else if (formattedHour === 0) {
                formattedHour = 12;
            }

            return `${formattedHour}:${minute} ${ampm}`;
        }
    </script>
</body>
</html>