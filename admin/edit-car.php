<?php
include_once 'connect.php';
$conn = connect();
session_start();


if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}


// Step 1: Get the car ID from the URL
$car_id = $_GET['id'];

// Step 2: Fetch the car details from the database
$query = "SELECT * FROM vehicles WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();

// Step 3: Check if the car exists
if ($result->num_rows > 0) {
    $car = $result->fetch_assoc();
} else {
    // Handle the case where the car doesn't exist
    echo "Car not found.";
    exit;
}

// Step 4: Handle form submission for updating the car details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $car_name = $_POST['car-name'];
    $car_brand = $_POST['car-brand'];
    $overview = $_POST['overview'];
    $price = $_POST['price'];
    $fuel = $_POST['fuel'];
    $model = $_POST['model'];
    $seat = $_POST['seat'];
    $accessories = $_POST['accessories'];

    // Prepare update query
    $updateQuery = "UPDATE vehicles SET car_name = ?, car_brand = ?, overview = ?, price = ?, fuel = ?, model = ?, seat = ?, accessories = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssssssssi", $car_name, $car_brand, $overview, $price, $fuel, $model, $seat, $accessories, $car_id);

    // Execute the update
    if ($updateStmt->execute()) {
        // Set session variables for the alert
        $_SESSION['alertMessage'] = "Car updated successfully!";
        $_SESSION['alertType'] = "success";
    
        // Redirect to managecar.php
        header("Location: managecar.php");
        exit;
    } else {
        echo "Error updating car details: " . $conn->error;
    }
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
	<style>
        /* Add your styles here */
    </style>
    <!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="style.css">
	<link rel="icon" type="image/x-icon" href="../image/logo.jpg">
	<title>AdminHub</title>
	<style>
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
			<li class="active">
				<a href="#">
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
				<img src="../login/image/user.png">
			</a>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Edit Car</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">Car</a>
						</li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li>
							<a class="active" href="#">Edit Car</a>
						</li>
					</ul>
				</div>
            </div>
            <div class="post-car-box">
                <h2>Basic Info</h2>
                <form action="" method="post" class="post-car-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <div class="form-field">
                            <label for="car-name">Car Name</label>
                            <input type="text" id="car-name" name="car-name" value="<?php echo htmlspecialchars($car['car_name']); ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="car-brand">Car Brand</label>
                            <input type="text" id="car-brand" name="car-brand" value="<?php echo htmlspecialchars($car['car_brand']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group overview-container">
                        <label for="car-overview">Car Overview</label>
                        <textarea id="car-overview" name="overview"><?php echo htmlspecialchars($car['overview']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <div class="form-field">
                            <label for="car-price">Price</label>
                            <input type="text" id="car-price" name="price" value="<?php echo htmlspecialchars($car['price']); ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="fuel-type">Fuel type</label>
                            <input type="text" id="fuel-type" name="fuel" value="<?php echo htmlspecialchars($car['fuel']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-field">
                            <label for="model-year">Model Year</label>
                            <input type="text" id="model-year" name="model" value="<?php echo htmlspecialchars($car['model']); ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="seating-capacity">Seating Capacity</label>
                            <input type="text" id="seating-capacity" name="seat" value="<?php echo htmlspecialchars($car['seat']); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-field">
                            <label for="accessories">Accessories</label>
                            <textarea id="accessories" name="accessories"><?php echo htmlspecialchars($car['accessories']); ?></textarea>
                        </div>
                    </div>

                    <div class="button-group">
                        <a type="button" class="cancel-btn" href="managecar.php">Cancel</a>
                        <button type="submit" class="submit save-btn">Edit</button>
                    </div>
                </form>
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
	</script>
</body>
</html>
