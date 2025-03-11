<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}
require_once '../../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $_POST['itemName'];
    $itemQuantity = $_POST['itemQuantity'];

    $sql = "INSERT INTO equipment_inventory (item_name, quantity) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("si", $itemName, $itemQuantity);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Equipment added successfully!";
    } else {
        $_SESSION['error'] = "Failed to add equipment.";
    }
    $stmt->close();

    header("Location: inventory.php");
    exit();
}
?>
