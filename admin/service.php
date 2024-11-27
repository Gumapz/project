<?php
include_once 'connect.php'; 
$conn = connect();
session_start();


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

	
$query = "SELECT SUM(price) AS total_sales FROM payment"; // Adjust the query based on your actual table and column names
$result = mysqli_query($conn, $query); // $connection is your database connection variable

// Fetch the result
$row = mysqli_fetch_assoc($result);
$totalSales = $row['total_sales'] ? $row['total_sales'] : 0;

//=======================================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $service_name = $_POST['service_name'];
    $service_price = $_POST['service_price'];

    // Validate and sanitize inputs
    $service_name = htmlspecialchars(strip_tags($service_name));
    $service_price = htmlspecialchars(strip_tags($service_price));

    // Prepare SQL query
    $sql = "INSERT INTO brand (service, price) VALUES (?, ?)";
    
    // Prepare statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $stmt->bind_param("ss", $service_name, $service_price);

        // Execute statement
        if ($stmt->execute()) {
            $alertMessage = "Extra Service Added Successfully!";
            $alertType = "success";
        } else {
            $alertMessage = "Extra Service Not Added.";
            $alertType = "error";
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }

}




if ($_SERVER['REQUEST_METHOD']) {
    if (isset($_POST['accept']) && !empty($_POST['accept'])) {
		$brand_id = $conn->real_escape_string($_POST['accept']);

		// Prepare and execute delete statement
		$stmt = $conn->prepare("DELETE FROM brand WHERE brand_id = ?");
		$stmt->bind_param("i", $brand_id); // Bind the brand_id

		if ($stmt->execute()) {
			$alertMessage = "Extra Service Deleted Successfully!";
			$alertType = "success";
		} else {
			$alertMessage = "Extra Service Not Deleted.";
			$alertType = "error";
		}

		// Close the statement and connection
		$stmt->close();
	} else {
		echo "No Renter ID provided.";
	}
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<style>
		.info-table {
			width: 100%;
			border-collapse: collapse; /* Ensure there are no gaps between table cells */
		}

		.info-table th, .info-table td {
			padding: 12px;
			text-align: left; /* Align text to the left */
			border: 1px solid #ddd; /* Add borders between cells */
		}

		.info-table th {
			background-color: #f4f4f4;
			font-size: 18px;
		}

		.info-table td {
			font-size: 16px;
			color: #333;
		}

		/* Button */
		.info-table td .action-btn {
			padding: 8px 16px;
			font-size: 16px;
			color: #fff;
			background-color: #007bff;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			transition: background-color 0.3s ease;
		}

		.info-table td .action-btn:hover {
			background-color: #0056b3;
		}

		/* Create Service Button */
		.button-container {
			display: flex;
			justify-content: flex-start; /* Align button to the left */
			margin-bottom: 10px; /* Add space between button and table */
		}

		.create-btn {
			padding: 10px 20px;
			font-size: 16px;
			color: #fff;
			background-color: #28a745; /* Green button */
			border: none;
			border-radius: 4px;
			cursor: pointer;
			transition: background-color 0.3s ease;
		}

		.create-btn:hover {
			background-color: #218838; /* Darker green on hover */
		}

		/* Modal Styles */
		.modal {
			display: none; /* Hidden by default */
			position: fixed;
			z-index: 1000; /* Stay on top */
			left: 0;
			top: 0;
			width: 120%;
			height: 100%;
			background-color: rgba(0, 0, 0, 0.5); /* Black background with opacity */
		}

		.modal-content {
			background-color: #fff;
			margin: 10% auto; /* Center the modal */
			padding: 20px;
			border: 1px solid #ddd;
			border-radius: 8px;
			width: 500px; /* Adjust width as needed */
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
		}

		.modal-content h2 {
			margin-bottom: 20px;
		}

		.modal-content label {
			display: block;
			margin-bottom: 8px;
			font-weight: bold;
			color: #333;
		}

		.modal-content input {
			width: 100%;
			padding: 10px;
			margin-bottom: 20px;
			border: 1px solid #ccc;
			border-radius: 4px;
			box-sizing: border-box;
		}

		.modal-content button.create-btn {
			width: 100%;
			padding: 10px 0;
			background-color: #28a745;
			color: #fff;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			transition: background-color 0.3s ease;
		}

		.modal-content button.create-btn:hover {
			background-color: #218838;
		}

		/* Close Button */
		.close-btn {
			color: #aaa;
			float: right;
			font-size: 28px;
			font-weight: bold;
			cursor: pointer;
		}

		.close-btn:hover {
			color: #000;
		}

		.dropdown {
			position: relative;
			display: inline-block;
		}

		.dropdown-menu {
			display: none; /* Hidden by default */
			position: absolute;
			background-color: #fff;
			min-width: 120px;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
			z-index: 1;
		}

		.dropdown-menu .dropdown-item {
			padding: 10px;
			background: #28a745;
			text-decoration: none;
			color: #fff;
			display: block;
			text-align: center;
			border-radius: 5px;
		}

		.dropdown-menu .dropdown-item:hover {
			background-color: #218838;
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

	<link rel="stylesheet" href="style.css">
	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="icon" type="image/x-icon" href="../image/logo.jpg">
	<title>AdminHub</title>
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
			
			<li>
				<a href="managebook.php">
					<i class='bx bxs-book' ></i>
					<span class="text">Manage Bookings</span>
				</a>
			</li>
			<li class="active">
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
			<i class='bx bx-menu'></i>
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
				<img src="../login/image/user.png" alt="Profile">
			</a>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Extra Service</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">Service</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Extra Service</a>
						</li>
					</ul>
				</div>

                <div class="brands-box">
					<h2>Extra Service</h2>
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

					<div class="button-container">
						<button class="create-btn" id="open-modal">Add Service</button>
					</div>
					<?php
						
						$sql = "SELECT * FROM `brand`";
						$result = $conn->query($sql);
					?>
					
					<table class="info-table">
						<tr>
							<th>Name</th>
							<th>Price</th>
							<th>Action</th>
						</tr>
						<?php
						if ($result && $result->num_rows > 0) {
							foreach ($result as $r) {
								?>
								<tr>
									<td><?php echo $r['service']; ?></td>
									<td>₱<?php echo number_format($r['price'], 2); ?></td>
									<td>
										<div class="dropdown">
											<button class="action-btn dropdown-toggle" onclick="toggleDropdown(event)">Action</button>
											<div class="dropdown-menu">
												<a href="javascript:void(0);" class="dropdown-item" 
												onclick="openEditModal('<?php echo $r['brand_id']; ?>', '<?php echo $r['service']; ?>', '<?php echo $r['price']; ?>')">Edit</a>
											
												<form method="POST" action="" style="display:inline;" id="accept" >
													<input type="hidden" name="accept" value="<?php echo $r['brand_id']; ?>">
													<a type="button" class="dropdown-item" onclick="openModal2()" >Delete</a>
												</form>
											</div>
										</div>
									</td>
								</tr>
								<?php
							}
						} else {
							echo "<tr><td colspan='3'>No Service found.</td></tr>";
						}
						?>
					</table>
				</div>

				<!-- Modal Popup -->
				<div class="modal" id="create-service-modal">
					<div class="modal-content">
					<form action="" method="post">
						<span class="close-btn" id="close-modal">&times;</span>
						<h2>Add New Service</h2>
						<label for="service-name">Name:</label>
						<input type="text" id="service-name" name="service_name" placeholder="Enter service name" required>

						<label for="service-price">Price:</label>
						<input type="text" id="service-price" name="service_price" placeholder="Enter service price" required>

						<button type="submit" class="create-btn" id="submit-service">Add</button>
					</form>
					</div>
				</div>

				<div id="editModal" class="modal" style="display:none;">
					<div class="modal-content">
						<span class="close-btn" onclick="closeModal()">&times;</span>
						<h2>Edit Service</h2>
						<form id="editForm" action="updateService.php" method="post">
							<input type="hidden" id="editBrandId" name="brand_id" value="">
							<label for="editService">Service Name:</label>
							<input type="text" id="editService" name="service" required>
							<label for="editPrice">Price:</label>
							<input type="text" id="editPrice" name="price" required>
							<button type="submit" class="create-btn">Save Changes</button>
						</form>
					</div>
				</div>

				<!-- Modal structure -->
				<div class="modal-overlay" id="confirmationModal2">
					<div class="modal-content">
						<div class="modal-header">
							Are you sure you want to delete this service?
						</div>
						<button class="modal-btn confirm-btn" onclick="confirmreservation()">Yes</button>
						<button class="modal-btn cancel-btn" onclick="closeModal()">Cancel</button>
					</div>
				</div>
			</div>
        </main>
	</section>

	<script src="script.js"></script>
	<script>
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

//=====================================================================================================
		// Get modal and buttons
		var modal = document.getElementById("create-service-modal");
		var openModalBtn = document.getElementById("open-modal");
		var closeModalBtn = document.getElementById("close-modal");
	
		// Open the modal
		openModalBtn.onclick = function() {
			modal.style.display = "block";
		}
	
		// Close the modal
		closeModalBtn.onclick = function() {
			modal.style.display = "none";
		}
	
		// Close the modal when clicking outside of the modal content
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}

		// Toggle dropdown menu
		function toggleDropdown(event) {
			const dropdownMenu = event.currentTarget.nextElementSibling;
			dropdownMenu.style.display = dropdownMenu.style.display === "block" ? "none" : "block";
		}

		// Close dropdown if clicked outside
		window.onclick = function(event) {
			if (!event.target.matches('.action-btn')) {
				const dropdowns = document.querySelectorAll('.dropdown-menu');
				dropdowns.forEach(dropdown => {
					dropdown.style.display = 'none';
				});
			}
		}

//=====================================================================================

function openEditModal(brandId, service, price) {
    document.getElementById('editBrandId').value = brandId;
    document.getElementById('editService').value = service;
    document.getElementById('editPrice').value = price;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
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
    </script>
</body>
</html>
