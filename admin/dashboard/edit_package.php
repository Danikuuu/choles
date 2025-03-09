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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $packageId = $_POST["packageId"];
    $packageName = $_POST["packageName"];
    $packagePrice = $_POST["packagePrice"];
    $peopleCount = $_POST["peopleCount"];
    $menuCount = $_POST["menuCount"];
    $venueStyling = $_POST["venueStyling"];
    $tableCount = $_POST["tableCount"];
    $chairCount = $_POST["chairCount"];
    $venue = $_POST["venue"];

    if (empty($packageId) || empty($packageName) || empty($packagePrice)) {
        $_SESSION['error'] = "Fields must be not empty";
        header("Location: package.php");
    }

    $sql = "UPDATE package 
            SET package_name=?, package_price=?, people_count=?, menu_count=?, venue_styling=?, table_count=?, chair_count=?, venue=? 
            WHERE id=?";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("sdiiiiiss", $packageName, $packagePrice, $peopleCount, $menuCount, $venueStyling, $tableCount, $chairCount, $venue, $packageId);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Package updated successfully!";
        header("Location: package.php");
    } else {
        $_SESSION['error'] = "Error updating package";
        header("Location: package.php");
    }

    $stmt->close();
    $con->close();
} else {
    $_SESSION['error'] = "An error occur";
    header("Location: package.php");
}
?>
