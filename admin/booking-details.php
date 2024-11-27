<?php
include_once 'connect.php';
$conn = connect();
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}


if ($conn && isset($_GET['id'])) {
    $book_id = $_GET['id'];
    $sql = "SELECT * FROM `booking` WHERE `book_id` = '$book_id';";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $booking = $result->fetch_assoc();
        // Display booking details in the HTML below
    } else {
        echo "No details found for this booking.";
    }
} else {
    echo "Database connection failed or invalid ID.";
}




	// Handle AJAX Requests
    if (isset($_GET['action'])) {
        $action = $_GET['action'];
    
        if ($action == 'check_new_bookings') {
            // Count new (unviewed) bookings
            $sql = "SELECT COUNT(*) AS new_bookings FROM booking WHERE viewed = 0";
            $result = $conn->query($sql);
            $newBookings = 0;
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $newBookings = $row['new_bookings'];
            }
            echo json_encode(['new_bookings' => $newBookings]);
            exit;
        }
    
        if ($action == 'fetch_notifications') {
            // Fetch the latest unread bookings
            $sql = "SELECT book_id, name, from_date, created_at FROM booking WHERE viewed = 0 ORDER BY created_at DESC LIMIT 10";
            $result = $conn->query($sql);
    
            $notifications = [];
    
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $createdAt = strtotime($row['created_at']);
                    $timeDiff = time() - $createdAt;
            
                    // Calculate the time difference in minutes
                    $minutesAgo = floor($timeDiff / 60);
            
                    // Prepare the notification data
                    $notifications[] = [
                        'book_id' => $row['book_id'],
                        'name' => htmlspecialchars($row['name']),
                        'date' => ($minutesAgo > 0) ? $minutesAgo . ' minutes ago' : 'just now'
                    ];
                }
            }
            
    
            // Return notifications as JSON
            echo json_encode(['notifications' => $notifications]);
            exit;
        }
    
        if ($action == 'mark_as_read') {
            // Mark all unread bookings as viewed
            $sql = "UPDATE booking SET viewed = 1 WHERE viewed = 0";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $conn->error]);
            }
            exit;
        }
    
        // If action not recognized
        echo json_encode(['status' => 'invalid_action']);
        exit;
    }
    
    // Fetch Dashboard Data
    // Total Cars
    $sql = "SELECT COUNT(*) AS total FROM `vehicles`;";
    $result = $conn->query($sql);
    $totalCars = 0;
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalCars = $row['total'];
    }
    
    // Total Bookings
    $sql2 = "SELECT COUNT(*) AS total FROM `booking`;";
    $result2 = $conn->query($sql2);
    $totalBookings = 0;
    if ($result2 && $result2->num_rows > 0) {
        $row2 = $result2->fetch_assoc();
        $totalBookings = $row2['total'];
    }
    
    // Count New Bookings
    $sql = "SELECT COUNT(*) as new_bookings FROM booking WHERE viewed = 0"; 
    $result = $conn->query($sql);
    $newBookings = 0;
    if ($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $newBookings = $data['new_bookings'];
    }


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['accept']) && !empty($_POST['accept'])) {
            $carId = $conn->real_escape_string($_POST['accept']);
            
            // Prepare and execute the select query to get from_date, until_date, and car_name
            $sqlSelect = "SELECT name, email, price, from_date, until_date, car_name, reference_ID FROM booking WHERE book_id = ?";
            $stmtSelect = $conn->prepare($sqlSelect);
            $stmtSelect->bind_param('i', $carId);
            
            if ($stmtSelect->execute()) {
                // Store the result
                $result = $stmtSelect->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $name = $row['name'];
                    $email = $row['email'];
                    $fromDate = $row['from_date'];
                    $untilDate = $row['until_date'];
                    $carName = $row['car_name'];
                    $reference_id = $row['reference_ID'];
                    $totalPrice = $row['price'];
                    $halfPrice = $totalPrice * 0.50; // 50% of total price
                    $status = 0;
    
                    // Prepare and execute the update query for vehicles table using car_name
                    $sqlUpdateVehicles = "UPDATE vehicles SET status= ?, available = ?, end_date = ? WHERE car_name = ?";
                    $stmtUpdateVehicles = $conn->prepare($sqlUpdateVehicles);
    
                    if (!$stmtUpdateVehicles) {
                        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
                    }
    
                    // Bind parameters for vehicles update
                    $stmtUpdateVehicles->bind_param('ssss', $status, $fromDate, $untilDate, $carName);
    
                    if ($stmtUpdateVehicles->execute()) {
                        // Now update the status in the booking table
                        $sqlUpdateBooking = "UPDATE booking SET status = '1'  WHERE book_id = ?";
                        $stmtUpdateBooking = $conn->prepare($sqlUpdateBooking);
    
                        if (!$stmtUpdateBooking) {
                            die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
                        }
    
                        // Bind parameters for booking update
                        $stmtUpdateBooking->bind_param('i', $carId);
    
                        if ($stmtUpdateBooking->execute()) {
                            // Send an email notification to the user about the reservation acceptance
                            require '../PHPMailer/class.phpmailer.php';
                            require '../PHPMailer/class.smtp.php';
                            require '../PHPMailer/class.pop3.php';
    
                            $mail = new PHPMailer();
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
                            $mail->SMTPAuth = true;
                            $mail->Username = 'bertgumapz@gmail.com'; // SMTP username
                            $mail->Password = 'fbosaubaexglpoeg'; // SMTP password
                            $mail->SMTPSecure = 'tls';
                            $mail->Port = 587;
    
                            $mail->setFrom('bertgumapz@gmail.com', 'Chadoyven Car Rental');
                            $mail->addAddress($email, $name); // Add recipient's email and name
    
                            // Prepare the email content
                            $subject = "Reservation Accepted - Payment Information";
                            $message = "<p>Dear $name,</p>";
                            $message .= "<p>We are pleased to inform you that your reservation for the car <strong>$carName</strong> has been accepted.</p>";
                            $message .= "<p>Your reservation details:</p>";
                            $message .= "<ul>";
                            $message .= "<li><strong>Car Name:</strong> $carName</li>";
                            $message .= "<li><strong>From:</strong> $fromDate</li>";
                            $message .= "<li><strong>Until:</strong> $untilDate</li>";
                            $message .= "<li><strong>Price:</strong> ₱" . number_format($totalPrice, 2) . "</li>";
                            $message .= "<li><strong>Amount to Pay (50%):</strong> ₱" . number_format($halfPrice, 2) . "</li>";
                            $message .= "</ul>";
                            $message .= "<p>Please send 50% of the total price (<strong>₱" . number_format($halfPrice, 2) . "</strong>) via GCash as down payment to proceed with your reservation:</p>";
                            $message .= "<p><strong>GCash Account Number: 09123456789</strong></p>";
                            $message .= "<p>After making the payment, please upload the screenshot of your receipt in the <strong>Manage Booking</strong> section on our website and make sure to use your <strong>Reference ID: $reference_id</strong>.</p>";
                            $message .= "<p>The full payment will be made to the company after you receive the car reserve.</p>";
                            $message .= "<p>If you have any questions, feel free to contact us.</p>";
                            $message .= "<p>Best Regards,<br>Chadoyven Car Rental</p>";
    
                            $mail->Subject = $subject;
                            $mail->Body = $message;
                            $mail->isHTML(true); // Set email format to HTML
    
                            if ($mail->send()) {
                                // Redirect to managebook.php after sending the email
                                $_SESSION['alertMessage'] = "Reservation Accepted successfully!";
                                $_SESSION['alertType'] = "success";
                                header("Location: managebook.php");
                                exit;
                            } else {
                                $errors[] = "Error sending confirmation email: " . $mail->ErrorInfo;
                            }
                        } else {
                            // If something goes wrong, show an error alert
                            $_SESSION['alertMessage'] = "Error updating reservation status: " . $stmtUpdateBooking->error;
                            $_SESSION['alertType'] = "error";
                        }
    
                        $stmtUpdateBooking->close();
                    } else {
                        // If something goes wrong with updating vehicles
                        $_SESSION['alertMessage'] = "Error updating vehicle information: " . $stmtUpdateVehicles->error;
                        $_SESSION['alertType'] = "error";
                    }
    
                    $stmtUpdateVehicles->close();
                } else {
                    // If no booking found
                    $_SESSION['alertMessage'] = "No booking found with the provided ID.";
                    $_SESSION['alertType'] = "error";
                }
            } else {
                // If the select query fails
                $_SESSION['alertMessage'] = "Error fetching booking information.";
                $_SESSION['alertType'] = "error";
            }
            $stmtSelect->close();
        } else {
            echo "No Renter ID provided.";
        }
    }



    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['carId']) && !empty($_POST['carId'])) {
            $carId = $conn->real_escape_string($_POST['carId']);
            
            // Fetch the name and email of the user before deleting the booking
            $sqlSelect = "SELECT name, email, car_name FROM booking WHERE book_id = ?";
            $stmtSelect = $conn->prepare($sqlSelect);
            $stmtSelect->bind_param('i', $carId);
            
            if ($stmtSelect->execute()) {
                $result = $stmtSelect->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $name = $row['name'];
                    $email = $row['email'];
                    $carName = $row['car_name'];
    
                    // Prepare and execute the insert query for cancellation
                    $sqlInsert = "INSERT INTO book_cancel (name, email, car_name, date) VALUES (?, ?, ?, NOW())";
                    $stmtInsert = $conn->prepare($sqlInsert);
                    
                    // Bind the variables (all are strings, hence 'sss')
                    $stmtInsert->bind_param('sss', $name, $email, $carName);
    
                    // Execute the insert query
                    if ($stmtInsert->execute()) {
                        // Proceed to delete the reservation
                        $sqlDelete = "DELETE FROM `booking` WHERE `book_id` = ?";
                        $stmtDelete = $conn->prepare($sqlDelete);
                        $stmtDelete->bind_param('i', $carId);
    
                        if ($stmtDelete->execute()) {
                            // Send email to the user notifying about the cancellation
                            require '../PHPMailer/class.phpmailer.php';
                            require '../PHPMailer/class.smtp.php';
                            require '../PHPMailer/class.pop3.php';
    
                            $mail = new PHPMailer;
                            $mail->isSMTP();                                      // Set mailer to use SMTP
                            $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
                            $mail->SMTPAuth = true;                               // Enable SMTP authentication
                            $mail->Username = 'bertgumapz@gmail.com';             // SMTP username
                            $mail->Password = 'fbosaubaexglpoeg';                    // SMTP password (app-specific password)
                            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                            $mail->Port = 587;                                    // TCP port to connect to
    
                            $mail->setFrom('bertgumapz@gmail.com', 'Chadoyven Car Rental');
                            $mail->addAddress($email, $name);                     // Add recipient's email and name
                            $mail->isHTML(true);                                  // Set email format to HTML
    
                            // Prepare the email content
                            $subject = "Reservation Canceled Due to Invalid Information";
                            $message = "<p>Dear $name,</p>";
                            $message .= "<p>We regret to inform you that your reservation for the car <strong>$carName</strong> has been canceled due to invalid or incomplete information provided.</p>";
                            $message .= "<p>If you have any questions or need assistance, feel free to contact us.</p>";
                            $message .= "<p>Best Regards,<br>Chadoyven Car Rental</p>";
    
                            // Set email subject and body
                            $mail->Subject = $subject;
                            $mail->Body    = $message;
                            $mail->AltBody = strip_tags($message);  // Plain text version for non-HTML mail clients
    
                            // Try to send the email
                            if ($mail->send()) {
                                // Set session variables for the alert
                                $_SESSION['alertMessage'] = "Reservation canceled successfully and user notified!";
                                $_SESSION['alertType'] = "success";
                            } else {
                                $_SESSION['alertMessage'] = "Reservation canceled, but email could not be sent: " . $mail->ErrorInfo;
                                $_SESSION['alertType'] = "error";
                            }
    
                            // Redirect to managebook.php after cancellation
                            header("Location: managebook.php");
                            exit;
                        } else {
                            // If delete fails
                            $_SESSION['alertMessage'] = "Error deleting reservation: " . $stmtDelete->error;
                            $_SESSION['alertType'] = "error";
                        }
    
                        $stmtDelete->close();
                    } else {
                        // If insert fails
                        $_SESSION['alertMessage'] = "Error logging cancellation: " . $stmtInsert->error;
                        $_SESSION['alertType'] = "error";
                    }
    
                    $stmtInsert->close();
                    $stmtSelect->close();
                } else {
                    // If no booking found
                    $_SESSION['alertMessage'] = "No booking found with the provided ID.";
                    $_SESSION['alertType'] = "error";
                }
            } else {
                // If the select query fails
                $_SESSION['alertMessage'] = "Error fetching booking information.";
                $_SESSION['alertType'] = "error";
            }
        } else {
            echo "No Renter ID provided.";
        }
    }
    
    
    
    
    
    


    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['cancel']) && !empty($_POST['cancel'])) {
            $carId = $conn->real_escape_string($_POST['cancel']);
            
            // Prepare and execute the select query to get name and email of the user
            $sqlSelect = "SELECT name, email, car_name FROM booking WHERE book_id = ?";
            $stmtSelect = $conn->prepare($sqlSelect);
            $stmtSelect->bind_param('i', $carId);
            
            if ($stmtSelect->execute()) {
                $result = $stmtSelect->get_result();
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $name = $row['name'];
                    $email = $row['email'];
                    $carName = $row['car_name'];
                    
                    // Prepare and execute the insert query for cancellation
                    $sqlInsert = "INSERT INTO book_cancel (name, email, car_name, date) VALUES (?, ?, ?, NOW())";
                    $stmtInsert = $conn->prepare($sqlInsert);
                    
                    // Bind the variables (all are strings, hence 'sss')
                    $stmtInsert->bind_param('sss', $name, $email, $carName);
                    
                    if ($stmtInsert->execute()) {
                        // Send the email to the user about cancellation approval
                        require '../PHPMailer/class.phpmailer.php';
                        require '../PHPMailer/class.smtp.php';
                        require '../PHPMailer/class.pop3.php';
                        
                        $mail = new PHPMailer;
                        $mail->isSMTP();                                      // Set mailer to use SMTP
                        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
                        $mail->SMTPAuth = true;                               // Enable SMTP authentication
                        $mail->Username = 'bertgumapz@gmail.com';             // SMTP username
                        $mail->Password = 'fbosaubaexglpoeg';                    // SMTP password (app-specific password)
                        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
                        $mail->Port = 587;                                    // TCP port to connect to
                        
                        $mail->setFrom('bertgumapz@gmail.com', 'Chadoyven Car Rental');
                        $mail->addAddress($email, $name);                     // Add recipient's email and name
                        $mail->isHTML(true);                                  // Set email format to HTML
                        
                        // Prepare the email content
                        $subject = "Booking Cancellation Approved";
                        $message = "<p>Dear $name,</p>";
                        $message .= "<p>We regret to inform you that your booking for the car <strong>$carName</strong> has been cancelled as per your request.</p>";
                        $message .= "<p>If you have any further questions or need assistance, feel free to contact us.</p>";
                        $message .= "<p>Best Regards,<br>Chadoyven Car Rental</p>";
                        
                        // Set email subject and body
                        $mail->Subject = $subject;
                        $mail->Body    = $message;
                        $mail->AltBody = strip_tags($message);  // Plain text version for non-HTML mail clients
                        
                        // Try to send the email
                        if ($mail->send()) {
                            // Set session variables for the alert
                            $_SESSION['alertMessage'] = "Cancellation Accepted Successfully!";
                            $_SESSION['alertType'] = "success";
                            
                            // Prepare and execute the delete query for the booking
                            $sqlDelete = "DELETE FROM `booking` WHERE `book_id` = ?";
                            $stmtDelete = $conn->prepare($sqlDelete);
                            $stmtDelete->bind_param('i', $carId);
                            
                            if ($stmtDelete->execute()) {
                                // Redirect to managebook.php
                                header("Location: managebook.php");
                                exit;
                            } else {
                                $_SESSION['alertMessage'] = "Error deleting booking: " . $stmtDelete->error;
                                $_SESSION['alertType'] = "error";
                            }
                            $stmtDelete->close();
                        } else {
                            $_SESSION['alertMessage'] = "Cancellation approved, but email could not be sent: " . $mail->ErrorInfo;
                            $_SESSION['alertType'] = "error";
                        }
                    } else {
                        // If something goes wrong, show an error alert
                        $_SESSION['alertMessage'] = "Error updating cancellation status: " . $stmtInsert->error;
                        $_SESSION['alertType'] = "error";
                    }
                    
                    $stmtInsert->close();
                } else {
                    // If no booking found
                    $_SESSION['alertMessage'] = "No booking found with the provided ID.";
                    $_SESSION['alertType'] = "error";
                }
            } else {
                // If the select query fails
                $_SESSION['alertMessage'] = "Error fetching booking information.";
                $_SESSION['alertType'] = "error";
            }
            
            $stmtSelect->close();
        } else {
            echo "No Renter ID provided.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
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
            font-weight: 400;
            color: black;
            background: rgb(218, 217, 217);
            width: 60px;
            padding: 2px 10px;
            border-radius: 10px;
            cursor: pointer;
        }

        .cancel a{
            color: black;
        }


        .cancel:hover{
            font-weight: 600;
        }

        .button:hover{
            background-color: rgb(20, 100, 20);
        }

        

        .logo{
            display: block;
            margin-left: auto;
            width: 100px;
        }
        .container {
            margin-left: 40%;
            display: grid;
            align-items: center; 
            grid-template-columns: 1fr 1fr 1fr;
            column-gap: 5px;
        }

        hr{
            border: 0;
            height: 3px;
            width: 100%;
            background: #ccc;
            margin: 15px 0 10px;
        }

        .car-image {
            width: 370px; /* Adjust width as needed */
            height: auto; /* Maintain aspect ratio */
            display: block; /* Center the image */
            
        }

        
        .date {
            display: flex; /* Use flexbox for layout */
            justify-content: space-between; /* Space between columns */
            margin: 20px 0; /* Margin around the date section */
        }

        .date-column {
            flex: 1; /* Each column takes equal space */
            margin: 0 10px; /* Space between columns */
        }


        .image {
            display: flex; /* Use flexbox to align items in a row */
            justify-content: space-between; /* Space between columns */
            margin: 20px 0; /* Margin around the image section */
        }

        .image-item {
            text-align: center; /* Center text and images */
            flex: 1; /* Make each column take equal space */
            margin: 0 10px; /* Add some horizontal spacing between items */
        }

        .image-review {
            width: 300px; /* Width of images */
            height: 200px; /* Maintain aspect ratio */
            margin-top: 5px; /* Space between text and images */
        }

        .date h3 {
            flex: 1 1 200px; /* Adjust the base width as needed */
            margin: 0;
            padding: 5px;
            box-sizing: border-box;
        }

        .Details{
            margin: 0; /* Remove margin from Details */
            padding: 0; /* Remove padding from Details */
        }

        .Details h2 {
            margin-bottom: 10px; /* Space below headings */
            color: #333; /* Darker color for headings */
        }

        .Details h3 {
            margin: 5px 0; /* Uniform spacing between details */
            color: #555; /* Slightly lighter color for text */
        }

        button {
            border: none; /* Remove default border */
            padding: 10px 20px; /* Padding for better size */
            border-radius: 5px; /* Rounded corners */
            font-size: 16px; /* Font size */
            cursor: pointer; /* Pointer cursor on hover */
            transition: background-color 0.3s; /* Smooth transition for background color */
            margin: 5px; /* Margin for spacing */
        }

        .accept-button {
            background-color: #28a745; /* Green background */
            color: white; /* White text */
        }

        .cancel-button {
            background-color: #dc3545; /* Red background */
            color: white; /* White text */
        }

        /* Hover effects */
        .accept-button:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .cancel-button:hover {
            background-color: #c82333; /* Darker red on hover */
        }


        /* Notification Dropdown Styles */
		.notification-dropdown {
			display: none;
			position: absolute;
			top: 50px; /* Adjust based on your navbar height */
			right: 20px; /* Adjust based on your navbar padding */
			background-color: white;
			min-width: 300px;
			box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
			border: 1px solid #ccc;
			z-index: 1000;
			max-height: 400px;
			overflow-y: auto;
			border-radius: 4px;
		}

		.notification-dropdown ul {
			list-style-type: none;
			padding: 0;
			margin: 0;
		}

		.notification-dropdown ul li {
			padding: 10px 15px;
			border-bottom: 1px solid #ddd;
		}

		.notification-dropdown ul li:last-child {
			border-bottom: none;
		}

		.notification-dropdown ul li:hover {
			background-color: #f1f1f1;
		}

		.num {
			background-color: red;
			color: white;
			padding: 2px 6px;
			border-radius: 50%;
			position: absolute;
			top: -5px;
			right: -5px;
			font-size: 12px;
		}

        /* Modal overlay style */
		.modal-overlay {
			display: none; /* Hidden by default */
			position: fixed; 
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
			justify-content: center;
			align-items: center;
			z-index: 1000;
		}

		/* Modal content style */
		.modal-content {
			background-color: white;
			padding: 20px;
			border-radius: 8px;
			text-align: center;
			width: 300px;
			box-shadow: 0 5px 15px rgba(0,0,0,.5);
			font-family: Arial, sans-serif;
		}

		/* Modal header text */
		.modal-header {
			font-size: 18px;
			margin-bottom: 10px;
		}

		/* Confirm and cancel button styles */
		.modal-btn {
			padding: 10px 20px;
			margin: 10px;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-size: 16px;
			transition: background-color 0.3s;
		}

		.confirm-btn {
			background-color: #28a745;
			color: white;
		}

		.confirm-btn:hover {
			background-color: #218838;
		}

		.cancel-btn {
			background-color: #dc3545;
			color: white;
		}

		.cancel-btn:hover {
			background-color: #c82333;
		}


        .cancel-button:disabled {
            background-color: gray;
            color: white; /* Optional: change text color if needed */
            cursor: not-allowed; /* Change cursor to indicate the button is disabled */
        }

        
    </style>
