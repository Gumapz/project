<?php
include_once 'connect.php';
$conn = connect();
session_start();

// Handle the return car
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['pickup']) && !empty($_POST['pickup'])) {
        $carId = $conn->real_escape_string($_POST['pickup']);
        
            $updateDate = date('Y-m-d');
            $returnDate = "pickup"; 

            $sql = "UPDATE booking SET  pickup = ?, date = ? WHERE book_id = ?";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssi", $returnDate, $updateDate, $carId);
                if ($stmt->execute()) {
                    $_SESSION['alertMessage'] = "The Car has been picked up!";
                    $_SESSION['alertType'] = "success";
                    header("Location: accepted.php");
                    exit;
                } else {
                    $_SESSION['alertMessage'] = "Code error picked up car.";
                    $_SESSION['alertType'] = "error";
                }
            } else {
                $_SESSION['alertMessage'] = "Failed to prepare SQL statement.";
                $_SESSION['alertType'] = "error";
            }
    }else {
        $_SESSION['alertMessage'] = "car ID not found.";
        $_SESSION['alertType'] = "error";
    }
}else {
    $_SESSION['alertMessage'] = "Failed to prepare SQL statement.";
    $_SESSION['alertType'] = "error";
}
?>