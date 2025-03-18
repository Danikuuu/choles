<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';
require_once '../../data-handling/db/connection.php';

$mail = new PHPMailer(true);

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php");
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["refund_proof"])) {
    $reservation_id = $_POST['id'];

    $sql = "SELECT 
        r.id AS reservation_id,
        cpm.id AS customer_package_id,
        CONCAT(c.fname, ' ', c.lname) AS customer_name,
        c.email,
        r.event_date,
        r.status,
        r.downpayment_price,
        r.down_payment,
        r.refund_img,
        r.refund_proof,
        p.package_name,
        p.package_price,
        p.people_count,
        p.venue,
        m.name AS menu_name,
        m.description AS menu_description,
        m.category AS menu_category,
        m.image AS menu_image
    FROM customer_package_menu cpm
    JOIN reservations r ON cpm.id = r.customer_package_id 
    JOIN package p ON cpm.package_id = p.id
    JOIN menu m ON cpm.menu_id = m.id
    JOIN user c ON cpm.customer_id = c.id
    WHERE r.id = ?"; // Filtering by reservation_id

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();


    $email = $reservation['email'];
    $name = $reservation['customer_name'];

    // Use absolute path for target directory
    $targetDir = "refund_proof/";

    // Ensure directory exists
    if (!is_dir($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            die("Failed to create directory: " . $targetDir);
        }
    }

    $fileName = basename($_FILES["refund_proof"]["name"]);
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedTypes = ["jpg", "jpeg", "png"];

    if (in_array($fileType, $allowedTypes)) {
        $uniqueFileName = time() . "_" . $fileName;
        $dbFilePath = "refund_proof/" . $uniqueFileName; // Relative path for database
        $targetFilePath = $targetDir . $uniqueFileName; // Absolute path

        // Debug: Check if file exists before moving
        if (!file_exists($_FILES["refund_proof"]["tmp_name"])) {
            die("File was not uploaded properly.");
        }

        // Debug: Display paths
        echo "Temp File: " . $_FILES["refund_proof"]["tmp_name"] . "<br>";
        echo "Target File Path: " . $targetFilePath . "<br>";

        if (!move_uploaded_file($_FILES["refund_proof"]["tmp_name"], $targetFilePath)) {
            die("Failed to move file. Error: " . error_get_last()["message"]);
        }

        $status = 'refunded';

        // Update database
        $sql = "UPDATE reservations SET refund_proof = ?, status = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ssi", $dbFilePath, $status, $reservation_id);

        if ($stmt->execute()) {
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'cholescatering@gmail.com';
                $mail->Password = 'kuse tvje epft vvuq'; // Use correct App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
            
                $mail->setFrom('cholescatering@gmail.com', 'CHOLES Support');
                $mail->addAddress($email, $name); // Ensure $name is defined
            
                // Check if the file exists before attaching
                if (file_exists($targetFilePath)) {
                    $mail->addAttachment($targetFilePath, "Refund_Proof.$fileType");
                    $mail->addEmbeddedImage($targetFilePath, 'refund_proof_img');
                } else {
                    die("Attachment file not found: " . $targetFilePath);
                }
            
                $mail->isHTML(true);
                $mail->Subject = 'Refund Success';
                $mail->Body = "
                                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; background-color: #f9f9f9; text-align: center;'>
                                    <h2 style='color: #2c3e50;'>🎉 Refund Processed Successfully! 🎉</h2>
                                    <p style='font-size: 18px; color: #333;'>Hello <strong style='color: #d35400;'>$name</strong>,</p>
                                    <p style='font-size: 16px; color: #555;'>Your refund has <strong style='color: #27ae60;'>Arrived</strong>! 🎊</p>
                                    <p style='font-size: 16px; color: #555;'>Below is the proof of refund:</p>
                                    <div style='margin: 20px 0;'>
                                        <img src='cid:refund_proof_img' alt='Refund Proof' width='300px' style='border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);' />
                                    </div>
                                    <p style='font-size: 16px; color: #555;'>If you have any questions, feel free to <a href='mailto:support@cholescatering.com' style='color: #2980b9; text-decoration: none;'>contact our support team</a>.</p>
                                    <p style='font-size: 16px; color: #333;'>Thank you for choosing <strong style='color: #d35400;'>CHOLES</strong>! 😊</p>
                                    <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                                    <p style='font-size: 14px; color: #999;'>This is an automated message. Please do not reply.</p>
                                </div>
                            ";
            
                $mail->send();
                $_SESSION['success'] = "Refund proof uploaded successfully and email sent!";
                header("Location: reservation.php");
            } catch (Exception $e) {
                $_SESSION['error'] = "Failed to send email. Error: ";
                header("Location: reservation.php");
            }
            $_SESSION['success'] = "refund_proof uploaded successfully!";
            header("Location: reservation.php");
        } else {
            $_SESSION['error'] = "Failed to update database.";
            header("Location: reservation.php");
        }

        $stmt->close();
    } else {
        $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG are allowed.";
        header("Location: reservation.php");
    }

    header("Location: reservation.php");
    exit();
}
?>