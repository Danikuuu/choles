<?php
session_start();
require_once '../data-handling/db/connection.php';

if (!isset($_SESSION["user_id"])) {
    die(json_encode(["error" => "Unauthorized access"]));
}

header('Content-Type: application/json');
$user_id = $_SESSION['user_id'];
$coupons = [];

// Query to fetch coupons that the user has not claimed yet
$query_coupons = "
    SELECT c.id, c.code, c.discount_value, c.discount_type, c.expiry_date, c.status
    FROM coupons c
    LEFT JOIN claimed_coupon cc ON c.id = cc.coupon_id AND cc.user_id = ?
    WHERE cc.coupon_id IS NULL
    ORDER BY c.expiry_date ASC;
";

$stmt_coupons = $con->prepare($query_coupons);
$stmt_coupons->bind_param("i", $user_id);
$stmt_coupons->execute();
$result_coupons = $stmt_coupons->get_result();

while ($row = $result_coupons->fetch_assoc()) {
    $coupons[] = [
        'id' => $row['id'],
        'code' => $row['code'],
        'discount_type' => $row['discount_type'],
        'discount_value' => $row['discount_value'],
        'expiry_date' => date('M d, Y', strtotime($row['expiry_date'])),
        'status' => ucfirst($row['status'])
    ];
}

$stmt_coupons->close();
$con->close();

echo json_encode($coupons);
?>
