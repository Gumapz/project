<?php
include_once 'connect.php'; 
$conn = connect();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare and bind parameters
    $stmt = $conn->prepare("INSERT INTO login (name, address, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fname, $address, $email, $password);

    // Set parameters and execute
    $fname = $_POST['Fullname'];
    $address = $_POST['Address'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Execute SQL query
    $stmt->execute();
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>';
    echo 'document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                position: "center",
                icon: "success",
                title: "You Succesfully Signup! Please Login!",
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location="login.php";
            });
        });
        </script>';

    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>
