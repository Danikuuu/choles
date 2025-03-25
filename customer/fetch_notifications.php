<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../data-handling/db/connection.php';

// Check if user is logged in and has the correct role
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    die(json_encode(["error" => "Unauthorized access"]));
}

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

$notifications = [];

// Prepare the query for approved or completed reservations
$query_reservations = "SELECT id, event_type, DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') AS date 
                        FROM reservations 
                        WHERE (status = 'approved' OR status = 'completed') 
                        AND customer_id = ?
                        ORDER BY created_at DESC;";

$stmt_reservations = $con->prepare($query_reservations);
$stmt_reservations->bind_param("i", $user_id);
$stmt_reservations->execute();
$result_reservations = $stmt_reservations->get_result();

// Add new reservation notifications
while ($row = $result_reservations->fetch_assoc()) {
    $notifications[] = [
        'message' => "New reservation for {$row['event_type']}",
        'date' => $row['date']
    ];
}

// Prepare the query for cancelled or refunded reservations
$query_refunds = "SELECT id, event_type, DATE_FORMAT(created_at, '%b %d, %Y %h:%i %p') AS date 
                    FROM reservations 
                    WHERE (status = 'cancelled' OR status = 'refunded') 
                    AND customer_id = ?
                    ORDER BY created_at DESC;";

$stmt_refunds = $con->prepare($query_refunds);
$stmt_refunds->bind_param("i", $user_id);
$stmt_refunds->execute();
$result_refunds = $stmt_refunds->get_result();

// Add refund request notifications
while ($row = $result_refunds->fetch_assoc()) {
    $notifications[] = [
        'message' => "Refund requested for {$row['event_type']}",
        'date' => $row['date']
    ];
}

// Close prepared statements
$stmt_reservations->close();
$stmt_refunds->close();
$con->close();

// Return JSON response
echo json_encode($notifications);
?>
