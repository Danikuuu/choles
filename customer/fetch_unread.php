<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}

require_once '../data-handling/db/connection.php';
$user_id = $_SESSION['user_id'];
$sql = "SELECT COUNT(*) as unread_count FROM messages WHERE receiver_id = ? AND status = 'unread'";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

echo json_encode(["unread_count" => $row['unread_count']]);
?>