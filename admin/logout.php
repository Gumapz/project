<?php
session_start(); // Start the session

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to the login page (or homepage)
header("Location: login.php"); // Change this to your login page
exit();
?>
