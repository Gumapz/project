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
    <title>Car Rental System</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="../image/logo.jpg">
    <link rel="stylesheet" href="css/user_style.css">

    <style>
        .profile-box {
    width: 600px;
    margin: 150px auto 0;
    padding: 50px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
    text-align: center;
    display: flex; /* Use flexbox to align items */
    align-items: center; /* Center items vertically */
    }

    .profile-image {
        width: 80px; /* Adjust size as needed */
        height: 80px; /* Adjust size as needed */
        border-radius: 50%;
        margin-right: 20px; /* Space between image and text */
    }

    .profile-info {
        text-align: left; /* Align text to the left */
    }

    .profile-name {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .profile-location {
        font-size: 18px;
        color: #666;
    }


    .settings-section {
        width: 600px;
        margin: 20px auto; /* Center the section and adjust margin as needed */
        padding: 20px;
        
    }

    .settings-section h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 20px;
    }

    .settings-item {
        margin-bottom: 15px;
    }

    .settings-item label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        font-size: 15px;
    }

    .settings-item input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box; /* Ensure padding and border are included in the element's total width and height */
    }

    .save-button {
        width: 100%;
        padding: 10px;
        background-color:var(--light-yellow);
        color: #fff;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .save-button:hover {
        background-color: var(--yellow);
    }

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

    <div class="profile-box">
        <img src="../uploads/<?php echo htmlspecialchars($_SESSION['user_profile_pic']); ?>" alt="User Image" class="profile-image">
        <div class="profile-info">
            <div class="profile-name"><?php echo htmlspecialchars($_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname']); ?></div>
            <div class="profile-location"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p></div>
        </div>
    </div>
    <div class="settings-section">
        <h2>General Settings</h2>
        <div class="settings-item">
            <label for="full-name">Full Name: </label>
            <input type="text" id="full-name" value="<?php echo htmlspecialchars($_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname']); ?>">
        </div>
        <div class="settings-item">
            <label for="dob">Age:</label>
            <input type="number" id="dob" value="<?php echo htmlspecialchars($_SESSION['user_age']); ?>">
        </div>
        <div class="settings-item">
            <label for="email-address">Email Address:</label>
            <input type="email" id="email-address" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>">
        </div>
        <div class="settings-item">
            <label for="phone-number">Phone Number:</label>
            <input type="tel" id="phone-number" value="<?php echo htmlspecialchars($_SESSION['user_contact']); ?>">
        </div>
        <div class="settings-item">
            <label for="address">Your Address:</label>
            <input type="text" id="address" value="<?php echo htmlspecialchars($_SESSION['user_address']); ?>">
        </div>
        <button class="save-button">Save Changes</button>
    </div>
    
    <script src="js/user_script.js"></script>
</body>
</html>