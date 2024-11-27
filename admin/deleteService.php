<?php
include_once 'connect.php';
$conn = connect();

// Retrieve JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['brand_id'])) {
    $brand_id = $data['brand_id'];

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM brand WHERE brand_id = ?");
    $stmt->bind_param("i", $brand_id); // Bind the brand_id

    if ($stmt->execute()) {
       // Set a success message in the session (if needed) for display on the redirected page
       session_start();
       $_SESSION['alertMessage'] = "Extra Service Deleted Successfully!";
       $_SESSION['alertType'] = "success";
       
       // Redirect to another page
       header('Location: service.php'); // Adjust this to your target page
       exit(); // Always call exit after a header redirect
    } else {
        // Handle error appropriately
        session_start();
        $_SESSION['alertMessage'] = "Error deleting service: " . $stmt->error;
        $_SESSION['alertType'] = "error";

        // Redirect to an error page or back to the form
        header('Location: service.php'); // Adjust this to your target page
        exit();
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
