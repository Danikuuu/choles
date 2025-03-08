<?php
session_start();
require_once '../../data-handling/db/connection.php';

if (!isset($_SESSION["user_id"]) && $_SESSION["role"] !== 1) {
    header("Location: ../index.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $packageName = $_POST['packageName'];
    $packageDescription = $_POST['packageDescription'];
    $packagePrice = $_POST['packagePrice'];
    $packagePeople = $_POST['packagePeople'];
    $packageMenu = $_POST['packageMenu'];
    $packagestyling = $_POST['packagestyling'];
    $packageTables = $_POST['packageTables'];
    $packageChairs = $_POST['packageChairs'];
    $packageGlass = $_POST['packageGlass'];
    $packagePlates = $_POST['packagePlates'];
    $packageSpoon = $_POST['packageSpoon'];
    $packageFork = $_POST['packageFork'];
    $packageVenue = $_POST['packageVenue'];


    // File upload setup
    $targetDir = "uploads/"; 
    $fileName = basename($_FILES["packageImage"]["name"]);
    $targetFilePath = $targetDir . time() . "_" . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    
    // Allowed file types
    $allowedTypes = ["jpg", "jpeg", "png"];

    
    if ($_FILES["packageImage"]["error"] == 0 && in_array($fileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["packageImage"]["tmp_name"], $targetFilePath)) {
            // Insert package into database
            $sql = "INSERT INTO package (package_name, description, package_price, people_count, menu_count, venue_styling, table_count, chair_count, glass_count, plate_count, spoon_count, fork_count, venue, image, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";


            $stmt = $con->prepare($sql);
            // echo 'here';
            $stmt->bind_param("ssdiiiiiiiiiss", 
                $packageName, $packageDescription, $packagePrice, $packagePeople, 
                $packageMenu, $packagestyling, $packageTables, $packageChairs, $packageGlass,
                $packagePlates, $packageSpoon, $packageFork, $packageVenue, 
                $targetFilePath
            );
            echo "dito";
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Package added successfully!";
            } else {
                $_SESSION['error'] = "Failed to add package.";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Error uploading image.";
        }
    } else {
        $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
    }

    header("Location: package.php");
    exit();
}
?>
