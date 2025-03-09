<?php
session_start();
require_once '../../data-handling/db/connection.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $menuName = $_POST['menuName'];
    $menuDescription = $_POST['menuDescription'];
    $menuCategory = $_POST['menuCategory'];

    $targetDir = "uploads/"; 

    $fileName = basename($_FILES["menuImage"]["name"]);
    $targetFilePath = $targetDir . "_" . time() . "_" . $fileName; // Add timestamp to file name
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allowed file types
    $allowedTypes = ["jpg", "jpeg", "png"];

    if (in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["menuImage"]["tmp_name"], $targetFilePath)) {
            $sql = "INSERT INTO menu (name, description, category, image, created_at) VALUES (?, ?, ?, ?, NOW())";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("sss", $menuName, $menuDescription, $menuCategory, $targetFilePath);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Menu added successfully!";
            } else {
                $_SESSION['error'] = "Failed to add menu.";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error uploading image.";
        }
    } else {
        $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
    }

    header("Location: menu.php");
    exit();
}

?>
