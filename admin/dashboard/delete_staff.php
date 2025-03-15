<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); 
    exit();
}

require_once '../../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if (empty($id)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: staff.php");
        exit();
    }

    $stmt = $con->prepare("DELETE from user WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting staff: ";
    }

    $stmt->close();
    $con->close();
    
    header("Location: staff.php"); 
    exit();
}
?>
