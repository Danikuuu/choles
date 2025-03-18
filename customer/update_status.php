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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

// echo 'hello1';
// Get form data
$reservationId = $_POST['reservationId'] ?? null;
$newStatus = $_POST['status'] ?? null;
$customer_email = $_SESSION['email'];
$customer_fname = $_SESSION['fname'];
$customer_lname = $_SESSION['lname'];

// echo $newStatus;

// Validate input
if (empty($reservationId) || empty($newStatus)) {
    echo json_encode(["success" => false, "message" => "Missing reservation ID or status"]);
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
    $refund = $downpayment * 0.80; // Calculate 80% refund
} else {
    $_SESSION['error'] = "Package ID or downpayment not found";
    exit();
}

// Debugging logs
error_log("Updating reservation ID: $reservationId with status: $newStatus");

// echo 'hello';

// Update status in database
$stmt = $con->prepare("UPDATE reservations SET status = ?, refund = ? WHERE id = ?");
$stmt->bind_param("sdi", $newStatus, $refund, $reservationId);
// echo 'hello';
if ($stmt->execute()) {

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
            $mail->addAddress($customer_email, "$customer_fname $customer_lname");

            $mail->isHTML(true);
            $mail->addEmbeddedImage('../assets/img/qrcode.png', 'qrcode_img');

            $mail->Subject = 'Reservation Cancelled - CHOLES';
            $mail->Body = " <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;'>
                                <h3 style='color: #2c3e50;'>Reservation Cancelled</h3>
                                <p style='font-size: 16px; line-height: 1.5;'>Thank you, your reservation has been <strong>cancelled</strong>.</p>
                                <p style='font-size: 16px; line-height: 1.5;'>Please wait for your refund.</p>

                                <div style='text-align: center; margin: 20px 0;'>
                                    <p style='font-size: 18px;'><strong>Expect your refund to be sent within a day</strong></p>
                                    <h5 style='font-size: 18px; color: #e74c3c;'>Refund Amount: <strong>" . htmlspecialchars($refund) . "</strong></h5>
                                    <p style='font-size: 18px; color:rgb(245, 33, 9);>Since you cancelled you reservation the refund ampunt will only be 80%</p>
                                </div>

                                <div style='margin-top: 20px; padding: 10px; background: #f2f2f2; border-left: 5px solid #e67e22;'>
                                    <h5 style='margin: 0; color: #e67e22;'>Note:</h5>
                                    <p style='margin: 5px 0; font-size: 14px;'>
                                        Once you cancel a reservation that already has a downpayment, 
                                        <strong>your refund will only be 80% of your total downpayment</strong>.
                                    </p>
                                </div>
                            </div>
                        ";
            $mail->send();
            $_SESSION['success'] = "Reservation added successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Reservation saved, but email failed: " . $mail->ErrorInfo;
        }

    header("Location: reservation_history.php");
    exit();
} else {
    echo json_encode(["success" => false, "message" => "Failed to update status"]);
}

// Close resources
$stmt->close();
$con->close();
?>
