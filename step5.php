<?php
include_once 'connect.php';
$conn = connect();
session_start(); 
// Assuming you already have a connection to the database established

// Get email and booking_id from URL parameters
$email = isset($_GET['email']) ? $_GET['email'] : '';
$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';

// Prepare and execute the SQL statement
$sql = "SELECT * FROM booking WHERE email = ? AND reference_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $email, $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Valid booking found, fetch details
    $booking = $result->fetch_assoc();

    // Extract booking details
    $id = $booking['book_id']; // Use book_id from booking table
    $carName = $booking['car_name'];
    $carImage = $booking['image'];
    $carModel = $booking['model'];
    $startDate = $booking['from_date'];
    $pickupTime = $booking['pickup_time'];
    $endDate = $booking['until_date'];
    $dropTime = $booking['drop_time'];
    $totalAmount = $booking['price'];
    $amountToPay = $totalAmount * 0.5;

    // Fetch the booking status
    $status = $booking['status']; // 1 for accepted, 0 for pending
    $disabled = ($status == 1) ? 'disabled' : '';

    $cancel = $booking['cancel'];
    $cancel_disabled = ($cancel == 1) ? 'disabled' : '';
} else {
    $_SESSION['warning'] = "Invalid email or reference ID. Please try again.";
    header("Location: manage.php");
    exit();
}


// if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['carId']) && isset($_POST['message'])) {
//     $stmt = $conn->prepare("UPDATE booking SET message = ?, cancel = 0 WHERE reference_ID = ?");
//     $stmt->bind_param("ss", $message, $bookingId);
//     $updateResult = $stmt->execute();
//     $uploadMessage = $updateResult ? "The cancellation submit." : "cancellation error.";
//     $stmt->close();
// }
// if ($_SERVER['REQUEST_METHOD']) {
//     if (isset($_POST['carId']) && !empty($_POST['carId'])) {
// 		$brand_id = $conn->real_escape_string($_POST['carId']);

// 		$stmt = $conn->prepare("UPDATE booking SET message = ?, cancel = 0 WHERE reference_ID = ?");
//         $stmt->bind_param("ss", $message, $brand_id);
//         $updateResult = $stmt->execute();
//         $uploadMessage = $updateResult ? "The cancellation submit." : "cancellation error.";
//         $stmt->close();
// 	} else {
// 		echo "No Renter ID provided.";
// 	}
// } else {
//     echo json_encode(['success' => false, 'message' => 'Invalid request.']);
// }



// Initialize a variable to hold upload status messages
$uploadMessage = '';
$status2 = 1;

// Handle file upload and payment record creation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Upload receipt
    if (isset($_FILES['receipt']) && $_FILES['receipt']['error'] == 0) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['receipt']['name']);
        $uploadFilePath = $uploadDir . $fileName;

        // Check file type and size
        $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes) && $_FILES['receipt']['size'] <= 5000000) {
            if (move_uploaded_file($_FILES['receipt']['tmp_name'], $uploadFilePath)) {
                // Insert payment record
                $stmt = $conn->prepare("INSERT INTO payment (book_id, image, price, status, date) VALUES (?, ?,?,?, NOW())");
                $stmt->bind_param("sssi", $id, $fileName, $totalAmount, $status2); // Bind book_id and uploaded file name

                // Execute and check for success
                if ($stmt->execute()) {
                    $uploadMessage = "The file has been uploaded successfully and payment record created.";
                } else {
                    $uploadMessage = "Error inserting payment record into the database.";
                }

                $stmt->close();
            } else {
                $uploadMessage = "There was an error uploading your file.";
            }
        } else {
            $uploadMessage = "Invalid file type or file too large.";
        }
    } else {
        $uploadMessage = "Error: " . ['error'];
    }
}

// Check if payment record exists for the current book_id
$paymentCheckQuery = "SELECT * FROM payment WHERE book_id = ?";
$paymentStmt = $conn->prepare($paymentCheckQuery);
$paymentStmt->bind_param("s", $id); // Bind the book_id
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();

