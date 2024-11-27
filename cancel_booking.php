<?php
include_once 'connect.php';
$conn = connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $booking_id = $_POST['booking_id'];
    $reason = $_POST['reason'];

    $sql = "UPDATE booking SET message = ?, cancel= 1 WHERE reference_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $reason, $booking_id);

    if ($stmt->execute()) {
        echo "Cancellation reason saved successfully.";
    } else {
        echo "Error saving cancellation reason.";
    }

    $stmt->close();
    $conn->close();
}
?>
