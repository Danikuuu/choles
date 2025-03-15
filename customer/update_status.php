<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Check if user is logged in and an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}

// echo 'hello';
require_once '../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

// echo 'hello1';
// Get form data
$reservationId = $_POST['reservationId'] ?? null;
$newStatus = $_POST['status'] ?? null;

// echo $newStatus;

// Validate input
if (empty($reservationId) || empty($newStatus)) {
    echo json_encode(["success" => false, "message" => "Missing reservation ID or status"]);
    exit();
}

// Debugging logs
error_log("Updating reservation ID: $reservationId with status: $newStatus");

// echo 'hello';

// Update status in database
$stmt = $con->prepare("UPDATE reservations SET status = ? WHERE id = ?");
$stmt->bind_param("si", $newStatus, $reservationId);
// echo 'hello';
if ($stmt->execute()) {

    header("Location: reservation_history.php");
    exit();
} else {
    echo json_encode(["success" => false, "message" => "Failed to update status"]);
}

// Close resources
$stmt->close();
$con->close();
?>
