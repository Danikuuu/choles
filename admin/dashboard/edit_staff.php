<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); 
    exit();
}

require_once '../../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);

    if (empty($id) || empty($fname) || empty($lname) || empty($email) || empty($mobile)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: staff.php");
        exit();
    }

    $stmt = $con->prepare("UPDATE user SET fname = ?, lname = ?, email = ?, mobile = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $fname, $lname, $email, $mobile, $id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff details updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating staff: ";
    }

    $stmt->close();
    $con->close();

    header("Location: staff.php"); 
    exit();
}
?>
