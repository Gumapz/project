<?php
include_once 'connect.php';
$conn = connect();

session_start();

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {

    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $birth = $_POST['birth'];
    $age = $_POST['age'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $address = $_POST['address'];
    $contact = $_POST['contact'];

    // File handling for Valid ID and Profile Picture
    $valid_id = $_FILES['valid_id']['name'];
    $profile_pic = $_FILES['profile_pic']['name'];

    // Set the upload directory
    $target_dir = "uploads/";
    $valid_id_target = $target_dir . basename($valid_id);
    $profile_pic_target = $target_dir . basename($profile_pic);

    // Move the uploaded files to the server
    move_uploaded_file($_FILES['valid_id']['tmp_name'], $valid_id_target);
    move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic_target);

    // Insert data into the database
    $sql = "INSERT INTO login(lastname, firstname, DOB, age, email, password, address, contact, valid, profile) 
            VALUES ('$lastname', '$firstname', '$birth', '$age', '$email', '$password', '$address', '$contact', '$valid_id', '$profile_pic')";

    if ($conn->query($sql) === TRUE) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>';
            echo 'document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "You Succesfully Register! Please Login!",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location="vehicle.php";
                    });
                });
                </script>';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM login WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Store all user data in session variables
            $_SESSION['user_id'] = $row['login_id'];
            $_SESSION['user_lastname'] = $row['lastname'];
            $_SESSION['user_firstname'] = $row['firstname'];
            $_SESSION['user_birth'] = $row['birth'];
            $_SESSION['user_age'] = $row['age'];
            $_SESSION['user_email'] = $row['email'];
            $_SESSION['user_address'] = $row['address'];
            $_SESSION['user_contact'] = $row['contact'];
            $_SESSION['user_valid_id'] = $row['valid'];
            $_SESSION['user_profile_pic'] = $row['profile'];

            // Redirect to a welcome page or dashboard
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>';
            echo 'document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Login Successfully!",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location="login/user_vehicle.php";
                    });
                });
                </script>';
            exit();
        } else {
            echo "Incorrect password!";
        }
    } else {
        echo "No user found with this email!";
    }
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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="image/logo.jpg">
    <title>Chadoyven Car Rental</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<style>

    .login-form-container .form {
            margin-top:7%;
        }
        .login-form-container {
            overflow-y: auto; /* Add vertical scroll bar if content exceeds height */
        }

        /* Optional: Customize the scroll bar */
        .login-form-container::-webkit-scrollbar {
            width: 10px;
        }

        .login-form-container::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 5px;
        }

        .login-form-container::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }
        .link:hover{
            font-weight: 800;
        }
</style>
<body>
    <header class="header">

        <div id="menu-btn" class="fas fa-bars"></div>

        <a  class="logo">
            <img src="image/logo.jpg" alt="Car Rental Logo">
            <p style="color:black; ">Chadoyven Car Rental</p>
        </a>
        <nav class="navbar">
            <a href="index.php">home</a>
            <a href="vehicle.php">vehicles</a>
            <a href="about.php">about</a>
            <a href="contact.php">contact</a>
        </nav>

        <div id="login-btn">
            <button class="btn">login</button>
            <i class="far fa-user"></i>
        </div>
    </header>

    <!-- Login Form -->
    <div class="login-form-container" id="login-form-container">
        <span class="fas fa-times" id="close-login-form"></span>
        <form action="" method="POST">
            <h3>user login</h3>
            <input type="email" placeholder="email" class="box" name="email" required>
            <input type="password" placeholder="password" class="box" name="password" required>
            <p>forget your password? <a href="#" class="link">click here</a></p>
            <input type="submit" value="login now" class="btn" name="login">
            <p>don't have an account? <a href="#" class="link" id="show-register-form">create account</a></p>
        </form>
    </div>

    <!-- Registration Form -->
    <div class="login-form-container" id="register-form-container">
        <span class="fas fa-times" id="close-register-form"></span>
        <form class="form" action="" method="POST" enctype="multipart/form-data">
            <h3>Register</h3>
            <input type="text" placeholder="Lastname" class="box" name="lastname" required>
            <input type="text" placeholder="Firstname" class="box" name="firstname" required>
            <input type="date" placeholder="Date of Birth" class="box" name="birth" id="birth" required onchange="calculateAge()">
            <input type="number" placeholder="Age" class="box" name="age" id="age" required readonly>
            <p id="age-error" style="color: red; display: none;">Age must be 18 or above.</p>
            <input type="email" placeholder="Email" class="box" name="email" required>
            <input type="password" placeholder="Password" class="box" name="password" required>
            <input type="text" placeholder="Address" class="box" name="address" required>
            <input type="text" placeholder="Contact No." class="box" name="contact" required>
            <br>
            <h2>Driver's License</h2>
            <input type="file" class="box" name="valid_id" required>
            <h2>Profile Picture</h2>
            <input type="file" class="box" name="profile_pic" required>
            <input type="submit" value="register now" class="btn" name="register">
            <p>already have an account? <a href="#" class="link" id="show-login-form">login now</a></p>
        </form>
    </div>

    <section class="vehicle-details-container">
        <!-- Left Box -->
        <div class="left-box">
            <img src="admin/uploads/<?php echo $imagePath; ?>" alt="Car Image">
            <h2><?php echo $car_name; ?></h2>
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
            <form id="review-booking-button" action="" method="POST" enctype="multipart/form-data">
                <p>from date</p>
                <input type="date" class="box" name="from_date" placeholder="Pick-up date" required>
                <p>Pick Up Time</p>
                <input type="time" class="box" name="pick_up_time" placeholder="Pick-up time" required>
                <p>to date</p>
                <input type="date" class="box" name="to_date" placeholder="Drop-off date" required>
                <p>message:</p>
                <textarea class="box" name="message" placeholder="Additional message or special requests"></textarea>
                <input type="submit" value="Review your Booking" class="btn" >
            </form>
            
        </div>
    </section>


    <section class="footer">
        <div class="box-container">
        <div class="box">
                <h3>Quick Links</h3>
                <a href="index.php"><i class="fas fa-arrow-right"></i> Home </a>
                <a href="vehicle.php"><i class="fas fa-arrow-right"></i> Vehicles </a>
                <a href="about.php"><i class="fas fa-arrow-right"></i> About Us </a>
                <a href="contact.php"><i class="fas fa-arrow-right"></i> Contact </a>
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
    <script src="js/script.js"></script>
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


        document.querySelector('#review-booking-button').onclick = () =>{
            document.querySelector('.login-form-container').classList.toggle('active');
        }

        document.querySelector('#close-login-form').onclick = () =>{
            document.querySelector('.login-form-container').classList.remove('active');
        }


        function calculateAge() {
            const birthDate = new Date(document.getElementById('birth').value);
            const today = new Date();
            
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();
            
            // Adjust if the birth date hasn't occurred yet this year
            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            const ageField = document.getElementById('age');
            const ageError = document.getElementById('age-error');
            
            // Check if age is 18 or above
            if (age >= 18) {
                ageField.value = age;
                ageError.style.display = 'none';  // Hide error message
            } else {
                ageField.value = '';  // Clear age input if below 18
                ageError.style.display = 'block';  // Show error message
            }
        }
    </script>
</body>
</html>