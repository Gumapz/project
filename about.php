
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
        .footer .box-container {
            margin-left: 5%;
        }
       /* About Us Section Styles */
.about-us {
    padding: 50px 30px; /* Increased padding for better spacing */
    background-color: #fdfdfd; /* Light background with a hint of white */
    border-radius: 12px; /* Slightly more rounded corners */
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1); /* More pronounced shadow */
    margin: 30px 0; /* More space above and below the section */
}

.about-content {
    max-width: 900px; /* Increased max-width for better readability */
    margin: 0 auto; /* Center the section */
    text-align: left; /* Left alignment for a more professional look */
}

.section-title {
    margin-top: 2.5rem; /* More space above headings */
    font-size: 2rem; /* Larger font size for section titles */
    color: #34495e; /* Darker heading color for better contrast */
    border-bottom: 3px solid #3498DB; /* Thicker underline for section titles */
    padding-bottom: 8px; /* More space below titles */
    font-weight: 600; /* Bold titles for emphasis */
}

.intro, p {
    font-size: 1.5rem; /* Slightly larger font for the introductory paragraph */
    line-height: 1.6; /* Increased line height for better readability */
    margin-bottom: 2rem; /* Space below the introduction */
    color: #555; /* Softer color for text */
}

.core-values,
.benefits-list {
    list-style-type: none; /* Remove bullets */
    padding: 0; /* Remove default padding */
}

.core-values li,
.benefits-list li {
    background: #e9ecef; /* Light background for list items */
    border-radius: 5px; /* Rounded corners */
    padding: 12px; /* Increased padding */
    margin: 10px 0; /* Space between list items */
    transition: background 0.3s ease; /* Smooth transition for hover effect */
    font-size: 1.5rem;
}

.core-values li:hover,
.benefits-list li:hover {
    background: #d1e7dd; /* Change background on hover for interactivity */
}

.testimonial {
    font-style: italic; /* Italicize testimonial */
    margin: 20px 0; /* Space above and below the testimonial */
    border-left: 4px solid #3498DB; /* Left border for emphasis */
    padding-left: 15px; /* Space to the left of the text */
    color: #555; /* Softer text color for testimonials */
}

.testimonial:before {
    content: "“"; /* Adding quote mark before testimonial */
    font-size: 2rem; /* Larger quote mark */
    color: #3498DB; /* Color for quote mark */
    position: relative; /* Positioning for alignment */
    top: -5px; /* Positioning above the text */
    left: 2px; /* Positioning to the left of the text */
}

.testimonial:after {
    content: "”"; /* Adding closing quote mark after testimonial */
    font-size: 2rem; /* Larger quote mark */
    color: #3498DB; /* Color for quote mark */
    position: relative; /* Positioning for alignment */
    bottom: 5px; /* Positioning below the text */
    right: 2px; /* Positioning to the right of the text */
}

/* Responsive Styles */
@media (max-width: 600px) {
    .about-us {
        padding: 30px 15px; /* Reduced padding on smaller screens */
    }

    .section-title {
        font-size: 1.6rem; /* Smaller font size for titles on mobile */
    }

    .intro {
        font-size: 1rem; /* Smaller font size for introductory paragraph */
    }

    .core-values li,
    .benefits-list li {
        padding: 10px; /* Adjust padding for smaller screens */
    }
}

    </style>
</head>
<body>
    <header class="header">
        <div id="menu-btn" class="fas fa-bars"></div>
        <a href="index.php" class="logo">
            <img style="border-radius: 10px;" src="image/logo.jpg" alt="Car Rental Logo">
            <h2>Chadoyven Car Rental</h2>
        </a>
        <nav class="navbar">
            <a href="reservation.php">reservations</a>
            <a href="index.php">home</a>
            <a href="vehicle.php">vehicles</a>
            <a href="about.php" style=" color: red; font-weight: 600;">about</a>
            <a href="contact.php">contact</a>
            <a href="manage.php">manage bookings</a>
        </nav>
        <div id="login-btn"></div>
    </header>

    <section class="home1">
        <div class="image-container">
            <img src="image/bg1.1.jpg" alt="Car Rental" />
        </div>
        <div class="home-content">
            <h1>About Us</h1>
        </div>
    </section>

    <section class="about-us">
    <div class="about-content">
        <h2 class="section-title">Welcome to Chadoyven Car Rental!</h2>
        <p class="intro">Founded with a commitment to providing high-quality rental services, Chadoyven Car Rental has been helping people get on the road with ease and confidence for over [number of years in operation] years. We’re proud of our reputation for reliability, affordability, and customer satisfaction, serving thousands of happy clients each year.</p>

        <h2 class="section-title">Our Mission</h2>
        <p>At Chadoyven Car Rental, our mission is to make car rentals as seamless and enjoyable as possible. We believe that every journey begins with trust, and we are dedicated to providing transparent, flexible, and customer-focused services tailored to fit every traveler’s unique needs.</p>

        <h2 class="section-title">Our Vision</h2>
        <p>Our vision is to be the leading choice for car rentals, where every client feels valued and can depend on our commitment to quality. We strive to create a smooth rental experience while contributing positively to our community and the environment.</p>

        <h2 class="section-title">Our Core Values</h2>
        <ul class="core-values">
            <li><strong>Reliability:</strong> We ensure that all vehicles are well-maintained, regularly serviced, and ready for your journey.</li>
            <li><strong>Affordability:</strong> We believe in transparent pricing, offering competitive rates with no hidden costs.</li>
            <li><strong>Customer Satisfaction:</strong> Our friendly and knowledgeable team is always here to help, ensuring a worry-free rental experience.</li>
            <li><strong>Sustainability:</strong> We are committed to reducing our environmental impact by offering eco-friendly vehicle options and following green practices.</li>
        </ul>

        <h2 class="section-title">Why Choose Us?</h2>
        <ul class="benefits-list">
            <li><strong>Convenient Locations:</strong> With easy pick-up and drop-off locations, you’re never far from a reliable ride.</li>
            <li><strong>Exceptional Support:</strong> We offer 24/7 customer support and assistance to ensure that your experience with us is smooth from start to finish.</li>
        </ul>

        <h2 class="section-title">Meet Our Team</h2>
        <p>Our team at Chadoyven Car Rental is made up of professionals who are passionate about helping you get on the road safely and comfortably. Led by [CEO or Manager's Name], our staff is here to support you at every step, whether you’re booking online or picking up your keys.</p>

        <h2 class="section-title">What Our Customers Say</h2>
        <?php
            include_once 'connect.php';
            $conn = connect();

            // Fetch testimonials from the "review" table
            $sql = "SELECT review, Name FROM review";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output each testimonial
                while ($row = $result->fetch_assoc()) {
                    echo '<p class="testimonial">"';
                    echo htmlspecialchars($row["review"]);
                    echo '" — ' . htmlspecialchars($row["Name"]);
                    echo '</p>';
                }
            } else {
                echo '<p class="testimonial">No testimonials available at the moment.</p>';
            }

            // Close the connection
            $conn->close();
            ?>
        <h2 class="section-title">Contact Us</h2>
        <p>Have any questions or ready to book? Our team is just a call or click away. Visit our <a href="contact.php">Contact Us</a> page to get in touch.</p>
    </div>
</section>


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
        <div class="credit"> created by me web designer | all rights reserved!</div>
    </section>

    <script src="js/script.js"></script>
</body>
</html>
