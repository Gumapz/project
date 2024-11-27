<?php
session_start(); // Start the session
include_once 'connect.php';
$conn = connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $username = $_POST['username'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    $car_name = $_POST['car_name'];
    $model = $_POST['model'];
    $fuel = $_POST['fuel'];
    $seat = $_POST['seat'];
    $price = $_POST['price'];

    $fromDate = $_POST['fromDate'];
    $time = $_POST['time'];
    $toDate = $_POST['toDate'];
    $message = $_POST['message'];

    // Prepare SQL statement
    $sql = "INSERT INTO booking (name, address, email, contact, car_name, model, fuel, seats, price, from_date, until_date, pickup_time, drop_time, message)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssssssssssssss', $username, $address, $email, $contact, $car_name, $model, $fuel, $seat, $price, $fromDate, $toDate, $time, $time, $message);

        if ($stmt->execute()) {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>';
            echo 'document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Reservation Successfully Save!",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location="user_vehicle.php";
                    });
                });
                </script>';
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}



?>
