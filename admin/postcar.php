<?php
include_once 'connect.php'; // Include your database connection script
$conn = connect();
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}


// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $carname = isset($_POST['car-name']) ? mysqli_real_escape_string($conn, $_POST['car-name']) : '';
    $brand = isset($_POST['car-brand']) ? $_POST['car-brand'] : '';
    $overview = isset($_POST['overview']) ? mysqli_real_escape_string($conn, $_POST['overview']) : '';
    $price = isset($_POST['price']) ? $_POST['price'] : '';
    $fuel = isset($_POST['fuel']) ? $_POST['fuel'] : '';
    $model = isset($_POST['model']) ? $_POST['model'] : '';
    $seat = isset($_POST['seat']) ? $_POST['seat'] : '';
    $accessories = isset($_POST['accessories']) ? $_POST['accessories'] : [];

    if (isset($_FILES['image'])) {
        $targetDirectory = "uploads/";
        $file_name = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $targetFile = $targetDirectory . basename($file_name);

        // Move the file to the target directory
        if (move_uploaded_file($file_tmp, $targetFile)) {
            // Database insert query
            $sql = "INSERT INTO vehicles (car_name, car_brand, overview, price, seat, fuel, model, image, accessories) 
                    VALUES ('$carname', '$brand', '$overview', '$price', '$seat', '$fuel', '$model', '$file_name', '" . implode(", ", $accessories) . "')";

            if (mysqli_query($conn, $sql)) {
                // If the insertion is successful, show a success alert
				$alertMessage = "Car added successfully!";
				$alertType = "success";
			} else {
				// If something goes wrong, show an error alert
				$alertMessage = "Error adding brand.";
				$alertType = "error";
			}
        } else {
            echo "File upload failed.";
        }

        
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

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<!-- My CSS -->
	<link rel="stylesheet" href="style.css">
	<link rel="icon" type="image/x-icon" href="../image/logo.jpg">
	<title>AdminHub</title>

	<style>
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
					<h1>Post Cars</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">Cars</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Post Cars</a>
						</li>
					</ul>
				</div>
                
            </div>
            <div class="post-car-box">
                <h2>Basic Info</h2>
				<!-- DISPLAY ALERT -->
				<?php if (!empty($alertMessage)): ?>
							<div class="alert alert-<?php echo $alertType; ?> alert-dismissible">
								<?php echo $alertMessage; ?>
								<button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
							</div>
						<?php endif; ?>
                <form action="" method="post" class="post-car-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <div class="form-field">
                            <label for="car-name">Car Name</label>
                            <input type="text" id="car-name" name="car-name" placeholder="Enter the car name" required>
                        </div>
                        <div class="form-field">
                            <label for="car-brand">Car Brand</label>
                            <input type="text" id="car-brand" name="car-brand" placeholder="Enter the car brand" required>
                        </div>
                    </div>
                    <div class="form-group overview-container">
                        <label for="car-overview">Car Overview</label>
                        <textarea id="car-overview" name="overview" placeholder=""></textarea>
                    </div>

                    <div class="form-group">
                        <div class="form-field">
                            <label for="car-name">Price</label>
                            <input type="text" id="car-name" name="price" placeholder="Enter the car price" required>
                        </div>
                        <div class="form-field">
                            <label for="car-brand">Select fuel type</label>
                            <select id="car-brand" name="fuel" required>
                                <option value="">Select</option>
                                <option value="Special">Special</option>
								<option value="Unleaded">Unleaded</option>
								<option value="Diesel">Diesel</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-field">
                            <label for="car-name">Model Year</label>
                            <input type="text" id="car-name" name="model" placeholder="Enter the car model year" required>
                        </div>
                        <div class="form-field">
                            <label for="car-name">Seating Capacity</label>
                            <input type="text" id="car-name" name="seat" placeholder="Enter the number Seat" required>
                        </div>
                    </div>

                    <div class="form-group image-upload">
                        <label for="car-image">Upload Image</label>
                        <div class="image-upload-container">
                            <input type="file" id="car-image" name="image" required accept="image/jpeg, image/jpg, image/png," />
                           
                        </div>
                    </div>

                
					</div>
						<div class="accessories-box">
							<h2>Accessories</h2>
							<div class="accessories-container">
								<label><input type="checkbox" name="accessories[]" value="aircon"> Aircon</label>
								<label><input type="checkbox" name="accessories[]" value="power-steering"> Power Steering</label>
								<label><input type="checkbox" name="accessories[]" value="cd-player"> CD Player</label>
								<label><input type="checkbox" name="accessories[]" value="navigation-system"> Navigation System</label>
								<label><input type="checkbox" name="accessories[]" value="bluetooth"> Bluetooth</label>
								<label><input type="checkbox" name="accessories[]" value="sunroof"> Sunroof</label>
								<label><input type="checkbox" name="accessories[]" value="leather-seats"> Leather Seats</label>
								<label><input type="checkbox" name="accessories[]" value="backup-camera"> Backup Camera</label>
								<label><input type="checkbox" name="accessories[]" value="parking-sensors"> Parking Sensors</label>
								<label><input type="checkbox" name="accessories[]" value="heated-seats"> Heated Seats</label>
								<label><input type="text" name="accessories[]" placeholder="Others..."></label>
							</div>
							
						</div>
						<div class="button-group">
								<button type="button" class="cancel-btn">Cancel</button>
								<button type="submit" class="submit save-btn">Save</button>
							</div>
			</form>
            
            
            
        </main>
    </section>

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