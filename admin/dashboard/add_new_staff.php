<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); 
    exit();
}

require_once '../../data-handling/db/connection.php';
require_once '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = trim($_POST['staffFname']);
    $lname = trim($_POST['staffLname']);
    $email = trim($_POST['staffEmail']);
    $mobile = trim($_POST['staffMobile']);
    $province = $_POST["province_name"];
    $city = $_POST["city_name"];
    $barangay = $_POST["barangay_name"];
    $street = $_POST["street_name"];

    $password = password_hash($fname, PASSWORD_DEFAULT);

    $stmt = $con->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if (empty($fname) || empty($lname) || empty($email) || empty($mobile) || empty($province) || empty($city) || empty($barangay) || empty($street)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: staff.php");
        exit();
    }
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already exists!";
    } else {
        $stmt = $con->prepare("INSERT INTO user (fname, lname, email, mobile, password, province, city, barangay, street, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 2)");
        $stmt->bind_param("sssisssss", $fname, $lname, $email, $mobile, $password, $province, $city, $barangay, $street);

        if ($stmt->execute()) {
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'cholescatering@gmail.com';
                $mail->Password = 'kuse tvje epft vvuq';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;


                $mail->setFrom('cholescatering@gmail.com', 'CHOLES Support');
                $mail->addAddress($email, "$fname $lname");

                $mail->isHTML(true);
                $mail->Subject = 'Welcome to CHOLES';
                $mail->Body = "<h3>Hello $fname,</h3>
                            <p>Welcome to <strong>CHOLES</strong></p>
                            <p>You've been added as one of our staff. Your email to sign in is: <strong>$email</strong>, and your password is: <strong>$fname</strong></p>";

                $mail->send();

                $_SESSION['success'] = "Staff added successfully and email sent!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Failed to send email. Error: " . $mail->ErrorInfo;
            }
            $_SESSION['success'] = "Staff added successfully!";   
        } else {
            $_SESSION['error'] = "Error adding staff: ";  
        }

        $stmt->close();
        $con->close();
        header("Location: staff.php"); 
        exit();
    }
    header("Location: staff.php"); 
    exit();
}
?>
