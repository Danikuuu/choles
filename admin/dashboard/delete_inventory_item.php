<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure user is logged in and an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 | $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); // Redirect to home or login
    exit();
}

require_once '../../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['inventoryId']) || empty($_POST['inventoryId'])) {
        $_SESSION['error'] = "Select an item to delete";
        header("Location: inventory.php");
    }

    $inventoryId = intval($_POST['inventoryId']); 


    $stmt = $con->prepare("DELETE FROM equipment_inventory WHERE id = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Database error";
        header("Location: inventory.php");
    }

    $stmt->bind_param("i", $inventoryId);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Item deleted successfully!";
        header("Location: inventory.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to delete item. Please try again.";
        header("Location: inventory.php");;
    }

    $stmt->close();
    $con->close();
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: inventory.php");
}
?>
