<?php

	include_once 'connect.php'; // Include your database connection script
	$conn = connect(); // Assuming you have a database connection established already
	session_start();


	if (!isset($_SESSION['user_id'])) {
		// Redirect to login page if not logged in
		header("Location: login.php");
		exit();
	}	
	
	if ($conn) {
		// Query to count the total number of cars owned by the logged-in owner
		$sql = "SELECT COUNT(*) AS total FROM `vehicles`;";
		$sql2 = "SELECT COUNT(*) AS total FROM `booking`;";
		$sql3 = "SELECT COUNT(*) AS total FROM `book_cancel`;";
		$sql4 = "SELECT COUNT(*) AS total FROM `booking` WHERE status = 1;";
		$sql5 = "SELECT COUNT(DISTINCT car_name) AS total FROM `booking`;";
		$result = $conn->query($sql);
		$result2 = $conn->query($sql2);
		$result3 = $conn->query($sql3);
		$result4 = $conn->query($sql4);
		$result5 = mysqli_query($conn, $sql5);

		// Check if the query was successful
		if ($result && $result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$totalRows = $row['total'];
		} else {
			echo "Error: Query failed.";
		}
		if ($result2 && $result2->num_rows > 0) {
			$row = $result2->fetch_assoc();
			$totalRows2 = $row['total'];
		} else {
			echo "Error: Query failed.";
		}
		if ($result3 && $result3->num_rows > 0) {
			$row = $result3->fetch_assoc();
			$totalRows3 = $row['total'];
		} else {
			echo "Error: Query failed.";
		}
		if ($result4 && $result4->num_rows > 0) {
			$row = $result4->fetch_assoc();
			$totalRows4 = $row['total'];
		} else {
			echo "Error: Query failed.";
		}
		if ($result5 && $result5->num_rows > 0) {
			$row = $result5->fetch_assoc();
			$totalRows5 = $row['total']; // This will be the total unique rented cars
		} else {
			echo "Error: Query failed.";
		}
	} else {
		echo "Database connection failed.";
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

	
$query = "SELECT SUM(price) AS total_sales FROM payment"; // Adjust the query based on your actual table and column names
$result = mysqli_query($conn, $query); // $connection is your database connection variable

// Fetch the result
$row = mysqli_fetch_assoc($result);
$totalSales = $row['total_sales'] ? $row['total_sales'] : 0;





// Initialize an array to hold the earnings for each month
$monthlyEarnings = array_fill(0, 12, 0); // Array for 12 months
$totalEarnings = 0; // Variable to hold total earnings
$monthToMonthChanges = array_fill(0, 11, 0); // Array for month-to-month changes

// Optionally, set a specific year to filter results
$selectedYear = date('Y'); // Default to current year, or set as needed

// Fetch earnings grouped by month
$sql = "SELECT MONTH(date) AS month, SUM(price) AS total_earnings 
        FROM payment 
        WHERE YEAR(date) = ? 
        GROUP BY MONTH(date)";
$stmt = $conn->prepare($sql);

// Check if statement preparation was successful
if ($stmt === false) {
    die("SQL statement preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $selectedYear); // Bind the year parameter
$stmt->execute();
$result = $stmt->get_result();

// Populate the monthly earnings array and calculate total earnings
while ($row = $result->fetch_assoc()) {
    $monthIndex = $row['month'] - 1; // month is 1-indexed
    $monthlyEarnings[$monthIndex] = (int)$row['total_earnings']; // Store monthly earnings
    $totalEarnings += $monthlyEarnings[$monthIndex]; // Accumulate total earnings
}

// Calculate month-to-month changes
for ($i = 1; $i < 12; $i++) {
    $monthToMonthChanges[$i - 1] = $monthlyEarnings[$i] - $monthlyEarnings[$i - 1]; // Difference from previous month
}

// Close the connection
$stmt->close();
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
		/* .table-container {
            max-height: 400px; 
            overflow-y: auto; 
            overflow-x: hidden; 
            border: 1px solid #ccc;
            border-radius: 5px; 
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px; 
            text-align: left; 
            border-bottom: 1px solid #ddd; 
        }

        th {
            background-color: #f2f2f2; 
        } */

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

			<li class="active">
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
					<h1>Dashboard</h1>
					<ul class="breadcrumb">
						<li>
							<a href="#">Dashboard</a>
						</li>
						<li><i class='bx bx-chevron-right' ></i></li>
						<li>
							<a class="active" href="#">Home</a>
						</li>
					</ul>
				</div>
				
			</div>

			<ul class="box-info">
			<li>
				<i class='bx bxs-dollar-circle'></i>
				<span class="text">
					<h3><?php echo '₱' . number_format($totalSales, 2); ?></h3> <!-- Format the total sales -->
					<p>Total Sales</p>
				</span>
			</li>		
				<li>
					<i class='bx bxs-car' ></i>
					<span class="text">
						<h3><?php echo $totalRows;?></h3>
						<p>Listed Cars</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-car' ></i>
					<span class="text">
						<h3><?php echo $totalRows5;?></h3>
						<p>Rented Cars</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-calendar-check' ></i>
					<span class="text">
						<h3><?php echo $totalRows2;?></h3>
						<p>Total Bookings</p>
					</span>
				</li>
				
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3><?php echo $totalRows3;?></h3>
						<p>Cancel Booking</p>
					</span>
				</li>
				<li>
					<i class='bx bxs-group' ></i>
					<span class="text">
						<h3><?php echo $totalRows4;?></h3>
						<p>Accepted Booking</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Earnings</h3>
						<i class='bx bx-filter' ></i>
					</div>
					<div style="max-height: 300px; overflow-y: auto; margin-top: 40px; border: 1px solid #ddd; border-radius: 4px;">
						<table style="width: 100%; border-collapse: collapse;">
							<thead>
								<tr>
									<th style="border: 1px solid #ddd; padding: 8px;">Month</th>
									<th style="border: 1px solid #ddd; padding: 8px;">Total Earnings</th>
								</tr>
							</thead>
							<tbody>
								<?php
								// Month names for display
								$monthNames = [
									'January', 'February', 'March', 'April', 'May', 
									'June', 'July', 'August', 'September', 'October', 
									'November', 'December'
								];
								
								// Populate the table rows based on monthly earnings
								foreach ($monthlyEarnings as $index => $earning) {
									if ($earning > 0) { // Only display months with earnings
										echo "<tr>
											<td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$monthNames[$index]}</td>
											<td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>₱" . number_format($earning, 2) . "</td>
										</tr>";
									}
								}
								?>
							</tbody>
						</table>
						
				</div>
				<br>
					<p style="text-align: right; font-size: 20px;"><strong> Total: </strong><?php echo '₱' . number_format($totalSales, 2); ?></p>
				</div>
				
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	

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