<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);
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
    $event_type = $_POST["event_type"];
    $event_theme = $_POST["event_theme"];
    $event_venue = $_POST["event_venue"];
    $start_time = date("H:i:s", strtotime($_POST["start_time"]));
    $end_time = date("H:i:s", strtotime($_POST["end_time"]));
    $coupon_code = isset($_POST["coupon"]) ? trim($_POST["coupon"]) : null;
    $coupon_id = null;


    if (!$customer_id || !$package_id || !$event_date || empty($menu_ids) || !$event_type || !$event_theme || !$event_venue || !$start_time || !$end_time) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: package.php");
        exit;
    }

    try {
        // Fetch package price
        $stmt = $con->prepare("SELECT downpayment, package_price FROM package WHERE id = ?");
        $stmt->bind_param("i", $package_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $package_price = $row['package_price'];
        $downpayment_price = $row['downpayment'];
        $stmt->close();

        // Apply coupon discount if valid
        if (!empty($coupon_code)) {
            // Get coupon details
            $stmt = $con->prepare("SELECT id, discount_type, discount_value, status FROM coupons WHERE code = ? AND expiry_date >= CURDATE()");
            $stmt->bind_param("s", $coupon_code);
            $stmt->execute();
            $coupon_result = $stmt->get_result();
        
            if ($coupon_result->num_rows > 0) {
                $coupon = $coupon_result->fetch_assoc();
                $coupon_id = $coupon['id'];
            
                // Check if the coupon has already been used by this customer
                $stmt = $con->prepare("SELECT id FROM used_coupons WHERE customer_id = ? AND coupon_id = ?");
                $stmt->bind_param("ii", $customer_id, $coupon_id);
                $stmt->execute();
                $used_result = $stmt->get_result();
            
                if ($used_result->num_rows > 0) {
                    $_SESSION['error'] = "Coupon already used!";
                    header("Location: package.php");
                    exit;
                }
            
                // Insert into used_coupons table
                $stmt = $con->prepare("INSERT INTO used_coupons (customer_id, coupon_id, used_at) VALUES (?, ?, NOW())");
                $stmt->bind_param("ii", $customer_id, $coupon_id);
                if (!$stmt->execute()) {
                    die("Execution failed (used_coupons): " . $stmt->error);
                }
            
                // Validate if coupon is expired
                if ($coupon['status'] === 'expired') {
                    $_SESSION['error'] = "This coupon is expired.";
                    header("Location: package.php");
                    exit;
                }
            
                // Apply the discount
                $discount_type = $coupon['discount_type'];
                $discount_value = $coupon['discount_value'];
            
                if ($discount_type == 'percentage') {
                    $discount_amount = ($package_price * $discount_value) / 100;
                } elseif ($discount_type == 'fixed') {
                    $discount_amount = min($package_price, $discount_value);
                } else {
                    // If another type of discount exists, apply it here
                    $discount_amount = $discount_value;
                }
            
                // Calculate final price after discount
                $final_price = max(0, $package_price - $discount_amount);
                $discounted_price = $final_price - $downpayment_price;
            
            } else {
                $_SESSION['error'] = "Invalid or expired coupon.";
                header("Location: package.php");
                exit;
            }
            
            $stmt->close();
        } else {
            $final_price = $package_price;
            $discounted_price = $final_price - $downpayment_price;
        }
        

        // Insert into `customer_package_menu`
        $stmt = $con->prepare("INSERT INTO customer_package_menu (customer_id, package_id, menu_id, created_at) VALUES (?, ?, ?, NOW())");
        if (!$stmt) {
            die("Prepare failed (customer_package_menu): " . $con->error);
        }

        foreach ($menu_ids as $menu_id) {
            $stmt->bind_param("iii", $customer_id, $package_id, $menu_id);
            if (!$stmt->execute()) {
                die("Execution failed (customer_package_menu): " . $stmt->error);
            }
        }

        $customer_package_id = $stmt->insert_id;
        $stmt->close();

        // Insert into `reservations`
        $stmt = $con->prepare("INSERT INTO reservations 
            (customer_package_id, event_date, down_payment, refund, refund_img, refund_proof, 
            downpayment_price, balance, status, event_type, event_theme, venue, start_time, end_time, 
            created_at, updated_at) 
            VALUES (?, ?, NULL, 0, NULL, NULL, ?, ?, 'pending', ?, ?, ?, ?, ?, NOW(), NOW())");

        if (!$stmt) {
            die("Prepare failed (reservations): " . $con->error);
        }

        $stmt->bind_param("issssssss", 
            $customer_package_id, $event_date, $downpayment_price, $discounted_price,
            $event_type, $event_theme, $event_venue, $start_time, $end_time
        );

        if (!$stmt->execute()) {
            die("Execution failed (reservations): " . $stmt->error);
        }

        $stmt->close();

        
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
            $mail->Body = " <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9;'>
                                <h3 style='color: #2c3e50;'>Reservation Confirmation</h3>
                                <p style='font-size: 16px; line-height: 1.5;'>Thank you, your reservation has been <strong>received</strong>.</p>
                                <p style='font-size: 16px; line-height: 1.5;'>Please pay for the downpayment for your approval to be processed.</p>

                                <div style='text-align: center; margin: 20px 0;'>
                                    <p style='font-size: 18px;'><strong>Scan the QR code to send the downpayment</strong></p>
                                    <h5 style='font-size: 18px; color: #e74c3c;'>Downpayment: <strong>" . htmlspecialchars($downpayment_price) . "</strong></h5>
                                    <img src='cid:qrcode_img' alt='QR Code' width='200px' style='margin-top: 10px; border-radius: 10px;' />
                                </div>

                                <p style='font-size: 16px; line-height: 1.5; text-align: center;'>
                                    Once paid, <strong>upload the screenshot of the transaction in the reservation history</strong>.<br>
                                    Your overall price is ₱ <strong>" . htmlspecialchars($discounted_price) . "</strong>.
                                </p>

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

    } catch (Exception $e) {
        $con->rollback();
        $_SESSION['error'] = "Failed to add reservation.";
    }

    header("Location: package.php");
    exit;
}
?>
