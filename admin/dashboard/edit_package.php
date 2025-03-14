<?php
session_start();
require_once '../../data-handling/db/connection.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0) {
    header("Location: ../index.php"); // Redirect if unauthorized
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get package ID
    $packageId = intval($_POST['packageId']);
    
    // Sanitize inputs
    $packageName = trim($_POST['packageName']);
    $packagePrice = floatval($_POST['packagePrice']);
    $packageDownpayment = floatval($_POST['editPackageDownpayment']);
    $peopleCount = intval($_POST['peopleCount']);
    $menuCount = intval($_POST['menuCount']);
    $venueStyling = isset($_POST['venueStyling']) ? intval($_POST['venueStyling']) : 0; // Ensure it's 0 or 1
    $tableCount = intval($_POST['tableCount']);
    $chairCount = intval($_POST['chairCount']);
    $venue = trim($_POST['venue']);
    
    // Prepare and execute update query
    $updateQuery = "UPDATE package SET package_name=?, package_price=?, downpayment=?, people_count=?, menu_count=?, venue_styling=?, table_count=?, chair_count=?, venue=? WHERE id=?";
    
    if ($stmt = $con->prepare($updateQuery)) {
        $stmt->bind_param("sddiiiiisi", $packageName, $packagePrice, $packageDownpayment, $peopleCount, $menuCount, $venueStyling, $tableCount, $chairCount, $venue, $packageId);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Package updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update package.";
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Database error: Failed to prepare statement.";
    }

    // Handle image upload if a new file is provided
    if (!empty($_FILES["packageImage"]["name"])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES["packageImage"]["name"]);
        $targetFilePath = $targetDir . time() . "_" . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png"];
        
        if (in_array($fileType, $allowedTypes) && move_uploaded_file($_FILES["packageImage"]["tmp_name"], $targetFilePath)) {
            // Update image path in the database
            $imageUpdateQuery = "UPDATE package SET image=? WHERE id=?";
            if ($stmt = $con->prepare($imageUpdateQuery)) {
                $stmt->bind_param("si", $targetFilePath, $packageId);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $_SESSION['error'] = "Invalid file type or upload failed.";
        }
    }
    
    header("Location: package.php");
    exit();
}
?>
