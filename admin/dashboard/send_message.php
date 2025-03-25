<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../data-handling/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
    $message = isset($_POST['message']) ? trim($_POST['message']) : "";

    if ($receiver_id <= 0) {
        echo json_encode(["status" => "error", "error" => "Invalid receiver ID"]);
        exit();
    }

    if (!empty($message)) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, status) VALUES (?, ?, ?, 'unread')";
        $stmt = $con->prepare($sql);
        if (!$stmt) {
            echo json_encode(["status" => "error", "error" => "SQL prepare failed: " . $con->error]);
            exit();
        }
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "error" => "SQL execute failed: " . $stmt->error]);
        }
    } else {
        echo json_encode(["status" => "empty"]);
    }
}
?>
