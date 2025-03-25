<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../data-handling/db/connection.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    die(json_encode(["error" => "Unauthorized access"]));
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT m.sender_id, u.fname, COUNT(CASE WHEN m.status = 'unread' THEN 1 END) AS unread_count
        FROM messages m
        JOIN user u ON m.sender_id = u.id
        WHERE m.receiver_id = ? 
        GROUP BY m.sender_id";



$stmt = $con->prepare($sql);

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {
    die(json_encode(["error" => "SQL Execution Error: " . $stmt->error]));
}

$result = $stmt->get_result();

$inbox = [];
while ($row = $result->fetch_assoc()) {
    $inbox[] = $row;
}

echo json_encode($inbox);
?>
