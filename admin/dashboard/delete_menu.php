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
    if (!isset($_POST['menuId']) || empty($_POST['menuId'])) {
        die(json_encode(["success" => false, "message" => "Invalid menu ID."]));
    }

    $menuId = intval($_POST['menuId']); 


    $stmt = $con->prepare("DELETE FROM menu WHERE id = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Database error";
        header("Location: menu.php");
    }

    $stmt->bind_param("i", $menuId);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Menu deleted successfully!";
        header("Location: menu.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to delete menu. Please try again.";
        header("Location: menu.php");;
    }

    $stmt->close();
    $con->close();
} else {
    $_SESSION['error'] = "Invalid request method";
    header("Location: menu.php");
}
?>
