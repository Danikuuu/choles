<?php
session_start();
require_once '../../data-handling/db/connection.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}

$logged_in_user = $_SESSION['user_id'];
$chat_user = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;

if ($chat_user <= 0) {
    echo json_encode(["error" => "Invalid receiver ID"]);
    exit();
}

// Fetch messages between the logged-in user and chat user
$sql = "SELECT m.*, u.fname, u.lname 
        FROM messages m 
        JOIN user u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?) 
        OR (m.sender_id = ? AND m.receiver_id = ?) 
        ORDER BY m.created_at ASC";

$stmt = $con->prepare($sql);
$stmt->bind_param("iiii", $logged_in_user, $chat_user, $chat_user, $logged_in_user);
$stmt->execute();
$result = $stmt->get_result();
$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$update_sql = "UPDATE messages SET status = 'read' WHERE receiver_id = ? AND sender_id = ?";
$update_stmt = $con->prepare($update_sql);
$update_stmt->bind_param("ii", $logged_in_user, $chat_user);
$update_stmt->execute();

echo json_encode($messages);
?>
