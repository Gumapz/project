<?php
session_start();
include_once 'connect.php'; // Include your database connection script
$conn = connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL statement
    $sql = "SELECT * FROM login WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Compare password directly (for plain-text password)
        if ($password === $user['password']) {
            // Set session variables for the admin
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['login_id'];
            $_SESSION['username'] = $user['email'];

            // Redirect to admin dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="icon" type="image/x-icon" href="../image/logo.jpg">
    <!-- Link Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>

<style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-image: url('../image/bg1.1.jpg'); /* Add your image here */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

.login-container {
    width: 350px;
    padding: 20px;
    background-color: rgba(161, 161, 161, 0.349); /* Slight transparency */
    border-radius: 8px;
    border: 1px solid rgb(179, 179, 179);
    backdrop-filter: blur(5px);
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
}

.login-form h2 {
    text-align: center;
    margin-bottom: 30px;
    color: white;
}

.input-group {
    margin-bottom: 30px;
}

.input-group label {
    display: block;
    font-size: 14px;
    margin-bottom: 10px;
    color: white;
}

/* Input container with icons */
.input-container {
    position: relative;
}

.input-container input {
    width: 100%;
    padding: 10px;
    padding-right: 40px; /* Add space for the icon */
    border: 1px solid #ffffff;
    border-radius: 20px;
    background-color: transparent; /* Fully transparent input background */
    color: #ffffff;
}

.input-container input::placeholder {
    color: #ffffff; /* Color for placeholder text */
}

/* Styling the icons inside input */
.input-container i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #ffffff;
}

.btn {
    width: 100%;
    padding: 10px;
    background-color: #ff8080;
    color: white;
    border: none;
    border-radius: 20px;
    cursor: pointer;
    font-size: 16px;
}

.btn:hover {
    background-color: #ff3b3b;
}
.error-message {
    color: #b71c1c; /* Darker red for a more serious tone */
    background-color: #fce4ec; /* Light pink for a soft contrast */
    border-left: 4px solid #d32f2f; /* A bold red accent on the left */
    padding: 15px;
    border-radius: 5px; /* Rounded corners for a smooth look */
    margin-top: 10px;
    font-size: 16px; /* Slightly larger font for readability */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Soft shadow for a floating effect */
    text-align: left;
    font-family: 'Arial', sans-serif; /* Clean, professional font */
}

.error-message i {
    margin-right: 10px; /* Space between icon and text */
}




</style>
<body>
    <div class="login-container">
        <form class="login-form" action="login.php" method="post">
            <h2>Login</h2>
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <br>
            <div class="input-group">
                <div class="input-container">
                    <input type="text" id="username" name="username" placeholder="Email" required>
                    <i class="fas fa-user"></i>
                </div>
            </div>
            <div class="input-group">
                <div class="input-container">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            <div class="input-group">
                <button type="submit" class="btn">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
