<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); 
    exit();
}

require_once '../../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = "CHOLES" . strtoupper(substr(str_replace(['a', 'b', 'c', 'd', 'e', 'f'], rand(0, 9), md5(time())), 0, 6));
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $expiry_date = $_POST['expiry_date'];

    $query = "INSERT INTO coupons (code, discount_type, discount_value, expiry_date) 
              VALUES ('$code', '$discount_type', '$discount_value', '$expiry_date')";
    
    if (mysqli_query($con, $query)) {
        $_SESSION['success'] = 'Coupon created successfully';
    } else {
        $_SESSION['error'] = 'Error creating coupon';
    }
}
?>
