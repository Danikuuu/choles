<?php
session_start();
require_once '../data-handling/db/connection.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Unauthorized access"]);
    exit;
}

// Check if coupon_id is received
if (!isset($_POST['coupon_id'])) {
    echo json_encode(["success" => false, "message" => "Coupon ID is missing"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$coupon_id = $_POST['coupon_id'];

// Check if coupon exists
$query_check = "SELECT id FROM coupons WHERE id = ?";
$stmt_check = $con->prepare($query_check);
$stmt_check->bind_param("i", $coupon_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Invalid coupon"]);
    exit;
}

// Insert into claimed_coupons
$query_claim = "INSERT INTO claimed_coupon (user_id, coupon_id, claimed_at) VALUES (?, ?, NOW())";
$stmt_claim = $con->prepare($query_claim);
$stmt_claim->bind_param("ii", $user_id, $coupon_id);

if ($stmt_claim->execute()) {
    echo json_encode(["success" => true, "message" => "Coupon claimed successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to claim coupon"]);
}

// Close connections
$stmt_check->close();
$stmt_claim->close();
$con->close();
?>
