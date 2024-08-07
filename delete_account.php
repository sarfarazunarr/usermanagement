<?php
session_start();
require_once 'config.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Prepare and execute delete query
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");

if ($stmt->execute([$user_id])) {
    // Account deleted successfully
    session_destroy();
    echo "Your account has been deleted successfully.";
    header("refresh:3;url=register.php");
} else {
    // Error occurred
    echo "Error deleting account.";
}

// No need to close PDO connections
?>
