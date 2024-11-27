<?php
include_once 'connect.php';
$conn = connect();
session_start();

// Handle the return car
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['return']) && !empty($_POST['return'])) {
        $carId = $conn->real_escape_string($_POST['return']);
        
        // Current date and time
        $updateDate = date('Y-m-d'); // Actual return date
        $actualDropTime = date('H:i:s'); // Actual return time
        $returnDate = "Returned"; 

        // Fetch expected return details from the database
        $sql = "SELECT until_date, drop_time FROM booking WHERE book_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $carId);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    // Combine expected return date and time
                    $expectedDateTime = $row['until_date'] . ' ' . $row['drop_time'];
                    $actualDateTime = $updateDate . ' ' . $actualDropTime;

                    // Calculate the duration
                    $expectedDt = new DateTime($expectedDateTime);
                    $actualDt = new DateTime($actualDateTime);
                    $duration = $expectedDt->diff($actualDt);
                    $hoursLate = ($duration->days * 24) + $duration->h + ($duration->i / 60);

                    // Penalty calculation (if late)
                    $penaltyAmount = 0; // Default penalty
                    if ($actualDt > $expectedDt) {
                        $penaltyRatePerHour = 50; // Example: PHP 100 per hour
                        $penaltyAmount = ceil($hoursLate) * $penaltyRatePerHour;
                    }

                    // Update booking record with penalty and return status
                    $updateSql = "UPDATE booking 
                                  SET returned = ?, date = ?, actual_return_date = ?, 
                                      actual_drop_time = ?, penalty_amount = ? 
                                  WHERE book_id = ?";
                    if ($updateStmt = $conn->prepare($updateSql)) {
                        $updateStmt->bind_param(
                            "ssssdi",
                            $returnDate,
                            $updateDate,
                            $updateDate,
                            $actualDropTime,
                            $penaltyAmount,
                            $carId
                        );

                        if ($updateStmt->execute()) {
                            $_SESSION['alertMessage'] = "The car has been returned!";
                            if ($penaltyAmount > 0) {
                                $_SESSION['alertMessage'] .= " Penalty incurred: PHP " . $penaltyAmount;
                            }
                            $_SESSION['alertType'] = "success";
                            header("Location: accepted.php");
                            exit;
                        } else {
                            $_SESSION['alertMessage'] = "Failed to update return details.";
                            $_SESSION['alertType'] = "error";
                        }
                    } else {
                        $_SESSION['alertMessage'] = "Failed to prepare update SQL statement.";
                        $_SESSION['alertType'] = "error";
                    }
                } else {
                    $_SESSION['alertMessage'] = "Car booking details not found.";
                    $_SESSION['alertType'] = "error";
                }
            } else {
                $_SESSION['alertMessage'] = "Failed to execute fetch query.";
                $_SESSION['alertType'] = "error";
            }
        } else {
            $_SESSION['alertMessage'] = "Failed to prepare fetch SQL statement.";
            $_SESSION['alertType'] = "error";
        }
    } else {
        $_SESSION['alertMessage'] = "Car ID not found.";
        $_SESSION['alertType'] = "error";
    }
} else {
    $_SESSION['alertMessage'] = "Invalid request method.";
    $_SESSION['alertType'] = "error";
}

?>