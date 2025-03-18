<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in and an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); // Redirect to home or login
    exit();
}

require_once '../../data-handling/db/connection.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

// Get form data
$reservationId = $_POST['reservationId'] ?? null;
$newStatus = $_POST['status'] ?? null;

if (empty($reservationId) || empty($newStatus)) {
    $_SESSION["error"] = "Missing reservation ID or status";
    exit();
}

$downpayment = "SELECT p.id AS package_id, p.downpayment 
                     FROM customer_package_menu cpm
                     JOIN reservations r ON cpm.id = r.customer_package_id
                     JOIN package p ON cpm.package_id = p.id
                     WHERE r.id = ?";

$stmt = $con->prepare($downpayment);
$stmt->bind_param("i", $reservationId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $packageId = $row['package_id'];  // Fetch package ID
    $downpayment = $row['downpayment'];  
    $refund = $downpayment;
} else {
    $_SESSION['error'] = "Package ID or downpayment not found";
    exit();
}

// Retrieve user email based on reservation ID
$query = "SELECT c.email, CONCAT(c.fname, ' ', c.lname) AS customer_name 
          FROM reservations r
          JOIN customer_package_menu cpm ON cpm.id = r.customer_package_id
          JOIN user c ON cpm.customer_id = c.id
          WHERE r.id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $reservationId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION["error"] =  "User not found";
    exit();
}

$email = $user['email'];
$fname = $user['customer_name'] ;


// Update reservation status
$stmt = $con->prepare("UPDATE reservations SET status = ?, refund = ? WHERE id = ?");
$stmt->bind_param("sii", $newStatus, $refund, $reservationId);

if ($stmt->execute()) {
    
    // Send email notification
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cholescatering@gmail.com';
        $mail->Password = 'kuse tvje epft vvuq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('cholescatering@gmail.com', 'CHOLES Support');
        $mail->addAddress($email, "$lname");

        $mail->isHTML(true);
                    $mail->Subject = 'Reservation ' .$newStatus. ' - CHOLES';
            $mail->Body = " <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;'>
                                <h3 style='color: #2c3e50;'>Reservation Cancelled</h3>
                                <p style='font-size: 16px; line-height: 1.5;'>Thank you, your reservation has been <strong>cancelled</strong>.</p>
                                <p style='font-size: 16px; line-height: 1.5;'>Please wait for your refund.</p>

                                <div style='text-align: center; margin: 20px 0;'>
                                    <p style='font-size: 18px;'><strong>Expect your refund to be sent within a day</strong></p>
                                    <h5 style='font-size: 18px; color: #e74c3c;'>Refund Amount: <strong>" . htmlspecialchars($refund) . "</strong></h5>
                                    <p style='font-size: 18px; color:rgb(245, 33, 9);>Since you cancelled you reservation the refund ampunt will only be 80%</p>
                                </div>
                            </div>
                        ";
            $mail->send();
        $_SESSION["success"] = "Reservation updated successfully";
        header("Location: reservation.php");
        exit();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        $_SESSION["error"] = "Failed to send OTP. Please try again.";
    }
    $_SESSION["error"] = "An error occured";
    header("Location: reservation.php");
    exit();
} else {
    $_SESSION["error"] = "Failed to update status";
}

// Close resources
$stmt->close();
$con->close();
?>
