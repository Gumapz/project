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


// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $car_id = $_POST['car_id'];
    $fromDate = $_POST['fromDate'];
    $pickUpTime = $_POST['pickUpTime'];
    $time = date("g:i A", strtotime($pickUpTime));
    $toDate = $_POST['toDate'];
    $message = $_POST['message'];

    // Fetch car details from the database
    $stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();

    // Car details
    $image = $car['image'];
    $car_name = $car['car_name'];
    $model = $car['model'];
    $fuel = $car['fuel'];
    $seat = $car['seat'];
    $price = $car['price'];
    $overview = $car['overview'];
    $accessories = explode(',', $car['accessories']);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/review.css">
    <link rel="icon" type="image/x-icon" href="../image/logo.jpg">
    <title>Chadoyven Car Rental</title>
    <style>
        .image-review{
            width: 150px;
            margin-top: 2%;
            margin-bottom: 1%;
        }

        .payment{
            width: 200px;
            margin-top: 2%;
            margin-bottom: 1%;
            box-shadow: 2px 2px 5px black;
        }

        .cancel{
            font-size: 15px;
            font-weight: 600;
            color: black;
            margin: auto;
            width: 25%;
            background-color: red;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel a{
            color: white;
        }


        .cancel:hover{
            background-color: rgb(145, 17, 17);
        }

        .button:hover{
            background-color: rgb(20, 100, 20);
        }

        form{
            margin: auto;
            margin-top:3%;
            margin-bottom:5%;
            width: 50%;
            height: auto;
            border: 1px;
            padding: 10px;
            z-index: 1;
            box-shadow: 2px 2px 5px black;
        }

        @media(max-width:991px){
            html{
                font-size: 50%;
            }
        }

        @media(max-width:768px){
            .form h4{
                font-size: 12px;
            }
            .button{
                font-size: 12px;
            }
            .cancel{
                font-size: 12px;
            }
            .payment{
                width: 100px;
            }
            .logo{
                width: 100px;
            }
        }

        @media(max-width:450px){
            html{
                font-size: 45%;
            }
        }
    </style>
</head>
<body>
    <form action="save_booking.php" method="POST" enctype="multipart/form-data">

        <center>
            <img class="logo" src="image/logo.jpg" alt="">
            <h1>CHADOYVEN CAR RENTAL</h1>
            <h2>Review Your Booking</h2>
        </center>
        <div class="form">
            <h4>Renter: <span style="font-weight: 400;"><?php echo htmlspecialchars($_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname']); ?></span> </h4>
            <h4>Address: <span style="font-weight: 400;"><?php echo htmlspecialchars($_SESSION['user_address']); ?> </span></h4>
            <h4>Email: <span style="font-weight: 400;"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span></h4>
            <h4>Contact: <span style="font-weight: 400;"><?php echo htmlspecialchars($_SESSION['user_contact']); ?></span></h4>
            <br><br>
            <h4>Car Image</h4>
            <img src="../admin/uploads/<?php echo htmlspecialchars($image); ?>" class="image-review">
            <h4>Car Name: <span style="font-weight: 400;"><?php echo htmlspecialchars($car_name); ?></span></h4>
            <h4>Model Year: <span style="font-weight: 400;"><?php echo htmlspecialchars($model); ?></span></h4>
            <h4>Fuel: <span style="font-weight: 400;"><?php echo htmlspecialchars($fuel); ?></span></h4>
            <h4>Seats: <span style="font-weight: 400;"><?php echo htmlspecialchars($seat); ?> people</h4>
            <h4>Price: <span style="font-weight: 400;">₱<?php echo htmlspecialchars($price); ?>.00</span></h4>
            <br><br>
            <h4>From Date: <span style="font-weight: 400;"><?php echo htmlspecialchars($fromDate); ?> </span></h4>
            <h4>Time Pickup: <span style="font-weight: 400;"><?php echo htmlspecialchars($time); ?> </span></h4>
            <h4>Until Date: <span style="font-weight: 400;"><?php echo htmlspecialchars($toDate); ?> </span></h4>
            <h4>Drop of Time: <span style="font-weight: 400;"><?php echo htmlspecialchars($time); ?> </span></h4>
            <h4>Message: <span style="font-weight: 400;"><?php echo htmlspecialchars($message); ?> </span></h4>
            
            <!-- Hidden fields to pass data to the POST request -->
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($_SESSION['user_firstname'] . ' ' . $_SESSION['user_lastname']); ?>">
            <input type="hidden" name="address" value="<?php echo htmlspecialchars($_SESSION['user_address']); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>">
            <input type="hidden" name="contact" value="<?php echo htmlspecialchars($_SESSION['user_contact']); ?>">

            <input type="hidden" name="car_name" value="<?php echo htmlspecialchars($car_name); ?>">
            <input type="hidden" name="model" value="<?php echo htmlspecialchars($model); ?>">
            <input type="hidden" name="fuel" value="<?php echo htmlspecialchars($fuel); ?>">
            <input type="hidden" name="seat" value="<?php echo htmlspecialchars($seat); ?>">
            <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">

            <input type="hidden" name="fromDate" value="<?php echo htmlspecialchars($fromDate); ?>">
            <input type="hidden" name="time" value="<?php echo htmlspecialchars($time); ?>">
            <input type="hidden" name="toDate" value="<?php echo htmlspecialchars($toDate); ?>">
            <input type="hidden" name="message" value="<?php echo htmlspecialchars($message); ?>">
        </div>
        <br><br>
        <div class="button">
            <center>
                <button type="submit">Submit</button>
            </center>
        </div>
        <br>
        <div class="cancel">
            <center>
                <a href="user_vehicle.php" class="btn">Cancel</a>
            </center>
        </div>
    </form>

</body>
</html>