<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require_once '../data-handling/db/connection.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1) {
    header("Location: ../index.php");
    exit();
}

$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["downpayment"])) {
    $reservation_id = $_POST['id'];

    // Use absolute path for target directory
    $targetDir = "downpayment/";

    // Ensure directory exists
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            die("Failed to create directory: " . $targetDir);
        }
    }

    $fileName = basename($_FILES["downpayment"]["name"]);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedTypes = ["jpg", "jpeg", "png"];

    if (in_array($fileType, $allowedTypes)) {
        $uniqueFileName = time() . "_" . $fileName;
        $dbFilePath = "downpayment/" . $uniqueFileName; // Relative path for database
        $targetFilePath = $targetDir . $uniqueFileName; // Absolute path

        // Debug: Check if file exists before moving
        if (!file_exists($_FILES["downpayment"]["tmp_name"])) {
            die("File was not uploaded properly.");
        }

        // Debug: Display paths
        echo "Temp File: " . $_FILES["downpayment"]["tmp_name"] . "<br>";
        echo "Target File Path: " . $targetFilePath . "<br>";

        if (!move_uploaded_file($_FILES["downpayment"]["tmp_name"], $targetFilePath)) {
            die("Failed to move file. Error: " . error_get_last()["message"]);
        }

        // Update database
        $sql = "UPDATE reservations SET down_payment = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("si", $dbFilePath, $reservation_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Downpayment uploaded successfully!";
        } else {
            $_SESSION['error'] = "Failed to update database.";
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
    }

    header("Location: reservation_history.php");
    exit();
}
?>
