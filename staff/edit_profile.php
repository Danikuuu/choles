<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in and an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 1) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}

require_once '../data-handling/db/connection.php';

// Get logged-in user ID
$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT fname, lname, email, mobile, province, city, barangay, street FROM user WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $province = $_POST["province"];
    $city = $_POST["city"];
    $barangay = $_POST["barangay"];
    $street = $_POST["street"];
    $password = trim($_POST["password"]);


    // Fix: Removed extra comma before WHERE
    if(!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET fname=?, lname=?, password= ?, province=?, city=?, barangay=?, street=? WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("sssssssi", $fname, $lname, $hashedPassword, $province, $city, $barangay, $street, $user_id);
    } else {
        $sql = "UPDATE user SET fname=?, lname=?, province=?, city=?, barangay=?, street=? WHERE id=?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssssssi", $fname, $lname, $province, $city, $barangay, $street, $user_id);
    }


    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating successfully!";
    }
}
?>