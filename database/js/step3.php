<?php
include_once 'connect.php';
$conn = connect();
session_start();

// Initialize variables for car details
$car_details = [];

if ($conn) {
    // Check if car_id is provided in the URL
    if (isset($_GET['car_id'])) {
        $car_id = intval($_GET['car_id']); // Get car_id from URL and convert to an integer

        // Query to fetch the car details based on car_id
        $sql = "SELECT id, car_name, price, image, seat, model, fuel, accessories, overview FROM vehicles WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $car_id); // Bind the car_id parameter
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Fetch the car details
            $car_details = $result->fetch_assoc();
        } else {
            echo "No car found with the specified ID.";
        }
    } else {
        echo "No car ID provided.";
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
    <title>Car Rental System</title>
    <link rel="icon" type="image/x-icon" href="image/logo.jpg">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .booking-details-box1{
            height: auto;
        }
        hr{
            border: 0;
            height: 2px;
            width: 100%;
            background: #ccc;
            margin: 15px 0 10px;
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
                    <h2>Step 3: choose available extra's.</h2>
                </div>
                <div class="dropdown1">
                    <button class="dropbtn1">Step 3 of 5 - price</button>
                    <div class="dropdown1-content">
                        <a style="font-weight:bold" href="#">Step 1 - When</a>
                        <a style="font-weight:bold" href="#">Step 2 - Choose Car</a>
                        <a style="font-weight:bold" href="#">Step 3 - Price</a>
                        <a href="#">Step 4 - Identification</a>
                        <a href="#">Step 5 - Finish</a>
                    </div>
                    
                </div>
                <div class="extra-services-box">
                    <h3 class="extra-services-title">Choose Extra Services</h3>
                    
                    <!-- First Service -->
                    <div class="extra-services-item">
                        <div class="service-description">
                            <p>24hrs or Overnight driver service (Food & Accommodation included)</p>
                        </div>
                        <div class="service-price">
                            <p>₱100.00</p>
                            <button class="service-btn" onclick="addService(100, this)">Add Service</button>
                        </div>
                    </div>
                    
                    <!-- Second Service (Newly added) -->
                    <div class="extra-services-item">
                        <div class="service-description">
                            <p>Child Car Seat (For added safety and comfort)</p>
                        </div>
                        <div class="service-price">
                            <p>₱20.00</p>
                            <button class="service-btn" onclick="addService(20, this)">Add Service</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="content-container">
            <!-- Booking Details Box Below -->
            <div class="booking-details-box1">
                <div class="booking-details">
                    <h3>Booking Details</h3>
                    <?php if (isset($_SESSION['name'])): ?>
                        <p>Pick-up Date: <?= htmlspecialchars($_SESSION['pickup_date']); ?></p>
                        <p>Pick-up Time: <?= htmlspecialchars($_SESSION['pickup_time']); ?></p>
                        <p>Return Date: <?= htmlspecialchars($_SESSION['return_date']); ?></p>
                        <p>Return Time: <?= htmlspecialchars($_SESSION['return_time']); ?></p>
                        <p>Rental Duration: <?= htmlspecialchars($_SESSION['day']); ?> Day(s)</p>
                    <?php else: ?>
                        <p>No booking details available.</p>
                    <?php endif; ?>

                    <?php if ($car_details): ?>
                        <br>
                        <h3>Car Selected</h3>
                        <div style="width: 100%;" class="car-details-container">
                            <p class="car-name"><?= htmlspecialchars($car_details['car_name']); ?></p>
                            <div class="car-image">
                                <img style="width:300px;" src="admin/uploads/<?= htmlspecialchars($car_details['image']); ?>" alt="<?= htmlspecialchars($car_details['car_name']); ?>" />
                            </div>
                            <p>Model: <?= htmlspecialchars($car_details['model']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
    
            <!-- Car Box -->
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
                            <p><?= htmlspecialchars($_SESSION['day']); ?> Day(s)</p> <!-- Update dynamically -->
                            <p>₱<?= number_format(htmlspecialchars($car_details['price']), 2); ?></p> <!-- Car rental fee -->
                            <p id="extra-price">₱0.00</p> <!-- Extra price -->
                            <hr>
                            <p id="total-price">₱<?= number_format(htmlspecialchars($car_details['price']), 2); ?></p> <!-- Total price -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="button-section">
            <a href="step2.php" class="back-btn">Back</a>
            <a id="proceed-btn" onclick="goToStep4()">
                <button class="continue-btn">Continue</button>
            </a>
        </div>
        <br>
    </section>
    
</body>
<section class="footer">
    <div class="credit"> create by me web designer | all rights reserved!</div>
</section>

<script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>
<script>
    // Initialize a variable to keep track of the total extra price
    let totalExtraPrice = 0;

    // Retrieve the rental duration and car price from the HTML
    const rentalDuration = parseInt('<?= htmlspecialchars($_SESSION['rental_duration']); ?>'); // Rental duration in days
    const carRentalFee = parseFloat('<?= htmlspecialchars($car_details['price']); ?>'); // Car rental fee

    // Calculate the initial total price based on rental duration
    let totalPrice = carRentalFee * rentalDuration;

    function addService(servicePrice, buttonElement) {
        // Update the total extra price
        totalExtraPrice += servicePrice;

        // Update the Extra Price in the payment summary
        document.getElementById('extra-price').textContent = '₱' + totalExtraPrice.toFixed(2);

        // Calculate the new total price: (car rental fee * rental duration) + total extra price
        const totalCarRentalCost = carRentalFee * rentalDuration;
        totalPrice = totalCarRentalCost + totalExtraPrice;

        // Update the Total Price in the payment summary
        document.getElementById('total-price').textContent = '₱' + totalPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Disable the clicked button
        buttonElement.disabled = true;
        buttonElement.textContent = "Added"; // Optionally, change the button text to "Added"
    }

    // This function can be called initially to set the total price on page load
    function initializeTotalPrice() {
        document.getElementById('total-price').textContent = '₱' + totalPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    }

    // Call this function on page load
    initializeTotalPrice();



    function goToStep4() {
    // Get the current values
    const rentalDuration = '<?= htmlspecialchars($_SESSION['rental_duration']); ?>';
    const carRentalFee = '<?= htmlspecialchars($car_details['price']); ?>';
    const extraPrice = document.getElementById('extra-price').textContent.replace('₱', '').replace(',', '').trim(); // Get the extra price
    const totalPrice = document.getElementById('total-price').textContent.replace('₱', '').replace(',', '').trim(); // Get the total price

    // Get all car details to pass to step4.php
    const carId = '<?= htmlspecialchars($car_details['id']); ?>'; // Car ID
    const carName = encodeURIComponent('<?= htmlspecialchars($car_details['car_name']); ?>'); // Car Name
    const carModel = encodeURIComponent('<?= htmlspecialchars($car_details['model']); ?>'); // Car Model
    const carImage = encodeURIComponent('<?= htmlspecialchars($car_details['image']); ?>'); // Car Image
    const carSeat = '<?= htmlspecialchars($car_details['seat']); ?>'; // Number of Seats
    const carFuel = encodeURIComponent('<?= htmlspecialchars($car_details['fuel']); ?>'); // Fuel Type
    const carAccessories = encodeURIComponent('<?= htmlspecialchars($car_details['accessories']); ?>'); // Accessories
    const carOverview = encodeURIComponent('<?= htmlspecialchars($car_details['overview']); ?>'); // Overview

    // Create the URL with query parameters
    const url = `step4.php?rental_duration=${rentalDuration}&car_rental_fee=${carRentalFee}&extra_price=${extraPrice}&total_price=${totalPrice}&car_id=${carId}&car_name=${carName}&car_model=${carModel}&car_image=${carImage}&car_seat=${carSeat}&car_fuel=${carFuel}&car_accessories=${carAccessories}&car_overview=${carOverview}`;

    // Redirect to step4.php
    window.location.href = url;
}

</script>

</body>
</html>