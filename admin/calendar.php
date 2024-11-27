<?php
include_once 'connect.php';
$conn = connect();
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}


// Initialize variables
$alertMessage = '';
$alertType = '';

// Handle the form submission (car selection)
$selectedCar = '';
$events = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get the selected car name
    $selectedCar = $_POST['car_name'];

    // Fetch unavailability data for the selected car
    $sqlGetUnavailability = "SELECT car_name, available, end_date FROM vehicles WHERE car_name = ?";
    $stmt = $conn->prepare($sqlGetUnavailability);
    $stmt->bind_param("s", $selectedCar);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'title' => $row['car_name'] . ' - Unavailable',
            'start' => $row['available'],
            'end' => $row['end_date'],
            'backgroundColor' => 'red', // Red background for unavailability
            'textColor' => '#fff', // White text color
            'className' => 'bold-event' // Bold styling for the event
        ];
    }

    // If there was an issue, set an error message
    if (empty($events)) {
        $alertMessage = "No unavailability data found for the selected car.";
        $alertType = "error";
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
	<!-- calendar -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.7.0/main.css">
	<!-- Modal CSS -->
	<style>
		/* Modal styles */
		.modal {
			display: none; /* Hidden by default */
			position: fixed;
			z-index: 1;
			left: 0;
			top: 0;
			width: 100%;
			height: 100%;
			overflow: auto;
			background-color: rgba(0, 0, 0, 0.6); /* Slightly darker overlay */
			padding-top: 60px;
			transition: opacity 0.3s ease-in-out; /* Smooth transition for modal appearance */
		}

		.modal-content {
			background-color: #ffffff; /* Cleaner white */
			margin: 5% auto;
			padding: 30px; /* Added padding for better spacing */
			border: none; /* Remove border for cleaner look */
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Softer shadow for depth */
			width: 80%; /* More responsive width */
			max-width: 450px;
			border-radius: 10px; /* Slightly reduced border-radius for modern look */
			font-family: 'Arial', sans-serif; /* Professional font */
			color: #333; /* Neutral dark color for content */
		}
		.modal-content h2{
			font-size: 30px;
		}
		/* Professional Save Button */
		.modal-content button[type="submit"] {
			background-color: #4CAF50; /* Professional green color */
			color: white; /* White text for contrast */
			padding: 12px 24px; /* Spacing for a more substantial button */
			border: none; /* Remove default borders */
			border-radius: 5px; /* Slightly rounded corners for a modern feel */
			font-size: 16px; /* Slightly larger text for readability */
			font-weight: bold; /* Emphasize button text */
			cursor: pointer; /* Pointer cursor to indicate clickable */
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow for depth */
			transition: background-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for hover effects */
		}

		.modal-content button[type="submit"]:hover {
			background-color: #45a049; /* Slightly darker green on hover */
			box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15); /* Enhanced shadow on hover */
		}

		.modal-content button[type="submit"]:active {
			background-color: #3e8e41; /* Darker green when clicked */
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Reduced shadow on active state */
			transform: translateY(2px); /* Slight movement to simulate press effect */
		}

		/* Form Field Styles */
		.modal-content label {
			font-family: 'Arial', sans-serif;
			font-size: 16px;
			color: #333; /* Dark, neutral color for professional look */
			margin-bottom: 8px;
			display: block; /* Ensure labels are block-level for clean vertical alignment */
		}

		.modal-content input[type="text"],
		input[type="date"] {
			width: 100%; /* Full width for consistency */
			padding: 10px; /* Padding for a comfortable input area */
			margin: 8px 0 16px 0; /* Spacing between fields */
			display: block; /* Block-level element for stacking vertically */
			border: 1px solid #ccc; /* Subtle border */
			border-radius: 4px; /* Slightly rounded corners */
			font-size: 14px; /* Professional, readable font size */
			box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Slight inset shadow for depth */
			background-color: #f9f9f9; /* Light background for readability */
			transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition for focus state */
		}

		.modal-content input[type="text"]:focus,
		input[type="date"]:focus {
			border-color: #4CAF50; /* Professional green color for focus */
			box-shadow: 0 0 6px rgba(76, 175, 80, 0.4); /* Soft green glow on focus */
			outline: none; /* Remove default outline */
		}

		.modal-content input[readonly] {
			background-color: #e9ecef; /* Distinct background for readonly fields */
			cursor: not-allowed; /* Disabled cursor for readonly fields */
		}

		.modal-content input[type="date"] {
			padding-right: 10px; /* Adjust padding for better date picker alignment */
		}

		/* Select box styling */
		.modal-content select {
			width: 100%; /* Full width for better alignment */
			padding: 12px; /* Adequate padding for user comfort */
			margin: 8px 0 16px 0; /* Consistent spacing around the field */
			border: 1px solid #ccc; /* Subtle border */
			border-radius: 4px; /* Slightly rounded corners for a modern look */
			background-color: #f9f9f9; /* Light background for a clean appearance */
			font-size: 14px; /* Professional font size */
			font-family: 'Arial', sans-serif;
			color: #333; /* Dark color for text */
			cursor: pointer; /* Pointer cursor for user interaction */
			box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1); /* Inset shadow for depth */
			transition: border-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transitions */
		}

		.modal-content select:focus {
			border-color: #4CAF50; /* Professional green focus color */
			box-shadow: 0 0 6px rgba(76, 175, 80, 0.4); /* Soft focus shadow */
			outline: none; /* Remove default focus outline */
		}

		/* Option styling */
		.modal-content option {
			padding: 10px; /* Padding for a more comfortable selection */
			background-color: #fff; /* White background for options */
			color: #333; /* Dark text color */
		}

		/* Disable first option for guidance */
		.modal-content select option:first-child {
			color: #888; /* Lighter color for placeholder */
			font-style: italic; /* Italicized for distinction */
		}



		.close {
			margin-top: -20px;
			color: #777; /* Slightly darker for better visibility */
			float: right;
			font-size: 40px; /* Reduced size for subtlety */
			font-weight: normal; /* Normal weight for less emphasis */
			transition: color 0.2s ease; /* Smooth transition on hover */
		}

		.close:hover,
		.close:focus {
			color: #444; /* Darker hover state */
			text-decoration: none;
			cursor: pointer;
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

		/* Style for checkmark on past dates */
		.past-day {
			position: relative;
		}

		.checkmark {
			position: absolute;
			top: 5px;
			left: 5px;
			font-size: 16px;
			color: green;  /* You can change the color */
		}

		/* General calendar styling */
		#calendar {
			max-width: 100%;
			margin: 0 auto;
		}

		/* Responsive adjustments */
		@media (max-width: 768px) {
			#calendar {
				font-size: 0.8em; /* Adjust font size for smaller screens */
			}
		}

		@media (max-width: 576px) {
			#calendar {
				font-size: 0.7em; /* Further adjust font size for very small screens */
			}
		}

		/* Style for the pop-up alert container */
		.popup-alert {
			position: fixed;
			top: 30%;
			left: 50%;
			transform: translate(-50%, -20%);
			z-index: 1050;
			width: 450px;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		/* Style for the pop-up alert content */
		.popup-alert-content {
			background: #fff;
			border-radius: 8px;
			padding: 20px;
			position: relative;
			box-shadow: 0 4px 8px 8px rgba(0, 0, 0, 0.2);
			text-align: center;
			max-width: 100%;
			width: 100%;
		}

		/* Style for the close button */
		.popup-alert-close {
			position: absolute;
			top: 10px;
			right: 5px;
			background: none;
			border: none;
			color: #333;
			font-size: 28px;
			cursor: pointer;
			transition: color 0.3s;
		}

		.popup-alert-close:hover {
			color: #dc3545;
		}

		/* Style for the alert message */
		.popup-alert-message {
			font-size: 20px;
			color: #333;
			margin: 0;
		}

		/* Optional: Animation for showing and hiding the alert */
		.popup-alert.fade-in {
			animation: fadeIn 0.3s ease-out;
		}

		.popup-alert.fade-out {
			animation: fadeOut 0.3s ease-in;
		}

		@keyframes fadeIn {
			from { opacity: 0; }
			to { opacity: 1; }
		}

		@keyframes fadeOut {
			from { opacity: 1; }
			to { opacity: 0; }
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

			<li class="active">
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
					<h1>Calendar Car Availability</h1>
					<ul class="breadcrumb">
						<li><a href="#">Calendar</a></li>
						<li><i class='bx bx-chevron-right'></i></li>
						<li><a class="active" href="#">Car Availability</a></li>
					</ul>
				</div>

				<div class="brands-box">
					<h2>Calendar</h2>
					<!-- Car Selection Form -->
					<form method="POST" action="">
						<label for="car_name">Select Car:</label>
						<select name="car_name" id="car_name">
							<!-- Add car options dynamically from the database -->
							<?php
							$sqlGetCars = "SELECT DISTINCT car_name FROM vehicles";
							$resultGetCars = $conn->query($sqlGetCars);
							while ($row = $resultGetCars->fetch_assoc()) {
								$selected = ($row['car_name'] == $selectedCar) ? 'selected' : '';
								echo "<option value='" . $row['car_name'] . "' $selected>" . $row['car_name'] . "</option>";
							}
							?>
						</select>
						<button type="submit">Show Availability</button>
					</form>

					<!-- Display Alert if there is any message -->
					<?php if (!empty($alertMessage)): ?>
						<div class="alert <?php echo $alertType; ?> alert-dismissible">
							<?php echo $alertMessage; ?>
							<button type="button" class="close" onclick="this.parentElement.style.display='none';">&times;</button>
						</div>
					<?php endif; ?>

					<!-- Calendar -->
					<div id="calendar"></div>
				</div>
			</div>
		</main>
	</section>

	<!-- Modal for pop-up form -->
    <div id="dateModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
			<br>
			<center><h2>Set Car Unavailability</h2></center>
			<br>
            <form id="availabilityForm" method="POST" action="">
                <label for="car_id">Select Car:</label>
                <?php
					// Connect to the database
					$conn = connect();
					if ($conn) {
						$sql = "SELECT * FROM vehicles;";
						$result = $conn->query($sql);
						if ($result && $result->num_rows > 0) {
							echo '<select name="car_name" id="car_name">'; // Changed from car_id to car_name
							echo '<option value="">Select Car</option>';
							while ($row = $result->fetch_assoc()) {
								echo '<option value="' . $row['car_name'] . '">' . $row['car_name'] . '</option>'; // Use car_name as the value
							}
							echo '</select>';
						} else {
							echo "No cars found.";
						}
					} else {
						echo "Database connection failed.";
					}
				?>

                <br><br>
                <label for="startDate">Start Date:</label>
                <input type="text" id="startDate" name="startDate" readonly>
                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" name="endDate" required>
                <button type="submit">Save</button>
            </form>
            <?php if (!empty($message)) echo "<p>$message</p>"; ?>
        </div>
    </div>

	<!-- Pop-Up Alert -->
	<div id="popupAlert" class="popup-alert" style="display: none;">
		<div class="popup-alert-content">
			<button id="popupAlertClose" class="popup-alert-close" aria-label="Close">&times;</button>
			<div id="popupAlertMessage" class="popup-alert-message"></div>
		</div>
	</div>



	<!-- Calendar and Modal JavaScript -->
	<script src="script.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js"></script>
	<script>
        // Modal handling
        var modal = document.getElementById('dateModal');
        var closeBtn = document.getElementsByClassName('close')[0];

        // Close the modal when 'x' is clicked
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        };

        // Close the modal when clicking outside the modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    // Get today's date in YYYY-MM-DD format
    var today = new Date().toISOString().split('T')[0];

    // Initialize FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: <?php echo json_encode($events); ?>,  // Pass PHP events array to JavaScript
        windowResize: function(view) {
            calendar.setOption('height', window.innerHeight - 100);  // Adjust height based on window size
        },
        validRange: {
            start: today,  // Disable dates before today
        },
        dayRender: function(info) {
            var currentDate = new Date(info.date);

            // Mark past days with a checkmark
            if (currentDate < new Date()) {
                // Add a class to the past date and hide the date number
                $(info.el).addClass('past-day');
                // Create and append the checkmark
                var checkmark = document.createElement('span');
                checkmark.className = 'checkmark';
                checkmark.innerHTML = '&#10003;'; // Checkmark character
                info.el.appendChild(checkmark);
            }
        }
    });

    // Render the calendar
    calendar.render();
});





        // Handle form submission
        document.getElementById('availabilityForm').addEventListener('submit', function(event) {
            // Remove this if you want traditional form submission
            // event.preventDefault(); 

            // Get form values
            var selectedCar = document.getElementById('car_id').value;
            var startDate = document.getElementById('startDate').value;
            var endDate = document.getElementById('endDate').value;

            // For now, just log the values to the console (you would send this to your server in a real app)
            console.log("Car:", selectedCar, "Start Date:", startDate, "End Date:", endDate);

            // You can then send this data to the server using an AJAX request or other methods

            // Close the modal after submission
            modal.style.display = 'none';
        });



		



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