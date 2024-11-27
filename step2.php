<?php
session_start();
// Check if the form data has been posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the data from the form submission
    $pickup_date = $_POST['pickup_date'];
    $pickup_time = $_POST['pickup_time'];
    $day = $_POST['day']; // This is the rental duration (in days) input by the user
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    // Combine pickup date and time into a single DateTime object
    $pickup_datetime = new DateTime("$pickup_date $pickup_time");

    // Calculate the return (end) date by adding the rental duration (days) to the pickup date
    $return_datetime = clone $pickup_datetime; // Clone to avoid modifying the original pickup date
    $return_datetime->modify("+$day days");

    // Format pickup and return dates and times
    $pickup_date_formatted = $pickup_datetime->format('Y-m-d'); // Format as YYYY-MM-DD
    $pickup_time_formatted = $pickup_datetime->format('h:i A'); // Format as 12-hour with AM/PM
    $return_date_formatted = $return_datetime->format('Y-m-d'); // Format as YYYY-MM-DD
    $return_time_formatted = $return_datetime->format('h:i A'); // Format as 12-hour with AM/PM

    // Store data in the session
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['contact'] = $contact;
    $_SESSION['pickup_date'] = $pickup_date_formatted;
    $_SESSION['pickup_time'] = $pickup_time_formatted;
    $_SESSION['return_date'] = $return_date_formatted;
    $_SESSION['return_time'] = $return_time_formatted; // Optional: you can store the time
    $_SESSION['day'] = $day; // Store the duration in days
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
        .booking-details-box {
            padding: 2rem; /* Internal padding */
            width: 25%; /* Adjust this width as needed */
            height: 300px;
            max-height: 300px; /* Maximum height for the box */
            background-color: #fff;
            border: 2px solid #ccc; /* Visible border */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Shadow for depth */
            border-radius: 8px;
            margin-top: 2rem; /* Space between the dropdowns and the box */
            margin-left: 12rem; /* Align the box to the left */
            overflow-y: auto; /* Enable vertical scrolling if content exceeds max height */
        }
        .car-unavailable{
            font-size: 14px;
            font-weight: 600;
            color: red;
        }
        .car-details-box {
    padding: 2rem;
    font-size: 1.6rem; /* Base font size */
    max-width: 750px;
}
        /* General responsive adjustments for .step2-section and its contents */
@media (max-width: 991px) {
    /* Reduce padding in the main section for smaller screens */
    .step2-section {
        padding: 2rem;
    }

    /* Stack .step2-header elements vertically */
    .step2-header {
        flex-direction: column;
        gap: 1rem;
    }

    /* Align step text and dropdown button for smaller screens */
    .left-content .step-text h2 {
        font-size: 2rem;
        text-align: center;
    }

    .dropdown1 .dropbtn1, .dropdown2 .dropbtn2 {
        font-size: 1.5rem;
        padding: 0.5rem;
    }

    /* Adjust .content-container layout */
    .content-container {
        flex-direction: column;
        gap: 2rem;
    }

    /* Full width for .booking-details-box on smaller screens */
    .booking-details-box {
        width: 100%;
        margin: 0 auto;
    }

    /* Car list adjustments */
    .car-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .car-details-box {
        padding: 1.5rem;
        font-size: 1.4rem; /* Slightly smaller font size */
        max-width: 800px; /* Reduced width */
    }
}

/* Further adjustments for screens smaller than 768px */
@media (max-width: 768px) {
    .car-details-box {
        padding: 1rem;
        font-size: 1.2rem;
        max-width: 450px; /* Full width on smaller screens */
    }

    /* Car image adjustments */
    .car-image img {
        width: 50%; /* Ensure the image takes full width */
        height: auto; /* Maintain aspect ratio */
    }

    /* Center-align car icons */
    .car-icons1 {
        justify-content: center;
        gap: 1rem;
    }

    .car-icon-group1 i {
        font-size: 1.5rem;
    }

    /* Stack filter and sort dropdowns */
    .filter-sort-container {
        flex-direction: column;
        gap: 1rem;
        align-items: center;
    }

    /* Font size adjustments */
    .car-price, .car-name, .car-icon-text {
        font-size: 1.5rem;
    }

    .car-description-container h3, .booking-details h3 {
        font-size: 1.8rem;
    }
}

