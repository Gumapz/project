<?php
include_once 'connect.php'; 
$conn = connect();
// Start session
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if username and password match
    $sql = "SELECT * FROM login WHERE email='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user details
        $row = $result->fetch_assoc();
        $owner_name = $row['name'];

        // Store owner name in session variable
        $_SESSION['name'] = $owner_name;

        if ($username == 'admin') {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>';
            echo 'document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Login Successfully!",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location="admin.php";
                    });
                });
                </script>';
            exit();
        } else {
            // Redirect to owner.php for other users
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo '<script>';
            echo 'document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Login Successfully!",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location="index.php";
                    });
                });
                </script>';
            exit();
        }
    } else {
        // Username or password is incorrect
        echo '<script>alert("Incorrect username or password.")</script>';
    }
}

// Close database connection
$conn->close();
?>
