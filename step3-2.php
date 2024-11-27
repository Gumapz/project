<?php
include_once 'connect.php';
$conn = connect();
session_start();
// Retrieve data from the previous form
$pickup_date = $_POST['pickup_date'];
$pickup_time = $_POST['pickup_time'];
$day = $_POST['day']; // Duration in days
$car_name = $_POST['car_name'];
$price = $_POST['price'];
$car_image = $_POST['car_image'];
$car_model = $_POST['model'];
$renter_name = $_POST['name']; // Renter's name
$renter_email = $_POST['email']; // Renter's email
$renter_contact = $_POST['contact']; // Renter's contact number


// Calculate the return date
$pickup_date_obj = new DateTime($pickup_date);
$return_date_obj = clone $pickup_date_obj;
$return_date_obj->modify('+' . $day . ' days');
$return_date = $return_date_obj->format('Y-m-d');

// Calculate the total price
$total_price = $price * $day;

$pickup_time_12hr = date("g:i A", strtotime($pickup_time));
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

        .remove-btn {
            background-color: #ff4d4d; /* Red color for remove button */
            color: white; /* Text color */
            border: none; /* Remove default border */
            padding: 10px 15px; /* Padding around the text */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            font-size: 14px; /* Font size */
            transition: background-color 0.3s ease; /* Smooth background color transition */
            margin-left: 10px; /* Space between the add and remove buttons */
        }

        .remove-btn:hover {
            background-color: #ff1a1a; /* Darker red on hover */
        }

        .remove-btn:disabled {
            background-color: #ccc; /* Gray color when disabled */
            cursor: not-allowed; /* Change cursor to indicate it's disabled */
            color: #666; /* Lighter text color when disabled */
        }
        .extra-services-box {
            margin-top: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
            width: 1200px; /* Set a fixed width or max-width */
            max-width: 100%; /* Allow it to be responsive but not exceed 100% of its parent */
            box-sizing: border-box; /* Include padding and border in width calculation */
            overflow: hidden; /* Optional: hide overflow content if it exceeds the box */
        }
        .content-container {
            display: flex;
            gap: 2rem;
            align-items: flex-start;
        }
        /* Extra Small Screens (very small phones, e.g., 320px width) */
        @media (max-width: 375px) {
            .extra-services-box {
                width: 100%; /* Full width */
                padding: 0.8rem; /* Reduced padding */
            }

            .extra-services-title {
                font-size: 1.3rem;
            }

            .extra-services-item {
                flex-direction: column;
                margin-top: 1.5rem;
            }

            .service-description, .service-price p {
                font-size: 1.2rem; /* Smaller font */
            }

            .service-btn {
                font-size: 1.1rem;
                padding: 0.5rem 1rem;
            }

            .summary-left, .summary-right {
                width: 50%;
                text-align: left; /* Stack items */
                padding: 0.5rem 0;
            }

            .button-section {
                flex-direction: column;
                margin-left: 0;
                margin-right: 0;
            }

            .back-btn, .continue-btn {
                width: 100%; /* Full width for small screens */
                padding: 0.8rem;
            }
            .content-container {
                flex-direction: column; /* Stack items vertically */
            }

            /* Ensure .booking-details-box1 is displayed below .car-details-container */
            .car-details-container {
                order: -1; /* Sets .car-details-container above */
            }
            .booking-details-box1 {
                order: 1; /* Sets .booking-details-box1 below */
                width: 90%;
                margin-left: 3%;
            }
            .car-details-box{
                width: 360px;
                margin-left: 40%;
            }
            
        }

        /* Large Phones and Small Tablets (between 376px and 768px) */
        @media (min-width: 376px) and (max-width: 768px) {
            .extra-services-box {
                width: 100%;
                padding: 1rem;
            }

            .extra-services-title {
                font-size: 1.5rem;
            }

            .extra-services-item {
                margin-top: 2rem;
            }

            .service-description {
                font-size: 1.3rem;
            }

            .service-price p {
                font-size: 1.4rem;
            }

            .service-btn {
                padding: 0.7rem 1.3rem;
                font-size: 1.2rem;
            }

            .summary-left, .summary-right {
                width: 50%;
            }

            .button-section {
                margin-left: 0;
                margin-right: 0;
            }

            .continue-btn {
                width: 100%;
                margin-top: 1rem;
            }
            .back-btn {
                width: 15%;
                margin-top: 1rem;
            }
            .content-container {
                flex-direction: column; /* Stack items vertically */
            }

            /* Ensure .booking-details-box1 is displayed below .car-details-container */
            .car-details-container {
                order: -1; /* Sets .car-details-container above */
            }

            .booking-details-box1 {
                order: 1; /* Sets .booking-details-box1 below */
                width: 90%;
                margin-left: 3%;
            }
            .car-details-box{
                width: 430px;
                margin-left: 40%;
            }
        }

        /* Large Tablets (768px and up) */
        @media (min-width: 769px) and (max-width: 992px) {
            .extra-services-box {
                width: 100%; /* Slightly smaller than full width */
            }

            .extra-services-title {
                font-size: 1.7rem;
            }

            .extra-services-item {
                flex-direction: row;
                margin-top: 2.5rem;
            }

            .service-description, .service-price p {
                font-size: 1.5rem;
            }

            .service-btn {
                font-size: 1.3rem;
                padding: 0.8rem 1.5rem;
            }

            .summary-left, .summary-right {
                width: 50%; /* Split width on larger tablets */
            }

            .button-section {
                flex-direction: row;
                justify-content: space-between;
                margin-left: 5rem;
                margin-right: 5rem;
            }

            .back-btn, .continue-btn {
                width: auto;
            }
            .content-container {
                flex-direction: column; /* Stack items vertically */
            }

            /* Ensure .booking-details-box1 is displayed below .car-details-container */
            .car-details-container {
                order: -1; /* Sets .car-details-container above */
            }

            .booking-details-box1 {
                order: 1; /* Sets .booking-details-box1 below */
                width: 90%;
                margin-left: 3%;
            }
            .car-details-box{
                width: 530px;
                margin-left: 40%;
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
                    
                    <?php
                        // Assuming you have a database connection in $conn
                        $query = "SELECT * FROM brand"; // Adjust the query if you need specific services
                        $result = mysqli_query($conn, $query);

                        if ($result) {
                            while ($service = mysqli_fetch_assoc($result)) {
                                $serviceId = $service['brand_id'];
                                $serviceName = $service['service'];
                                $servicePrice = $service['price'];
                                
                                // Output for each service
                                echo '<div class="extra-services-item">';
                                echo '    <div class="service-description">';
                                echo '        <p style="font-size: 18px;">' . htmlspecialchars($serviceName) . '</p>'; // Escape output for security
                                echo '    </div>';
                                echo '    <div class="service-price">';
                                echo '        <p>₱' . number_format($servicePrice, 2) . '</p>';
                                echo '        <button class="service-btn" onclick="addService(' . $servicePrice . ', this)">Add Service</button>';
                                echo '        <button class="remove-btn" onclick="removeService(' . $servicePrice . ', this)" disabled>Remove Service</button>';
                                echo '    </div>';
                                echo '</div>';
                            }
                        } else {
                            echo 'Error fetching services: ' . mysqli_error($conn);
                        }
                    ?>
                </div>
            </div>
        </div>
    
        <div class="content-container">
            <!-- Booking Details Box Below -->
            <div class="booking-details-box1">
                <div class="booking-details">
                    <h3>Booking Details</h3>
                    <p style="display: none;"><?php echo htmlspecialchars($renter_name); ?></p>
                    <p style="display: none;"> <?php echo htmlspecialchars($renter_email); ?></p>
                    <p style="display: none;"> <?php echo htmlspecialchars($renter_contact); ?></p>
                    <p>Pick-up Date: <?php echo htmlspecialchars($pickup_date); ?></p>
                    <p>Pick-up Time: <?php echo htmlspecialchars($pickup_time_12hr); ?></p>
                    <p>Return Date: <?php echo htmlspecialchars($return_date); ?></p>
                    <p>Return Time: <?php echo htmlspecialchars($pickup_time_12hr); ?></p>
                    <p>Rental Duration: <?php echo htmlspecialchars($day); ?> Day(s)</p>
                    <!-- Car Details -->
                    <br>
                    <h3>Car Selected</h3>
                    <div style="width: 100%;" class="car-details-container">
                        <p class="car-name"><?php echo htmlspecialchars($car_name); ?></p>
                        <div class="car-image">
                            <img style="width:300px;" src="admin/uploads/<?php echo htmlspecialchars($car_image); ?>" alt="<?php echo htmlspecialchars($car_name); ?>" />
                        </div>
                        <p>Model: <?php echo htmlspecialchars($car_model); ?></p>
                    </div>
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
                            <p><?php echo htmlspecialchars($day); ?> Day(s)</p> <!-- Rental duration -->
                            <p>₱<?php echo number_format($price, 2); ?></p> <!-- Car rental fee -->
                            <p id="extra-price">₱0.00</p> <!-- Extra price -->
                            <hr>
                            <p id="total-price">₱<?php echo number_format($total_price, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section>
            <div class="button-section">
                <a href="vehicle.php" class="back-btn">Back</a>
                <a id="proceed-btn">
                    <button class="continue-btn" onclick="passData()">Continue</button>
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
    const rentalDuration = parseInt('<?php echo htmlspecialchars($day); ?>'); // Rental duration in days
    const carRentalFee = parseFloat('<?php echo htmlspecialchars($price); ?>'); // Car rental fee

    // Calculate the initial total price based on rental duration
    let totalPrice = rentalDuration * carRentalFee;

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

        // Disable the clicked button and enable the remove button
        buttonElement.disabled = true;
        buttonElement.nextElementSibling.disabled = false; // Enable the remove button
        buttonElement.textContent = "Added"; // Optionally, change the button text to "Added"
    }

    function removeService(servicePrice, buttonElement) {
        // Update the total extra price
        totalExtraPrice -= servicePrice;

        // Update the Extra Price in the payment summary
        document.getElementById('extra-price').textContent = '₱' + totalExtraPrice.toFixed(2);

        // Calculate the new total price: (car rental fee * rental duration) + total extra price
        const totalCarRentalCost = carRentalFee * rentalDuration;
        totalPrice = totalCarRentalCost + totalExtraPrice;

        // Update the Total Price in the payment summary
        document.getElementById('total-price').textContent = '₱' + totalPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // Disable the remove button and enable the add button
        buttonElement.disabled = true;
        buttonElement.previousElementSibling.disabled = false; // Enable the add button
        
        // Reset the text of the add button to "Add Service"
        buttonElement.previousElementSibling.textContent = "Add Service"; // Change back to "Add Service"
    }


    // This function can be called initially to set the total price on page load
    function initializeTotalPrice() {
        document.getElementById('total-price').textContent = '₱' + totalPrice.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Call this function on page load
    initializeTotalPrice();

    function passData() {
    // Retrieve PHP values
    const pickupDate = '<?php echo urlencode($pickup_date); ?>';
    const pickupTime = '<?php echo urlencode($pickup_time); ?>';
    const returnDate = '<?php echo urlencode($return_date); ?>'; // Add the return_date
    const day = '<?php echo htmlspecialchars($day); ?>';
    const carName = '<?php echo urlencode($car_name); ?>';
    const price = '<?php echo htmlspecialchars($price); ?>';
    const carImage = '<?php echo urlencode($car_image); ?>';
    const carModel = '<?php echo urlencode($car_model); ?>';
    const renterName = '<?php echo urlencode($renter_name); ?>';
    const renterEmail = '<?php echo urlencode($renter_email); ?>';
    const renterContact = '<?php echo urlencode($renter_contact); ?>';
    
    // Retrieve extra-price and total-price from the page using JavaScript
    const extraPrice = document.getElementById('extra-price').textContent.replace('₱', '').replace(',', '');
    const totalPrice = document.getElementById('total-price').textContent.replace('₱', '').replace(',', '');

    // Build the URL with all the parameters, including return_date, extra-price, and total-price
    const url = `step4-2.php?pickup_date=${pickupDate}&pickup_time=${pickupTime}&return_date=${returnDate}&day=${day}&car_name=${carName}&price=${price}&car_image=${carImage}&car_model=${carModel}&renter_name=${renterName}&renter_email=${renterEmail}&renter_contact=${renterContact}&extra_price=${extraPrice}&total_price=${totalPrice}`;

    // Redirect to the next page with the data
    window.location.href = url;
}

</script>

</body>
</html>