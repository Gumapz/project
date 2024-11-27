<?php
include_once 'connect.php'; 
$conn = connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand_id = $_POST['brand_id'];
    $service = $_POST['service'];
    $price = $_POST['price'];

    // Prepare and bind
    $stmt = $conn->prepare("UPDATE brand SET service = ?, price = ? WHERE brand_id = ?");
    $stmt->bind_param("ssi", $service, $price, $brand_id); // Adjust data types as necessary

    // Execute the statement
    if ($stmt->execute()) {
        // Set a success message in the session (if needed) for display on the redirected page
        session_start();
        $_SESSION['alertMessage'] = "Extra Service Updated Successfully!";
        $_SESSION['alertType'] = "success";
        
        // Redirect to another page
        header('Location: service.php'); // Adjust this to your target page
        exit(); // Always call exit after a header redirect
    } else {
        // Handle error appropriately
        session_start();
        $_SESSION['alertMessage'] = "Error updating service: " . $stmt->error;
        $_SESSION['alertType'] = "error";

        // Redirect to an error page or back to the form
        header('Location: service.php'); // Adjust this to your target page
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
