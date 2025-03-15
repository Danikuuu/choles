<?php
session_start();

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure user is logged in and an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); // Redirect to home or login
    exit();
}

require_once '../../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST['id'], $_POST['item_name'], $_POST['quantity'], $_POST['unit'])) {
        die(json_encode(["success" => false, "message" => "Invalid input data."]));
    }

    $id = intval($_POST['id']);
    $name = trim($_POST['item_name']);
    $quantity = intval($_POST['quantity']);
    $unit = trim($_POST['unit']);

    $stmt = $con->prepare("UPDATE equipment_inventory SET item_name = ?, quantity = ?, unit = ? WHERE id = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Database error!";
    }

    $stmt->bind_param("sisi", $name, $quantity, $unit, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Item updated successfully!";
        header("Location: inventory.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to update item!";
        header("Location: inventory.php");
    }

    $stmt->close();
    $con->close();
} else {
    $_SESSION['error'] = "Invalid request method";
        header("Location: inventory.php");
}
?>
