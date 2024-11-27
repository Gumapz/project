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
    <!-- FullCalendar CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.css" rel="stylesheet" />

    <!-- FullCalendar JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>

    <style>
        .car-unavailable{
            font-size: 15px;
            color: red;
            font-weight: 600;
        }
        hr{
            border: 0;
            height: 3px;
            width: 100%;
            background: #ccc;
        }
        #error-message {
            color: red;
            font-size: 14px;
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
        /* Optional: Style for disabled past dates */
        .fc-disabled-day {
            background-color: #f0f0f0;  /* Light gray background for disabled past dates */
            color: #d3d3d3;  /* Lighter text color */
            pointer-events: none;  /* Prevent any interaction with the date */
            cursor: not-allowed;  /* Show a not-allowed cursor when hovering */
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
            <a href="vehicle.php" style=" color: red; font-weight: 600;">vehicles</a>
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
            <h1>Car details</h1>
            <div class="subtitle">Choose your car</div>
        </div>
        
    </section>

    <?php
    include_once 'connect.php';
    $conn = connect();

    if ($conn) {
        $sql = "SELECT id, car_name, price, image, seat, model, fuel, accessories, overview, status, available, end_date FROM vehicles";
        $result = $conn->query($sql);

        if ($result === false) {
            // Query execution failed, handle the error
            echo "Error executing query: " . $conn->error; // Display error message
        } else {
            ?>
            <section class="car-display">
                <?php
                while ($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    $imagePath = $row['image'];
                    $car_name = $row['car_name'];
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
                    <hr>
                    <div class="car">
                    <div class="car-image">
                        <img style="width: 80%" src="admin/uploads/<?php echo $imagePath; ?>" alt="<?php echo $car_name; ?>" />
                    </div>
                    <div class="car-details">
                        <div class="car-header">
                            <h2 class="car-name"><?php echo $car_name; ?></h2>
                            <?php if ($status == 1): ?>
                                <!-- Car available -->
                                <button class="btn" onclick="openPopup(<?php echo $id; ?>, '<?php echo $car_name; ?>', <?php echo $price; ?>, '<?php echo $model; ?>', null, '<?php echo $imagePath; ?>')">Rent Now</button>
                            <?php else: ?>
                                <!-- Car unavailable -->
                                <p class="car-unavailable">Unavailable: <br>from: <?php echo date('F j, Y', strtotime($start_date)); ?> <br> to: <?php echo date('F j, Y', strtotime($end_date)); ?></p>
                                <button class="btn" onclick="openPopup(<?php echo $id; ?>, '<?php echo $car_name; ?>', <?php echo $price; ?>, '<?php echo $model; ?>', '<?php echo $end_date; ?>', '<?php echo $imagePath; ?>')">Rent Now</button>
                            <?php endif; ?>
                        </div>
                            <p class="car-price">Price: ₱<?php echo number_format($price, 2); ?>/day</p>
                            <div class="car-icons">
                                <div class="car-icon-group">
                                    <i class="fas fa-user"></i>
                                    <span class="car-icon-text"><?php echo $seat; ?> seats</span>
                                </div>
                                <div class="car-icon-group">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span class="car-icon-text"><?php echo $model; ?> model</span>
                                </div>
                                <div class="car-icon-group">
                                    <i class="fas fa-gas-pump"></i>
                                    <span class="car-icon-text"><?php echo $fuel; ?> gas</span>
                                </div>
                            </div>
                            <ul class="car-accessories">
                                <h1>Accessories</h1>
                                <?php foreach ($accessories as $accessory): ?>
                                    <li style="color: black"><?php echo htmlspecialchars($accessory); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <h1>Overview</h1>
                            <p class="car-description"><?php echo $overview; ?></p>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </section>
            <?php
        }
    } else {
        echo "Database connection failed.";
    }
?>

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
                    <input type="date" id="pickup-date" class="form-inputs" name="pickup_date" required>
                </div>
                <div class="form-groups">
                    <label style="color: black;" for="pickup-time" class="form-label1">Pick-up Time</label>
                    <input type="time" id="pickup-time" class="form-inputs" name="pickup_time" required>
                </div>
                <div class="form-groups">
                    <label style="color: black;" for="day" class="form-label1">Duration Day(s)</label>
                    <input type="number" id="day" class="form-inputs" name="day" min="1" max="99" value="1" required>
                    <span id="error-message" style="color: red; display: none;">The limit for renting the car is 99 days.</span>
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