$paymentExists = $paymentResult->num_rows > 0; // Determine if payment record exists


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
</head>
<style>
    
    .content-container {
        display: flex;
        
        min-height: 100vh; /* Ensure full page height for vertical centering */
        padding: 2rem;
        background-color: #f9f9f9;
    }

    .booking-details-box1 {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
        text-align: center;
        padding: 2rem;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 400px;
        width: 100%;
        min-height: 500px; /* Use min-height to ensure enough space */
        position: relative;
        padding-bottom: 100px; /* Ensure space for the button */
    }

    .gcash-payment img {
        display: block;
        margin: 0 auto 1.5rem;
    }

    .upload-input {
        display: block;
        margin: 1rem auto;
        padding: 0.5rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1.6rem;
        text-align: center;
        width: 70%;
    }

    /* Position Submit Button at the Bottom */
    .submit-btn {
        width: 100%;
        background-color: var(--yellow);
        color: white;
        padding: 1rem;
        font-size: 1.6rem;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        position: absolute;
        bottom: 20px; /* Position 20px from the bottom of the box */
        left: 50%;
        transform: translateX(-50%);
    }

    .submit-btn:hover {
        background-color: var(--light-yellow);
        color: var(--black);
    }
    .lastbtn {
        padding: 10px 20px;
        background-color: var(--yellow); /* Default blue color */
        color: white;
        border: none;
        cursor: pointer;
    }

    .lastbtn[disabled] {
        background-color: gray; /* Gray color when disabled */
        cursor: not-allowed;    /* Show 'not-allowed' cursor when disabled */
        opacity: 0.6;           /* Slightly transparent */
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
			width: 500px;
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
        .message{
            border: 2px solid black;
            width: 100%;
            height: 10%;
        }
        .message2 {
            margin-top: 20px; 
            color: green;
            font-size: 15px; 
        }
        /* Main Button */
        .open-modal-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .open-modal-btn:hover {
            background-color: #0056b3;
        }

        /* Modal Background */
        .modal {
            display: none; /* Hidden initially */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto;
            padding: 15px;
            z-index: 1000;
        }

        /* Modal Dialog */
        .modal-dialog {
            background-color: #fff;
            width: 100%;
            max-width: 400px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease;
            margin: auto; /* Centering */
            margin-top: 10%;
        }

        /* Modal Header */
        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
        }

        .close {
            background: none;
            border: none;
            font-size: 24px;
            color: #000;
            cursor: pointer;
        }

        /* Modal Body */
        .modal-body {
            padding: 15px;
            font-size: 14px;
        }

        .modal-body textarea {
            width: 100%;
            height: 80px;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: none;
        }

        /* Modal Footer */
        .modal-footer {
            padding: 10px;
            border-top: 1px solid #e5e5e5;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }
        #successModal .modal-body {
            font-size: 16px;
            color: #333; /* Success message color */
            text-align: center; /* Center text */
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        



</style>
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


   <section>
    
    <div class="content-container">
        
        <!-- Booking Details Box Below -->
        <div class="booking-details-box1">
        <h4 class="car-name">Pay now</h4>

                
        <!-- Payment Summary Table -->
        <div class="payment-summary">
        
            
        </div>

        <div class="gcash-payment">
            <img src="image/gcashl.png" alt="GCash Logo" class="gcash-logo">
            <!-- Display message for existing payment or upload result -->
            <?php if ($paymentExists): ?>
                <div class="message2">Payment has already been made.</div>
            <?php else: ?>
                <?php if ($uploadMessage): ?>
                    <div class="message2" aria-live="polite"><?php echo $uploadMessage; ?></div>
                <?php endif; ?>
            <?php endif; ?>

                <!-- Payment upload form -->
                <?php if ($cancel == 1): ?>
                    <!-- If the reservation is accepted, display a message instead of the button -->
                    <p class="notification-message" style="color: red; font-size: 20px">Booking Cancelled.</p>
                <?php else: ?>
                    <form action="" method="POST" enctype="multipart/form-data" onsubmit="return validateFile()">
                        <label for="upload-receipt" class="upload-label">Upload Screenshot of Receipt</label>
                        <input type="file" id="upload-receipt" name="receipt" class="upload-input" accept="image/*" required>

                        <!-- Error message for invalid file -->
                        <div id="fileError" style="color:red; display:none; font-size:15px;">Invalid file type or file too large.</div>
                        <!-- Submit Button -->
                        <button type="submit" class="btn submit-btn">Submit Payment</button>
                    </form>
                <?php endif; ?>
        </div>

    </div>

        <!-- Car Box -->
        <div class="car-details-container"> 
            <div class="car-details-box">
                <div class="booking-details">
                    <h3>Order Summary</h3><br>
                    <?php if ($status == 1): ?>
                        <!-- If the reservation is accepted, display a message instead of the button -->
                        <p class="notification-message" style="color: green; font-size: 20px"><strong style="color: black; font-size: 20px">Message:</strong> <br> Your reservation has been accepted. You cannot cancel it anymore.</p>
                    <?php endif; ?>
                    <?php if ($cancel == 1): ?>
                        <!-- If the reservation is accepted, display a message instead of the button -->
                        <p class="notification-message" style="color: red; font-size: 20px"><strong style="color: black; font-size: 20px">Message:</strong> <br>Booking Cancelled.</p>
                    <?php endif; ?>
                    
                    <p>Car Image: </p>
                    <img style="width: 50%;" src="admin/uploads/<?php echo htmlspecialchars($carImage); ?>" alt="<?php echo htmlspecialchars($carName); ?>">
                    <p>Car Name: <strong><?php echo htmlspecialchars($carName); ?></strong></p>
                    <p>Car Model: <strong><?php echo htmlspecialchars($carModel); ?></strong></p>
                    <p>Start Date: <strong><?php echo htmlspecialchars($startDate); ?></strong></p>
                    <p>Pick-up Time: <strong><?php echo htmlspecialchars($pickupTime); ?></strong></p>
                    <p>End Date: <strong><?php echo htmlspecialchars($endDate); ?></strong></p>
                    <p>Drop-off Time: <strong><?php echo htmlspecialchars($dropTime); ?></strong></p>
                    <p>Total Amount: <strong>₱<?php echo htmlspecialchars(number_format($totalAmount, 2)); ?></strong></p>

                    <?php if ($status == 0): ?>
                        <?php if ($cancel == 0): ?>
                            <button type="button" class="lastbtn" onclick="openCancelModal()">Cancel Reservation</button>
                        <?php endif; ?>
                    <?php endif; ?>


                    <!-- Modal structure -->
                    <div id="cancelModal" class="modal" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h5 class="modal-title">Cancel Reservation</h5>
                                    <button type="button" class="close" onclick="closeModal()">&times;</button>
                                </div>
                                
                                <!-- Modal Body -->
                                <div class="modal-body">
                                    <p>Please provide a reason for cancellation:</p>
                                    <textarea id="cancelReason" placeholder="Enter cancellation reason..." class="form-control"></textarea>
                                </div>
                                
                                <!-- Modal Footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" onclick="closeModal()">No</button>
                                    <button type="button" class="btn btn-primary" onclick="submitCancellation()">Yes</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Success Modal -->
                    <div class="modal" id="successModal" style="display: none;">
                        <div class="modal-dialog">
                            <div class="modal-header">
                                <h5 class="modal-title">Success</h5>
                                <button type="button" class="close" onclick="closeSuccessModal()">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p>Your cancellation has been saved successfully!</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" onclick="closeSuccessModal()">OK</button>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        
    </div>
   </section>
    
<br><br>
    
</body>
<section class="footer">
    <div class="credit"> create by me web designer | all rights reserved!</div>
</section>

<script src="https://unpkg.com/swiper@7/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>
<script>
    function openCancelModal() {
    document.getElementById("cancelModal").style.display = "flex"; // Show the cancellation modal
}

// Function to close the modal
function closeModal() {
    document.getElementById("cancelModal").style.display = "none"; // Hide the cancellation modal
}



    function openSuccessModal(message) {
        document.getElementById('successMessage').innerText = message;
        document.getElementById('successModal').style.display = 'flex';
    }

    function closeSuccessModal() {
        document.getElementById('successModal').style.display = 'none';
    }
    function submitCancellation() {
        var reason = document.getElementById("cancelReason").value;
        var bookingId = "<?php echo $booking_id; ?>"; // Get booking_id from PHP

        // AJAX request to send cancellation reason
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "cancel_booking.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                showSuccessModal(); // Show the success modal instead of alert
                closeModal(); // Close the cancellation modal after submission
                location.reload();
            }
        };
        xhr.send("booking_id=" + bookingId + "&reason=" + encodeURIComponent(reason));
    }

    // Function to show the success modal
    function showSuccessModal() {
        document.getElementById("successModal").style.display = "flex"; // Show the success modal
    }

    // Function to close the success modal
    function closeSuccessModal() {
        document.getElementById("successModal").style.display = "none"; // Hide the success modal
    }





    function validateFile() {
    const fileInput = document.getElementById('upload-receipt');
    const fileError = document.getElementById('fileError');
    fileError.style.display = 'none';
    const allowedTypes = ['image/jpeg', 'image/png'];
    const maxSize = 5000000; // 5 MB

    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        if (!allowedTypes.includes(file.type) || file.size > maxSize) {
            fileError.style.display = 'block';
            return false;
        }
    }
    return true;
}

</script>


</body>
</html>