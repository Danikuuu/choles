<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); // Redirect to home or login
    exit();
}

require_once '../../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity <= 0) {
        echo "Invalid quantity.";
        exit;
    }

    // Check current stock
    $query = "SELECT quantity FROM equipment_inventory WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($current_stock);
    $stmt->fetch();
    $stmt->close();

    if ($current_stock < $quantity) {
        $_SESSION['error'] = "Not enough stock to decrease";
        exit;
    }

    // Update stock
    $query = "UPDATE equipment_inventory SET quantity = quantity - ? WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("ii", $quantity, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Stock decreased successfully!";
        header("Location: inventory.php"); // Redirect back to inventory page
        exit;
    } else {
        $_SESSION['error'] = "Error updating stock";;
    }
}
?>
