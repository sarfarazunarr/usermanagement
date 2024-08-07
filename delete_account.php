<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "username", "password", "database_name");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Account deleted successfully
    session_destroy();
    echo "Your account has been deleted successfully.";
    header("refresh:3;url=register.php");} else {
    // Error occurred
    echo "Error deleting account: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