/* Small screens (up to 450px) */
@media (max-width: 450px) {
    /* Smaller font sizes for buttons and titles */
    .dropbtn1, .dropbtn2 {
        font-size: 1.3rem;
        padding: 0.4rem;
    }

    /* Reduce padding in .step2-section and container elements */
    .step2-section {
        padding: 1.5rem;
    }

    .content-container, .booking-details-box, .car-details-container {
        padding: 1rem;
    }

    /* Adjust font sizes for text and elements in .booking-details */
    .booking-details p, .car-price, .car-description-container p {
        font-size: 1.2rem;
    }

    /* Stack the left content and dropdown menus vertically */
    .step2-header .left-content, .filter-sort-container {
        align-items: center;
        text-align: center;
    }

    /* Make buttons and dropdowns full-width for better tap targets */
    .dropdown1 .dropbtn1, .dropdown2 .dropbtn2, .book-now-btn {
        width: 100%;
        font-size: 1.2rem;
        padding: 0.6rem;
    }

    /* Reduce font size for headings and descriptions */
    .left-content .step-text h2 {
        font-size: 1.6rem;
    }

    /* Adjust the car details box to avoid overflow on very small screens */
    .car-details-box, .car-description-container {
        padding: 0.8rem;
    }

    /* Center-align text in the car icons and reduce font size */
    .car-icons1 .car-icon-text {
        font-size: 1.3rem;
        text-align: center;
    }

    /* Additional adjustments for buttons and icons */
    .view-booking-btn, .continue-btn, .back-btn {
        font-size: 1.4rem;
        padding: 0.7rem;
    }
    .car-details-box {
        padding: 0.8rem;
        font-size: 1rem; /* Smallest font size */
        max-width: 400px; /* Ensures no overflow */
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
                    <h2>Step 2: Browse available vehicles and choose the one that meets your needs</h2>
                </div>
                <div class="dropdown1">
                    <button class="dropbtn1">Step 2 of 5 - Choose Car</button>
                    <div class="dropdown1-content">
                        <a style="font-weight:bold" href="#">Step 1 - When</a>
                        <a style="font-weight:bold" href="#">Step 2 - Choose Car</a>
                        <a href="#">Step 3 - Price</a>
                        <a href="#">Step 4 - Identification</a>
                        <a href="#">Step 5 - Finish</a>
                    </div>
                </div>
            </div>
    
        </div>
    
        <div class="content-container">
            <!-- Booking Details Box Below -->
            <div class="booking-details-box">
            <div class="booking-details">
                <h3>Booking Details</h3>
                <p>Pick-up Date: <?= htmlspecialchars($_SESSION['pickup_date']); ?></p>
                <p>Pick-up Time: <?= htmlspecialchars($_SESSION['pickup_time']); ?></p>
                <p>Return Date: <?= htmlspecialchars($_SESSION['return_date']); ?></p>
                <p>Return Time: <?= htmlspecialchars($_SESSION['return_time']); ?></p>
                <p>Rental Duration: <?= htmlspecialchars($_SESSION['day']); ?> Day(s)</p>
            </div>
            </div>
    
            <!-- Car Box -->
            <?php
                include_once 'connect.php';
                $conn = connect();

                if ($conn) {
                    $sql = "SELECT id, car_name, price, image, seat, model, fuel, accessories, overview, available, end_date, status FROM vehicles";
                    $result = $conn->query($sql);

                    if ($result === false) {
                        // Query execution failed, handle the error
                        echo "Error executing query: " . $conn->error; // Display error message
                    } else {
                        // Start the container for all cars
                        echo '<div class="car-list">'; // Optional: you can add a wrapper for styling

                        while ($row = $result->fetch_assoc()) {
                            $car_name = $row['car_name'];
                            $imagePath = $row['image'];
                            $price = $row['price'];
                            $seat = $row['seat'];
                            $model = $row['model'];
                            $fuel = $row['fuel'];
                            $accessories = explode(',', $row['accessories']);
                            $overview = $row['overview'];
                            $status = $row['status'];
                            $start_date = $row['available'];
                            $end_date = $row['end_date'];
                            ?>

                            <div class="car-details-container">
                                <div class="car-details-box">
                                    <h4 class="car-name"><?php echo htmlspecialchars($car_name); ?></h4>
                                    <div class="car-details-inner">
                                        <div class="car-image">
                                            <img src="admin/uploads/<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($car_name); ?>" />
                                        </div>
                                        <div class="car-book-button">
                                            <p class="car-name">Rental Price:</p>
                                            <p class="car-price">₱<?php echo number_format($price, 2); ?>/day</p>
                                            <?php if ($status == 1): ?>
                                                <!-- Car is available -->
                                                <a href="step3.php?car_id=<?php echo $row['id']; ?>" class="book-now-btn">Book Now</a>
                                            <?php else: ?>
                                                <!-- Car is unavailable, show start and end dates -->
                                                <p  class="car-unavailable">Unavailable:  from <?php echo date('F j, Y', strtotime($start_date)); ?> to <?php echo date('F j, Y', strtotime($end_date)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="car-icons1">
                                        <div class="car-icon-group1">
                                            <i class="fas fa-user"></i>
                                            <span class="car-icon-text"><?php echo htmlspecialchars($seat); ?></span>
                                        </div>
                                        <div class="car-icon-group1">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span class="car-icon-text"><?php echo htmlspecialchars($model); ?></span>
                                        </div>
                                        <div class="car-icon-group1">
                                            <i class="fas fa-gas-pump"></i>
                                            <span class="car-icon-text"><?php echo htmlspecialchars($fuel); ?></span>
                                        </div>
                                    </div>
                                    <div class="features-container">
                                        <div class="features-column">
                                            <ul>
                                                <?php foreach ($accessories as $accessory): ?>
                                                    <li><?php echo htmlspecialchars($accessory); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="car-description-container">
                                        <h3>Car Description</h3>
                                        <p><?php echo htmlspecialchars($overview); ?></p>
                                    </div>
                                </div>
                            </div>

                            <?php
                        }

                        echo '</div>'; // Close the wrapper for all cars
                    }
                } else {
                    echo "Database connection failed.";
                }
            ?>


            
            </div>
        </div>
        <br>
    </section>

    
</body>

<section class="footer">
    <div class="credit"> create by me web designer | all rights reserved!</div>
</section>

<script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>


</body>
</html>