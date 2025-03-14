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
    if (empty($_POST['menuId'])) {
        die("Error: Menu ID is missing.");
    }

    $menuId = $con->real_escape_string($_POST['menuId']);

    // Fetch existing menu details
    $query = "SELECT * FROM menu WHERE id='$menuId'";
    $result = $con->query($query);

    if (!$result) {
        die("SQL Error: " . $con->error);
    }

    if ($result->num_rows > 0) {
        $menu = $result->fetch_assoc();

        // Fix category issue
        $menuName = !empty($_POST['menuName']) ? $con->real_escape_string($_POST['menuName']) : $menu['name'];
        $menuDescription = !empty($_POST['menuDescription']) ? $con->real_escape_string($_POST['menuDescription']) : $menu['description'];
        $editMenuCategory = !empty($_POST['editMenuCategory']) ? $con->real_escape_string($_POST['editMenuCategory']) : $menu['category'];

        $imageQuery = "";

        if (!empty($_FILES["menuImage"]["name"])) {
            $targetDir = "uploads/";
            $fileName = basename($_FILES["menuImage"]["name"]);
            $targetFilePath = $targetDir . "_" . time() . "_" . $fileName; // ✅ Fixed variable
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

            $allowedTypes = array("jpg", "jpeg", "png", "gif");
            if (in_array(strtolower($fileType), $allowedTypes)) {
                if (move_uploaded_file($_FILES["menuImage"]["tmp_name"], $targetFilePath)) {
                    $imageQuery = ", image='$targetFilePath'"; // ✅ Save correct path
                } else {
                    $_SESSION['error'] = "Error uploading file!";
                    header("Location: menu.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Only JPG, JPEG, PNG & GIF files are allowed.";
                header("Location: menu.php");
                exit();
            }
        }

        // Fix SQL query
        $sql = "UPDATE menu SET 
                    name='$menuName', 
                    description='$menuDescription', 
                    category='$editMenuCategory' 
                    $imageQuery 
                WHERE id='$menuId'";

        if ($con->query($sql) === TRUE) {
            $_SESSION['success'] = "Menu updated successfully!";
            header("Location: menu.php");
            exit();
        } else {
            $_SESSION['error'] = "An error occurred: " . $con->error;
            header("Location: menu.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Menu not found!";
        header("Location: menu.php");
        exit();
    }

    $con->close();
} else {
    die("Invalid request.");
}
?>
