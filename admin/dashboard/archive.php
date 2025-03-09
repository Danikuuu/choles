<?php
require_once '../../data-handling/db/connection.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $stmt = $con->prepare("UPDATE menu SET status = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Menu added to archive successfully!";
    } else {
        $_SESSION['error'] = "Failed to add to archive!";
    }

    $stmt->close();
    $con->close();
    header("Location: menu.php");
}

?>