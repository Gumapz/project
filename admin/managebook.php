<?php
include_once 'connect.php';
$conn = connect();
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="style.css">
	<link rel="icon" type="image/x-icon" href="../image/logo.jpg">
	<title>AdminHub</title>

	<style>
		.brands-box {
			padding: 20px;
			background-color: #f9f9f9;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
			margin: 20px 0;
			max-width: 100%;
			overflow-x: auto;
			box-sizing: border-box; 
		}

		.brands-table {
			width: 100%;
			max-width: 100%;
			border-collapse: collapse;
		}
		.brands-table th{
			text-align: center;
		}
		.book-details{
			background-color: #4CAF50;
			padding:5px 5px;
			border-radius: 10px;
			color: white;
			font-size: 15px;
			font-weight: 500;
		}
		.book-details:hover{
			background:#059212;
			color: white;
			font-weight: 600;
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

		.modal {
			display: none; /* Hidden by default */
			position: fixed; /* Stay in place */
			z-index: 1050; /* Sit on top */
			left: 0;
			top: 0;
			width: 100%; /* Full width */
			height: 100%; /* Full height */
			overflow: auto; /* Enable scroll if needed */
			background-color: rgba(0, 0, 0, 0.7); /* Dark overlay */
		}

		.modal-content {
			background-color: #fff; /* White background */
			margin: 15% auto; /* 15% from the top and centered */
			padding: 20px; /* Padding around the content */
			border: 1px solid #888; /* Border around the modal */
			width: 80%; /* Set to your desired width */
			max-width: 600px; /* Maximum width */
			border-radius: 5px; /* Rounded corners */
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Soft shadow */
		}

		.modal-image {
			display: block;
			margin: auto; /* Center the image */
			width: 100%; /* Responsive */
			height: auto; /* Maintain aspect ratio */
		}

		.close {
			color: #aaa; /* Light color for the close button */
			float: right; /* Position at the top right */
			font-size: 28px; /* Size of the close button */
			font-weight: bold; /* Bold font */
		}

		.close:hover,
		.close:focus {
			color: black; /* Dark color on hover */
			text-decoration: none; /* No underline */
			cursor: pointer; /* Pointer cursor */
		}

		.caption {
			color: #333; /* Dark color for the caption */
			text-align: center; /* Center the caption */
			margin-top: 10px; /* Space above the caption */
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
			<li >
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
					<h1>Manage Booking</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">manage book</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Manage book</a>
						</li>
					</ul>
				</div>

                <div class="brands-box">
					<h2>Booking Info</h2>
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
							<input type="text" name="search" class="search-bar" placeholder="Search by renter name:">
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

						// Build the SQL query to count total records for pagination
						$sql = "SELECT COUNT(*) as total FROM `booking`
								WHERE status = 0 AND name LIKE '%$searchQuery%'";
						$result = $con->query($sql);
						$totalRecords = $result->fetch_assoc()['total'];

						// Calculate the total number of pages
						$totalPages = ceil($totalRecords / $recordsPerPage);

						// Fetch the records for the current page with search filter and status = 1
						$sql = "SELECT b.*, p.image AS payment_image, p.status AS payment_status 
								FROM `booking` b 
								LEFT JOIN `payment` p ON b.book_id = p.book_id
								WHERE b.status = 0 AND b.name LIKE '%$searchQuery%'
								ORDER BY b.book_id DESC
								LIMIT $offset, $recordsPerPage";
						$result = $con->query($sql);
						?>

						<table class="brands-table">
						<thead>
						<tr>
							<th>Renter Name</th>
							<th>Vehicle</th>
							<th>Status</th>
							<th>Payment Image</th>
							<th>Payment Status</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>
						<?php
						if ($result && $result->num_rows > 0) {
							foreach ($result as $r) {
								?>
								<tr class="gradeX">
									<td style="text-align: center;"><?php echo htmlspecialchars($r['name']); ?></td>
									<td style="text-align: center;"><?php echo htmlspecialchars($r['car_name']); ?></td>
									<td style="text-align: center;">Pending</td>
									<td style="text-align: center;" class="center hidden-phone">
										<figure class="image rounded">
											<?php if ($r['payment_status'] == 1): ?>
												<a data-toggle="modal" data-target="#imageModal" onclick="showImage('<?php echo !empty($r['payment_image']) ? '../uploads/' . htmlspecialchars($r['payment_image']) : 'default-image.png'; ?>')">
													<img style="height: 80px; width: 70px; border-radius: 10px; border: 1px solid darkgray;" 
														src="<?php echo !empty($r['payment_image']) ? '../uploads/' . htmlspecialchars($r['payment_image']) : 'default-image.png'; ?>" 
														alt="Payment Image">
												</a>
											<?php else: ?>
												<span style="color: red; font-size: 12px;">No payment proof</span>
											<?php endif; ?>
										</figure>
									</td>
									<td style="text-align: center;">
										<?php 
										// Display payment status based on the value
										echo $r['payment_status'] == 1 ? 'Paid' : 'Not Paid'; 
										?>
									</td>
									<td style="text-align: center;">
										<a class="book-details" href="booking-details.php?id=<?php echo htmlspecialchars($r['book_id']); ?>">Full Details</a>
									</td>
								</tr>
								<?php
							}
						} else {
							echo "<tr><td colspan='8'>No bookings found.</td></tr>"; // Update colspan to 8
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

				</div>
				<!-- Modal Structure -->
				<div id="imageModal" class="modal">
						<div class="modal-content">
							<span class="close" onclick="closeModal()">&times;</span>
							<img class="modal-image" id="fullImage" alt="Full Screen Image">
							<div class="caption" id="caption"></div>
						</div>
					</div>

			</div>
        </main>
    </section>
    <script src="script.js"></script>
	<script>
		 function showImage(imageSrc) {
			document.getElementById('fullImage').src = imageSrc;
			document.getElementById('imageModal').style.display = "block";
		}

		function closeModal() {
			document.getElementById('imageModal').style.display = "none";
		}

		// Close modal on clicking outside the image
		window.onclick = function(event) {
			const modal = document.getElementById('imageModal');
			if (event.target == modal) {
				closeModal();
			}
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
