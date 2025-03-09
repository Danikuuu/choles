<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure user is logged in and an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}

require_once '../../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['packageId']) || empty($_POST['packageId'])) {
        $_SESSION['error'] = "Select a package to delete";
        header("Location: package.php");
    }

    $packageId = intval($_POST['packageId']); 


    $stmt = $con->prepare("DELETE FROM package WHERE id = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Database error";
        header("Location: package.php");
    }

    $stmt->bind_param("i", $packageId);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Package deleted successfully!";
        header("Location: package.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to delete package. Please try again.";
        header("Location: package.php");;
    }

    $stmt->close();
    $con->close();
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: package.php");
}
?>
