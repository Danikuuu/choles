<?php
session_start();
require_once '../data-handling/db/connection.php';

if (!isset($_SESSION["user_id"])) {
    die(json_encode(["error" => "Unauthorized access"]));
}

header('Content-Type: application/json');
$user_id = $_SESSION['user_id'];

$query = "SELECT COUNT(*) AS unread_count FROM coupons WHERE status = 'active'";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
$con->close();

echo json_encode(["unread_count" => $row['unread_count']]);
?>