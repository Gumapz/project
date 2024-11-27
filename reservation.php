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
            <a href="reservation.php" style=" color: red; font-weight: 600;">reservations</a>
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
        <h1>Choose your car</h1>
        <p class="subtitle">Rent now</p>
        </div>
        
    </section>

    

    <section class="booking-section">
    <form action="step2.php" method="post" class="booking-form">
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
                    <label style="color: black;" for="name" class="form-label1">Fullname</label>
                    <input type="text" id="name" class="form-input1" name="name" required>
                </div>
                <div class="form-groups">
                    <label style="color: black;" for="email" class="form-label1">Email</label>
                    <input type="email" id="email" class="form-input1" name="email" required>
                </div>
                <div class="form-groups">
                    <label style="color: black;" for="number" class="form-label1">Contact</label>
                    <input type="tel" id="contact" class="form-input1" name="contact" 
                            inputmode="numeric" pattern="\d{11}" title="Please enter a valid 11-digit phone number" required>
                </div>
            </div>
            <div class="form-rows">
                <button type="submit" class="form-button">rent a car</button>
            </div>
        </form>
    </section>

    

    <section class="company-info">
        <h2>Our Company</h2>
        <p>
            Welcome to [Your Company Name]! We are dedicated to providing the best car rental services to ensure you have a smooth and enjoyable experience. Our fleet includes a variety of vehicles to suit your needs, whether you're traveling for business or leisure. Our team is committed to excellent customer service and is here to assist you at every step of your journey.
        </p>
        <p>
            Contact us for more information or to make a reservation today!
        </p>
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
    var today = new Date().toISOString().split('T')[0];
        // Set the min attribute of the date input to today's date
        document.getElementById('pickup-date').setAttribute('min', today);


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