<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["user_id"]) && isset($_SESSION["role"]) && $_SESSION["role"] == 0) {
    $customer_id = $_SESSION["user_id"];  
    $customer_email = $_SESSION["email"]; 
    $customer_fname = $_SESSION["fname"];
    $customer_lname = $_SESSION["lname"];  
    $package_id = $_POST['package_id'] ?? null;
    $event_date = $_POST['event_date'] ?? null;
    $menu_ids = $_POST['menu_id'] ?? [];

    if (!$customer_id || !$package_id || !$event_date || empty($menu_ids)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: package.php");
        exit;
    }
    

    try {
        // Fetch downpayment price from the package table
        $stmt = $con->prepare("SELECT downpayment FROM package WHERE id = ?");
        $stmt->bind_param("i", $package_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $downpayment_price = $row['downpayment'] ?? null;
        echo $downpayment_price;
        $stmt->close();

        // Insert into customer_package_menu
        $stmt = $con->prepare("INSERT INTO customer_package_menu (customer_id, package_id, menu_id, created_at) VALUES (?, ?, ?, NOW())");
        foreach ($menu_ids as $menu_id) {
            $stmt->bind_param("iii", $customer_id, $package_id, $menu_id);
            $stmt->execute();
        }
        $customer_package_id = $stmt->insert_id;
        $stmt->close();

        // Insert into reservations with the retrieved downpayment_price
        $stmt = $con->prepare("INSERT INTO reservations (customer_package_id, event_date, down_payment, downpayment_price, status, created_at, updated_at) VALUES (?, ?, ?, ?, 'pending', NOW(), NOW())");
        $downpayment = null; // Set downpayment as NULL if not yet paid
        $stmt->bind_param("isss", $customer_package_id, $event_date, $downpayment, $downpayment_price);
        $stmt->execute();
        $stmt->close();

        $con->commit();

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

            $mail->Subject = 'Reservation Success - CHOLES';
            $mail->Body = "<h3>Reservation has been sent,</h3>
                        <p>Thank you, your reservation has been <strong>received</strong>.</p>
                        <p>Please pay for the downpayment for your approval to be processed.</p>
                        <br>
                        <p>Scan the qr code to send the downpayment</p>
                        <h5>Downpayment: <strong>" . htmlspecialchars($downpayment_price) . "</strong></h5>
                        <br>
                        <br>
                        <img src='cid:qrcode_img' alt='QR Code' width='200px' />
                        
                        <br>
                        <br>
                        <p> Once paid, <strong>Uplaod the screenshot of the transaction in the reservation history</strong></p>"
                        ;
            $mail->send();

            $_SESSION['success'] = "Reservation added successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Reservation saved, but email failed: " . $mail->ErrorInfo;
        }

    } catch (Exception $e) {
        $con->rollback();
        $_SESSION['error'] = "Failed to add reservation.";
    }

    header("Location: package.php");
    exit;
}
?>
