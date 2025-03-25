<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../data-handling/db/connection.php';


if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    die(json_encode(["error" => "Unauthorized access"]));
}

header('Content-Type: application/json');


$query_reservations = "SELECT id, event_type, DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') as date 
                        FROM reservations 
                        WHERE status = 'pending'
                        ORDER BY created_at DESC";

$query_refunds = "SELECT id, event_type, DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') AS date 
                    FROM reservations 
                    WHERE status = 'cancelled' 
                    ORDER BY created_at DESC;";

$result_reservations = $con->query($query_reservations);
$result_refunds = $con->query($query_refunds);

$notifications = [];

// Add new reservation notifications
while ($row = $result_reservations->fetch_assoc()) {
    $notifications[] = [
        'message' => "New reservation for {$row['event_type']}",
        'date' => $row['date']
    ];
}

// Add refund request notifications
while ($row = $result_refunds->fetch_assoc()) {
    $notifications[] = [
        'message' => "Refund requested for {$row['event_type']}",
        'date' => $row['date']
    ];
}

echo json_encode($notifications);
?>
