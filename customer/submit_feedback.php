<?php
session_start();

// Redirect unauthorized users
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}

require '../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error']= "Unauthorized access. Please log in first";
    }

    $user_id = $_SESSION['user_id'];
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = trim($_POST['comment']);


    if ($rating < 1 || $rating > 5) {
        $_SESSION['error']= "Invalid rating value.";
        header("Location: feedback.php");
    }


    if (empty($comment)) {
        $_SESSION['error']= "Comment is required";
        header("Location: feedback.php");
    }


    $stmt = $con->prepare("INSERT INTO feedback (user_id, rating, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $user_id, $rating, $comment);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Thank you! Your feedback is successfully sent";
        header("Location: feedback.php");
        exit();
    } else {
        $_SESSION['error'] = "Error submitting feedback. Please try again";
        header("Location: feedback.php");
        exit();
    }

    $stmt->close();
    $con->close();
} else {
    $_SESSION['error']= "Invalid request";
    header("Location: feedback.php");
}
?>
