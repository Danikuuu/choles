<?php
session_start();
include "./data-handling/db/connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    $recaptchaResponse = $_POST["g-recaptcha-response"];

    // Check if reCAPTCHA was completed
    if (empty($recaptchaResponse)) {
        $_SESSION["error"] = "Please complete the reCAPTCHA verification!";
        header("Location: forgot-password.php");
        exit();
    }

    // Verify reCAPTCHA response
    $secretKey = "6LdUMPUqAAAAAFZIGMIsx26RiIwZmCPKsBMBIPew";
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verifyURL . "?secret=" . $secretKey . "&response=" . $recaptchaResponse);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        $_SESSION["error"] = "reCAPTCHA verification failed!";
        header("Location: forgot-password.php");
        exit();
    }

    $stmt = $con->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $otp = rand(100000, 999999);
        $_SESSION["reset_otp"] = $otp;
        $_SESSION["reset_email"] = $email;
        $_SESSION["reset_user_id"] = $user_id;

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
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "<h3>Your OTP for password reset is: <strong>$otp</strong></h3>";

            $mail->send();
            $_SESSION['success'] = "Email sent successfully!";
            header("Location: forgot-password-otp.php");
            exit();
        } catch (Exception $e) {
            header("Location: forgot-password.php");
            $_SESSION['error'] = "Failed to send OTP.";
        }
    } else {
        $_SESSION['error'] = "Email not found.";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Login</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">


</head>

<body>
<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <h1 class="sitename">CHOLES</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="active">Home<br></a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="catering.php">Catering</a></li>
          <li><a href="events.php">Event Styling</a></li>
          <!-- <li><a href="contact.php">Contact Us</a></li> -->
          <li><a href="login.php" class="active">Login</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="signup.php">Create an account</a>

    </div>
  </header>

  <main class="main d-flex align-items-center justify-content-center" style="min-height: 80vh; background-color: #059652;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-4">
          <div class="card shadow p-4">
            <h3 class="text-center">Login</h3>
            <form method="post">
                <div class="p-3" style="z-index: 11">
                    <div id="toastMessage" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <?php
                                if (isset($_SESSION['success'])) {
                                    echo $_SESSION['success'];
                                    unset($_SESSION['success']); // Clear message after showing
                                } elseif (isset($_SESSION['error'])) {
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']); // Clear message after showing
                                }
                                ?>
                            </div>
                            <button type="button" class="btn me-2 m-auto" data-bs-dismiss="toast" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Enter your Email Address</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email" required>
                </div>
                <div class="mb-3 d-flex justify-content-center align-items-center">
                            <div class="g-recaptcha" data-sitekey="6LdUMPUqAAAAALZHOSRgzVY2T5e-usP6Iv3kjoss"></div>
                        </div>
                <button type="submit" class="btn btn-primary w-100">Send OTP</button>

                <p class="text-center mt-3">
                    Remembered your password? <a href="login.php">Login here</a>
                </p>
            </form>

          </div>
        </div>
      </div>
    </div>
  </main>

  <footer id="footer" class="footer position-relative light-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename">CHOLES Catering Services</span>
          </a>
          <div class="footer-contact pt-3">
            <p>Cabanatuan</p>
            <p>Nueva Ecija, Philippines</p>
            <p class="mt-3"><strong>Phone:</strong> <span>+63 912-345-6789</span></p>
            <p><strong>Email:</strong> <span>cholescatering@gmail.com</span></p>
          </div>
          <div class="social-links d-flex mt-4">
            <a href=""><i class="bi bi-facebook"></i></a>
            <a href=""><i class="bi bi-instagram"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="#">Home</a></li>
            <li><a href="#">About us</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Terms of service</a></li>
            <li><a href="#">Privacy policy</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Our Services</h4>
          <ul>
            <li><a href="#">Catering</a></li>
            <li><a href="#">Venue Styling</a></li>
            <li><a href="#">Menu Choosing</a></li>
            <li><a href="#">Chairs and Tables</a></li>
            <li><a href="#">And many more</a></li>
          </ul>
        </div>

      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>©  <strong class="px-1 sitename">CHOLES</strong> <span>All Rights Reserved</span></p>
    </div>

  </footer>
  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>


  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>
  <script>  
    document.addEventListener("DOMContentLoaded", function () {
        var toastEl = document.getElementById('toastMessage');
        if (toastEl && toastEl.textContent.trim() !== "") {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
  </script>
</body>

</html>
