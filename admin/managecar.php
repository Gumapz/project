<?php
include_once 'connect.php';
$con = connect();
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
// Handle AJAX Requests
if (isset($_GET['action'])) {
	$action = $_GET['action'];

	if ($action == 'check_new_bookings') {
		// Count new (unviewed) bookings
		$sql = "SELECT COUNT(*) AS new_bookings FROM booking WHERE viewed = 0";
		$result = $con->query($sql);
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
		$result = $con->query($sql);

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
		if ($con->query($sql) === TRUE) {
			echo json_encode(['status' => 'success']);
		} else {
			echo json_encode(['status' => 'error', 'message' => $con->error]);
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
$result = $con->query($sql);
$totalCars = 0;
if ($result && $result->num_rows > 0) {
	$row = $result->fetch_assoc();
	$totalCars = $row['total'];
}

// Total Bookings
$sql2 = "SELECT COUNT(*) AS total FROM `booking`;";
$result2 = $con->query($sql2);
$totalBookings = 0;
if ($result2 && $result2->num_rows > 0) {
	$row2 = $result2->fetch_assoc();
	$totalBookings = $row2['total'];
}

// Count New Bookings
$sql = "SELECT COUNT(*) as new_bookings FROM booking WHERE viewed = 0"; 
$result = $con->query($sql);
$newBookings = 0;
if ($result && $result->num_rows > 0) {
	$data = $result->fetch_assoc();
	$newBookings = $data['new_bookings'];
}


// Handle the status update logic if carId and status are passed in the URL
if (isset($_GET['carId'])) {
    $carId = $_GET['carId'];
    $status = 1;

        // SQL query to update the car status, delete start and end dates, and set return and update dates
        $sql = "UPDATE vehicles SET status = ?, available = NULL, end_date = NULL WHERE id = ?";

        // Prepare and execute the query
        if ($stmt = $con->prepare($sql)) {
            // Bind parameters (status as string, return_date and update_date as strings, carId as string)
            $stmt->bind_param("is", $status, $carId);
            if ($stmt->execute()) {
                $alertMessage = "The Car is Available Now!";
                $alertType = "success";
            } else {
                // If something goes wrong, show an error alert
                $alertMessage = "Car remains unavailable.";
                $alertType = "error";
            }
        } else {
            // Handle preparation error
            $alertMessage = "Failed to prepare SQL statement.";
            $alertType = "error";
        }
}




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['carId']) && !empty($_POST['carId'])) {
        $carId = $con->real_escape_string($_POST['carId']);
        
        // Prepare and execute the delete query
        $sql = "DELETE FROM `vehicles` WHERE `id` = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('i', $carId);
        
        if ($stmt->execute()) {
            $alertMessage = "Car deleted successfully!";
			$alertType = "success";
		} else {
			// If something goes wrong, show an error alert
			$alertMessage = "Error deleting car.";
			$alertType = "error";
		}
        
    } else {
        echo "No car ID provided.";
    }
    
} else {
    echo "Invalid request method.";
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" type="image/x-icon" href="../image/logo.jpg">
	<title>AdminHub</title>
	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="style.css">

	<style>
		.brands-box {
			padding: 20px;
			background-color: #f9f9f9;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
			margin: 20px 0;
			max-width: 100%;
			overflow-x: auto;
			box-sizing: border-box; /* Ensure padding and border are included in the element's total width and height */
		}

		.brands-table {
			width: 100%;
			max-width: 100%;
			border-collapse: collapse;
		}
		.brands-table th{
			text-align: center;
		}

		.dropdown {
            position: relative;
            display: inline-block;
        }
        /* Dropdown Button */
		.dropdown-button {
			background-color: #4CAF50; 
			color: white; /* White text */
			padding: 10px 16px; /* Padding */
			font-size: 16px; /* Font size */
			border: none; /* No border */
			cursor: pointer; /* Pointer cursor */
			border-radius: 5px; /* Rounded corners */
			transition: background-color 0.3s; /* Transition effect */
		}

		/* Dropdown Content (Hidden by Default) */
		.dropdown-content {
			display: none; /* Hidden by default */
			position: absolute; /* Position the dropdown */
			background-color: white; /* White background */
			min-width: 100px; /* Minimum width */
			box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); /* Shadow effect */
			z-index: 1; /* On top of other elements */
			border-radius: 5px; /* Rounded corners */
		}

		/* Links inside the dropdown */
		.dropdown-content a {
			color: black; /* Black text */
			padding: 12px 16px; /* Padding */
			text-decoration: none; /* No underline */
			display: block; /* Block display */
			transition: background-color 0.3s; /* Transition effect */
		}

		/* Change background color on hover */
		.dropdown-content a:hover {
			background-color: #f1f1f1; /* Light grey background */
		}

		/* Edit Button */
		.edit-button {
			display: inline-block;
			padding: 10px 20px;
			text-align: center;
			text-decoration: none;
			background-color: #4CAF50;
			color: black;
			border-radius: 5px;
			font-size: 14px;
			transition: background-color 0.3s, cursor 0.3s;
			margin-left:5px;
			margin-top:5px;
			height: 40px;
			width: 90px; /* Adjust the width as needed */
			box-sizing: border-box; /* Ensures padding is included in the width */
		}

		

		/* Show dropdown content on button click */
		.show {
			display: block; /* Show the dropdown */
		}

		.pagination {
			text-align: center;
			margin: 20px 0;
		}

		.pagination a {
			color: #007bff;
			padding: 8px 16px;
			text-decoration: none;
			border: 1px solid #ddd;
			border-radius: 4px;
			margin: 0 2px;
		}

		.pagination a.active {
			background-color: #007bff;
			color: white;
			border: 1px solid #007bff;
		}

		.pagination a:hover:not(.active) {
			background-color: #ddd;
		}

		.alert {
			padding: 15px;
			margin-bottom: 20px;
			border: 1px solid transparent;
			border-radius: 4px;
			position: relative;
		}

		.alert-success {
			color: #155724;
			background-color: #d4edda;
			border-color: #c3e6cb;
		}

		.alert-error {
			color: #721c24;
			background-color: #f8d7da;
			border-color: #f5c6cb;
		}

		.alert-dismissible .close {
			position: absolute;
			top: 0;
			right: 10px;
			color: inherit;
			border: none;
			background: none;
			font-size: 20px;
			cursor: pointer;
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

		.delete-btn {
			background-color: #dc3545;
			color: black;
			margin-left:5px;
			margin-top:5px;
			margin-bottom:5px;
			border: none;
			border-radius: 5px;
			font-size: 16px;
			cursor: pointer;
			transition: background-color 0.3s;
		}

		.delete-btn:hover {
			background-color: #c82333;
		}

		/* Container styles */
		.button-container {
			text-align: center; /* Centers the button */
		}

		/* Button styles */
		.status-button {
			display: inline-block;
			padding: 10px 20px;
			text-align: center;
			text-decoration: none;
			color: black;
			border-radius: 5px;
			font-size: 14px;
			transition: background-color 0.3s, cursor 0.3s;
			margin-left:5px;
			margin-top:5px;
			height: 40px;
			width: 90px; /* Adjust the width as needed */
			box-sizing: border-box; /* Ensures padding is included in the width */
		}


		/* Styles for the active button */
		.status-button.active {
			color: black;
			background-color: #28a745; /* Green */
			cursor: pointer;
		}

		/* Styles for the disabled button */
		.status-button.disabled {
			background-color: #6c757d; /* Gray */
			cursor: not-allowed;
			pointer-events: none;
		}


		/* Professional Save Button */
		.search button[type="submit"] {
			background-color: #4CAF50; /* Professional green color */
			color: white; /* White text for contrast */
			padding: 8px 18px; /* Spacing for a more substantial button */
			border: none; /* Remove default borders */
			border-radius: 5px; /* Slightly rounded corners for a modern feel */
			font-size: 14px; /* Slightly larger text for readability */
			font-weight: bold; /* Emphasize button text */
			cursor: pointer; /* Pointer cursor to indicate clickable */
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
			transition: background-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effects */
		}

		.search button[type="submit"]:hover {
			background-color: #45a049; /* Slightly darker green on hover */
			box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15); /* Enhanced shadow on hover */
		}

		.search button[type="submit"]:active {
			background-color: #3e8e41; /* Darker green when clicked */
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Reduced shadow on active state */
			transform: translateY(2px); /* Slight movement to simulate press effect */
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
			<li class="active">
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
			
			<li>
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



	<!-- CONTENT -->
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
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Manage Cars</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">Cars</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Manage Cars</a>
						</li>
					</ul>
				</div>



                <div class="brands-box">
					<h2>Listed Cars</h2>
					<?php if (!empty($alertMessage)): ?>
						<div class="alert alert-<?php echo $alertType; ?> alert-dismissible">
							<?php echo $alertMessage; ?>
							<button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
						</div>
					<?php endif; ?>

					<?php
						// Check for alert message
						if (isset($_SESSION['alertMessage'])) {
							$alertType = $_SESSION['alertType'];
							$alertMessage = $_SESSION['alertMessage'];
							echo "<div class='alert alert-$alertType'>$alertMessage</div>";
							// Clear the message after displaying
							unset($_SESSION['alertMessage']);
							unset($_SESSION['alertType']);
						}
					?>
					<div class="search-container">
						<form class="search" method="GET" action="">
							<input type="text" name="search" class="search-bar" placeholder="Search by car name:">
							<button type="submit">Search</button>
						</form>

					</div>

					<?php
						include_once 'connect.php';
						$con = connect();

						// Define the number of records per page
						$recordsPerPage = 5;

						// Get the current page number from the query string or default to 1
						$pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;
						$offset = ($pageNumber - 1) * $recordsPerPage;

						// Get the search query from the query string
						$searchQuery = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

						// Build the SQL query with a search filter
						$sql = "SELECT COUNT(*) as total FROM `vehicles` WHERE `car_name` LIKE '%$searchQuery%'";
						$result = $con->query($sql);
						$totalRecords = $result->fetch_assoc()['total'];

						// Calculate the total number of pages
						$totalPages = ceil($totalRecords / $recordsPerPage);

						// Fetch the records for the current page with search filter
						$sql = "SELECT * FROM `vehicles` WHERE `car_name` LIKE '%$searchQuery%' ORDER BY `id` DESC LIMIT $offset, $recordsPerPage";
						$result = $con->query($sql);
					?>

						<table class="brands-table" border="1">
							<thead>
								<tr>
									<th rowspan="2">Image</th>
									<th rowspan="2">Car Name</th>
									<th rowspan="2">Brand</th>
									<th rowspan="2">Overview</th>
									<th rowspan="2">Price</th>
									<th rowspan="2">Seat</th>
									<th rowspan="2">Fuel</th>
									<th rowspan="2">Model</th>
									<th rowspan="2">Accessories</th>
									<th colspan="2">Unavailable Date</th>
									<th rowspan="2" colspan="3" style="text-align:center;">Action</th>
								</tr>
								<tr>
									<th>Start</th>
									<th>End</th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ($result && $result->num_rows > 0) {
									foreach ($result as $r) {
										?>
										<tr class="gradeX">
											<td class="center hidden-phone">
												<figure class="image rounded">
													<img style="height: 60px;width: 70px;border-radius: 10px; border: 1px solid darkgray;" src="uploads/<?php echo $r['image']; ?>" alt="Item Image">
												</figure>
											</td>
											<td><?php echo $r['car_name']; ?></td>
											<td><?php echo $r['car_brand']; ?></td>
											<td><?php echo $r['overview']; ?></td>
											<td>₱<?php echo number_format($r['price'], 2); ?></td>
											<td><?php echo $r['seat']; ?></td>
											<td><?php echo $r['fuel']; ?></td>
											<td><?php echo $r['model']; ?></td>
											<td><?php echo $r['accessories']; ?></td>
											<td><?php echo $r['available']; ?> </td>
											<td><?php echo $r['end_date']; ?> </td>
											<td colspan="3">
												<div class="dropdown">
													<button class="dropdown-button" onclick="toggleDropdown(event)">Actions</button>
													<div class="dropdown-content">
														<!-- Link to set the car as "available" -->
														<?php
														$status = $r['status']; // Assuming $r['status'] holds the current status of the car
														?>

														<a href="managecar.php?carId=<?php echo $r['id']; ?>&status=1" 
														class="status-button <?php echo $status == 1 ? 'disabled' : 'active'; ?>">
														Available
														</a>


														<!-- Edit -->
														<a class="edit-button" href="edit-car.php?id=<?php echo $r['id']; ?>">Edit Car</a>

														<!-- Delete Form -->
														<form method="POST" action="" style="display:inline;" id="deleteCarForm">
															<input type="hidden" name="carId" value="<?php echo $r['id']; ?>">
															<button type="button" class="delete-btn" onclick="openModal()">Delete Car</button>
														</form>
													</div>
												</div>
											</td>
										</tr>
										<?php
									}
								} else {
									echo "<tr><td colspan='13'>No cars found.</td></tr>";
								}
								?>
							</tbody>
						</table>

						<!-- Pagination controls -->
						<div class="pagination">
							<?php if ($pageNumber > 1): ?>
								<a href="?page=<?php echo $pageNumber - 1; ?>&search=<?php echo htmlspecialchars($searchQuery); ?>">&laquo; Previous</a>
							<?php endif; ?>
							
							<?php for ($i = 1; $i <= $totalPages; $i++): ?>
								<a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($searchQuery); ?>" <?php if ($i == $pageNumber) echo 'class="active"'; ?>><?php echo $i; ?></a>
							<?php endfor; ?>
							
							<?php if ($pageNumber < $totalPages): ?>
								<a href="?page=<?php echo $pageNumber + 1; ?>&search=<?php echo htmlspecialchars($searchQuery); ?>">Next &raquo;</a>
							<?php endif; ?>
						</div>


				<!-- Modal structure -->
				<div class="modal-overlay" id="confirmationModal">
					<div class="modal-content">
						<div class="modal-header">
							Are you sure you want to delete this car?
						</div>
						<button class="modal-btn confirm-btn" onclick="confirmDelete()">Yes</button>
						<button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
					</div>
				</div>

				
				
				
				
			</div>
        </main>
    </section>
    <script src="script.js"></script>
	<script>
		// Function to open the modal
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




		document.getElementById('statusBtn').addEventListener('click', function() {
			// Hide the status button
			this.style.display = 'none';
			
			// Show the date input and update button
			document.getElementById('availabilityDate').style.display = 'inline-block';
			document.getElementById('updateBtn').style.display = 'inline-block';
		});


		function toggleDropdown(event) {
			event.stopPropagation();
			
			var dropdownContent = event.target.nextElementSibling;
			var isVisible = dropdownContent.style.display === 'block';
			closeAllDropdowns();
			dropdownContent.style.display = isVisible ? 'none' : 'block';
			
			window.onclick = function(event) {
				if (!event.target.matches('.dropdown-button')) {
					closeAllDropdowns();
				}
			};
		}
		
		function closeAllDropdowns() {
			var dropdowns = document.querySelectorAll('.dropdown-content');
			dropdowns.forEach(function(dropdown) {
				dropdown.style.display = 'none';
			});
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