</head>
<body>
    <!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bxs-smile'></i>
			<span class="text">Admin</span>
		</a>
		<ul class="side-menu top">
			<li>
				<a href="dashboard.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="#" class="dropdown-toggle">
					<i class='bx bxs-car'></i>
					<span class="text">Cars</span>
					<i class='bx bx-chevron-down'></i>
				</a>
				<ul class="dropdown-menu">
					<li><a href="postcar.php">Post Cars</a></li>
					<li><a href="managecar.php">Manage cars</a></li>
				</ul>
			</li>

			<li>
				<a href="calendar.php">
					<i class='bx bxs-calendar' ></i>
					<span class="text">Available Cars</span>
				</a>
			</li>
			
			<li class="active">
				<a href="managebook.php">
					<i class='bx bxs-book' ></i>
					<span class="text">Manage Bookings</span>
				</a>
			</li>
			<li>
				<a href="service.php">
					<i class='bx bxs-report' ></i>
					<span class="text">Extra Service</span>
				</a>
			</li>
			<li >
				<a href="managereview.php">
					<i class='bx bxs-message' ></i>
					<span class="text">Feedback</span>
				</a>
			</li>
			<li >
				<a href="accepted.php">
					<i class='bx bxs-book' ></i>
					<span class="text">Booking Accepted</span>
				</a>
			</li>

			<li >
				<a href="cancel.php">
					<i class='bx bxs-book' ></i>
					<span class="text">Booking Canceled</span>
				</a>
			</li>

			<li>
				<a href="user.php">
					<i class='bx bxs-report' ></i>
					<span class="text">Reports</span>
				</a>
			</li>
			
		</ul>
		<ul class="side-menu">
			<li>
				<a href="logout.php" class="logout">
					<i class='bx bxs-log-out-circle'></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->

    <section id="content">
        <!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<a href="#" class="nav-link">Categories</a>
			<div class="notification-wrapper">
                <a href="#" class="notification" id="notificationBell">
                    <i class='bx bxs-bell'></i>
                    <?php if ($newBookings > 0) { ?>
                        <span class="num" id="notification-count"><?php echo $newBookings; ?></span>
                    <?php } else { ?>
                        <span class="num" id="notification-count" style="display: none;">0</span>
                    <?php } ?>
                </a>
                <!-- Notification Dropdown -->
                <div class="notification-dropdown" id="notificationDropdown">
                    <ul id="notificationList">
					<?php
						// Fetch latest unread bookings to display initially
						if ($newBookings > 0) {
							$sql = "SELECT book_id, name, created_at FROM booking WHERE viewed = 0 ORDER BY created_at DESC LIMIT 10";
							$result = $conn->query($sql);
							
							if ($result && $result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									// Create the link to managebook.php without using book_id
									echo "<li>
											<a href='managebook.php'>" . htmlspecialchars($row['name']) . " has made a booking just now</a>
										</li>";
								}
							} else {
								echo "<li>No new bookings.</li>";
							}
						} else {
							echo "<li>No new bookings.</li>";
						}
					?>
                    </ul>
                </div>
            </div>
			<a href="#" class="profile">
				<img src="../login/image/user.png">
			</a>
		</nav>
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Manage Booking</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="managebook.php">manage book</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="" href="#">Booking Details</a>
                        </li>
                        <li><i class='bx bx-chevron-right' ></i></li>
                        <li>
                            <a class="active" href="#">renter details</a>
                        </li>
                    </ul>
                </div>

                <div class="brands-box">
                    <div class="Details">
                        <h2>Renter Details</h2>
                        <h3>Name: <strong style="color: black;"><?php echo $booking['name']; ?></strong> </h3>
                        <h3>Email: <strong style="color: black;"><?php echo $booking['email']; ?></strong></h3>
                        <h3>Contact: <strong style="color: black;"><?php echo $booking['contact']; ?></strong></h3>
                        <h3>Address: <strong style="color: black;"><?php echo $booking['address']; ?></strong></h3>
                        <h3>City: <strong style="color: black;"><?php echo $booking['city']; ?></strong></h3>
                        <div class="image">
                            <div class="image-item">
                                <h2>Valid ID</h2>
                                <img src="../uploads/valid_ids/<?php echo $booking['valid_id']; ?>" alt="Valid ID" class="image-review">
                            </div>
                            <div class="image-item">
                                <h2>Selfie</h2>
                                <img src="../uploads/<?php echo $booking['selfie']; ?>" alt="Selfie" class="image-review">
                            </div>
                        </div>
                        <hr>

                        <h2>Car Details</h2>
                        <h3>Car Image:</h3>
                        <img src="uploads/<?php echo $booking['image']; ?>" alt="car" class="image-review">
                        <h3>Car Name: <strong style="color: black;"><?php echo $booking['car_name']; ?></strong></h3>
                        <h3>Car Model: <strong style="color: black;"><?php echo $booking['model']; ?></strong></h3>
                        <hr>

                        <h2>Date</h2>
                        <div class="date">
                            <div class="date-column">
                                <h3>Pick-up Date: <strong style="color: black;"><?php echo $booking['from_date']; ?></strong></h3>
                                <h3>Pick-up Time: <strong style="color: black;"><?php echo $booking['pickup_time']; ?></strong></h3>
                            </div>
                            <div class="date-column">
                                <h3>Drop off Date: <strong style="color: black;"><?php echo $booking['until_date']; ?></strong></h3>
                                <h3>Drop off Time: <strong style="color: black;"><?php echo $booking['drop_time']; ?></strong></h3>
                            </div>
                        </div>
                        <h3 style="color: black;">Total Price: ₱<?php echo number_format($booking['price'], 2); ?></h3>
                        <hr>
                        <?php
                        $status = $booking['status'];
                        $cancel = $booking['cancel']; // Make sure you have the status in the booking array
                        $return = $booking['returned'];
                        $pickup = $booking['pickup'];
                        ?>
                        <div <?php if ($cancel == 0) echo 'style="display: none;"'; ?>>
                            <h3 style="color: black;">Message</h3>
                            <p><?php echo $booking['message']; ?></p>
                            <hr>
                        </div>
                        <?php if ($return == 'Returned') : ?>
                                <p style="color: green; font-size: 20px; font-weight: 600;">This car has already been returned.</p>
                            <?php endif; ?>

                        <?php if ($pickup == 'pickup') : ?>
                            <p <?php if ($return == 'Returned') echo 'style="display:none;"'; ?> style="color: green; font-size: 20px; font-weight: 600;" >This car has already been picked up.</p>
                        <?php endif; ?>
                        
                        <!--  -->
                        

                        <!-- Cancel Reservation Form -->
                        <form method="POST" action="" style="display:inline;" id="cancel" >
                            <input type="hidden" name="cancel" value="<?php echo $booking['book_id']; ?>">
                            <button type="button" class="cancel-button" id="cancelButton" onclick="openModal3()" <?php if ($status == 1) echo 'style="display:none;"'; ?> <?php if ($cancel == 0) echo 'style="display: none;  "'; ?>>Accept Cancellation</button>
                        </form>

                        <!-- Accept Reservation Form -->
                        <form method="POST" action="" style="display:inline;" id="accept" >
                            <input type="hidden" name="accept" value="<?php echo $booking['book_id']; ?>">
                            <button type="button" class="accept-button" onclick="openModal2()" <?php if ($status == 1) echo 'style="display:none;"'; ?> <?php if ($cancel == 1) echo 'style="display: none;  "'; ?>>Accept Reservation</button>
                        </form>

                        <!-- Delete Reservation Form -->
                        <form method="POST" action="" style="display:inline;" id="deleteCarForm">
                            <input type="hidden" name="carId" value="<?php echo $booking['book_id']; ?>">
                            <button type="button" class="accept-button" onclick="openModal()" <?php if ($status == 1) echo 'style="display:none;"'; ?> <?php if ($cancel == 1) echo 'style="display: none;  "'; ?>>Cancel Reservation</button>
                        </form>

                        <!-- ========================================================================================================== -->

                        <form method="POST" action="return.php" style="display:inline;" id="return">
                            <input type="hidden" name="return" value="<?php echo $booking['book_id']; ?>">
                            <?php if ($pickup == 'pickup'): ?>
                                <button type="button" class="accept-button" onclick="openModal4()" 
                                    <?php if ($status == 0 || $return == 'Returned') echo 'style="display:none;"'; ?>>
                                    Return Car
                                </button>
                            <?php endif; ?>
                        </form>

                        <!-- ========================================================================================================== -->

                        <form method="POST" action="pickup.php" style="display:inline;" id="pickup">
                            <input type="hidden" name="pickup" value="<?php echo $booking['book_id']; ?>">

                            <button type="button" class="accept-button" onclick="openModal5()" 
                                <?php if ($status == 0 || $pickup == 'pickup' || $return == 'Returned') echo 'style="display:none;"'; ?>>
                                Car picked up
                            </button>
                            
                        </form>


                    </div>
                </div>
            </div>
        </main>
        <!-- Modal structure -->
        <div class="modal-overlay" id="confirmationModal3">
            <div class="modal-content">
                <div class="modal-header">
                    Cancellation Accepted!
                </div>
                <button class="modal-btn confirm-btn" onclick="confirmcancel()">Okay</button>
            </div>
        </div>

        <!-- Modal structure -->
        <div class="modal-overlay" id="confirmationModal2">
            <div class="modal-content">
                <div class="modal-header">
                    Are you sure you want to Accept this reservation?
                </div>
                <button class="modal-btn confirm-btn" onclick="confirmreservation()">Yes</button>
                <button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </div>

        <!-- Modal structure -->
        <div class="modal-overlay" id="confirmationModal">
            <div class="modal-content">
                <div class="modal-header">
                    Are you sure you want to cancel this reservation?
                </div>
                <button class="modal-btn confirm-btn" onclick="confirmDelete()">Yes</button>
                <button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </div>

        <!-- Modal structure -->
        <div class="modal-overlay" id="confirmationModal4">
            <div class="modal-content">
                <div class="modal-header">
                    Are you sure you the car is return?
                </div>
                <button class="modal-btn confirm-btn" onclick="confirmReturn()">Yes</button>
                <button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </div>

        <!-- Modal structure -->
        <div class="modal-overlay" id="confirmationModal5">
            <div class="modal-content">
                <div class="modal-header">
                    Are you sure you the car has been picked up?
                </div>
                <button class="modal-btn confirm-btn" onclick="confirmpickup()">Yes</button>
                <button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </div>
        
    </section>
    
    <script src="script.js"></script>
    <script>
        // Function to open the modal for accepted reservation
		function openModal3() {
			document.getElementById('confirmationModal3').style.display = 'flex';
		}
		function confirmcancel() {
			document.getElementById('cancel').submit();
		}


        // Function to open the modal for accepted reservation
		function openModal2() {
			document.getElementById('confirmationModal2').style.display = 'flex';
		}

		// Function to close the modal
		function closeModal() {
			document.getElementById('confirmationModal2').style.display = 'none';
		}

		// Function to confirm deletion and submit the form
		function confirmreservation() {
			document.getElementById('accept').submit();
		}



        // Function to open the modal for cancellation 
		function openModal() {
			document.getElementById('confirmationModal').style.display = 'flex';
		}

		// Function to close the modal
		function closeModal() {
			document.getElementById('confirmationModal').style.display = 'none';
		}

		// Function to confirm deletion and submit the form
		function confirmDelete() {
			document.getElementById('deleteCarForm').submit();
		}



        // Function to open the modal for cancellation 
		function openModal4() {
			document.getElementById('confirmationModal4').style.display = 'flex';
		}

		// Function to confirm deletion and submit the form
		function confirmReturn() {
			document.getElementById('return').submit();
		}

        // Function to open the modal for cancellation 
		function openModal5() {
			document.getElementById('confirmationModal5').style.display = 'flex';
		}

		// Function to confirm deletion and submit the form
		function confirmpickup() {
			document.getElementById('pickup').submit();
		}



        document.addEventListener('DOMContentLoaded', function() {
			const notificationBell = document.getElementById('notificationBell');
			const notificationDropdown = document.getElementById('notificationDropdown');
			const notificationList = document.getElementById('notificationList');
			const notificationCount = document.getElementById('notification-count');

			// Function to check for new bookings
			function checkNewBookings() {
				fetch('?action=check_new_bookings')
					.then(response => response.json())
					.then(data => {
						console.log('New bookings response:', data); // Debugging line
						const count = data.new_bookings;
						if (count > 0) {
							notificationCount.textContent = count;
							notificationCount.style.display = 'inline';
						} else {
							notificationCount.style.display = 'none';
						}
					})
					.catch(error => console.error('Error:', error));
			}

			// Function to fetch notifications
			function fetchNotifications() {
				fetch('?action=fetch_notifications')
					.then(response => response.json())
					.then(data => {
						console.log('Notifications fetched:', data); // Debugging line
						// Clear existing notifications
						notificationList.innerHTML = '';

						if (data.notifications.length > 0) {
							data.notifications.forEach(notification => {
								const li = document.createElement('li');
								li.innerHTML = `${notification.message} <br><small>${notification.date}</small>`;
								notificationList.appendChild(li);
							});
						} else {
							const li = document.createElement('li');
							li.textContent = 'No new bookings.';
							notificationList.appendChild(li);
						}
					})
					.catch(error => console.error('Error:', error));
			}

			// Function to mark notifications as read
			function markAsRead() {
				fetch('?action=mark_as_read', {
					method: 'POST',
				})
				.then(response => response.json())
				.then(data => {
					console.log('Mark as read response:', data); // Debugging line
					if (data.status === 'success') {
						notificationCount.style.display = 'none';
					}
				})
				.catch(error => console.error('Error:', error));
			}

			// Toggle notification dropdown
			notificationBell.addEventListener('click', function(e) {
				e.preventDefault();
				if (notificationDropdown.style.display === 'none' || notificationDropdown.style.display === '') {
					fetchNotifications();
					notificationDropdown.style.display = 'block';
					markAsRead();
					checkNewBookings(); // Update the count after marking as read
				} else {
					notificationDropdown.style.display = 'none';
				}
			});

			// Periodically check for new bookings every 10 seconds
			setInterval(checkNewBookings, 10000);

			// Initial check
			checkNewBookings();
		});


		document.addEventListener('click', function(event) {
			// Check if the clicked element is a link inside the notification dropdown
			if (event.target.closest('.notification-dropdown a')) {
				// Allow the default action (navigation)
				return;
			}
			
			// Existing code to hide the dropdown
			if (!event.target.closest('.notification-wrapper')) {
				document.getElementById('notificationDropdown').style.display = 'none';
			}
		});



    </script>
</body>
</html>