<?php
session_start();
require_once '../../data-handling/db/connection.php'; // Adjust path as needed

// Check if the user is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    $_SESSION['error'] = "Unauthorized Action";
    header("Location: ../../index.php"); // Redirect to homepage
    exit();
}

// Check if the delete request was made
if (!isset($_POST['submit']) || !isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
    $_SESSION['error'] = "Invalid request";
    header("Location: ../user_management.php"); // Redirect back to the user list page
    exit();
}

$user_id = (int) $_POST['user_id']; // Sanitize input

// Prepare and execute delete query
$stmt = $con->prepare("DELETE FROM user WHERE id = ?");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "User deleted successfully";
    } else {
        $_SESSION['error'] = "Error deleting user";
    }
    $stmt->close();
} else {
    $_SESSION['error'] = "Database error";
}

$con->close();

// Redirect back to the user management page
header("Location: users.php"); 
exit();
?>
