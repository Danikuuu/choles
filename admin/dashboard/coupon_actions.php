<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); // Redirect to home or login
    exit();
}
require_once '../../data-handling/db/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $coupon_id = $_POST['coupon_id'];
    $action = $_POST['action'];

    if ($action == 'edit') {
        $code = $_POST['code'];
        $discount_value = $_POST['discount_value'];
        $discount_type = $_POST['discount_type'];
        $expiry_date = $_POST['expiry_date'];

        $stmt = $con->prepare("UPDATE coupons SET code = ?, discount_type = ?, discount_value = ?, expiry_date = ? WHERE id = ?");

        $stmt->bind_param("ssssi", $code, $discount_type, $discount_value, $expiry_date, $coupon_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Coupon updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update coupon.";
        }
        $stmt->close();
    }

    if ($action == 'reactivate') {
        $stmt = $con->prepare("UPDATE coupons SET status = 'active' WHERE id = ?");
        $stmt->bind_param("i", $coupon_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Coupon reactivated successfully!";
        } else {
            $_SESSION['error'] = "Failed to reactivate coupon.";
        }
        $stmt->close();
    }

    if ($action == 'deactivate') {
        $stmt = $con->prepare("UPDATE coupons SET status = 'expired' WHERE id = ?");
        $stmt->bind_param("i", $coupon_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Coupon deactivated successfully!";
        } else {
            $_SESSION['error'] = "Failed to deactivate coupon.";
        }
        $stmt->close();
    }

    header("Location: coupon.php");
    exit();
}
?>
