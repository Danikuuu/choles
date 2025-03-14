<?php
session_start();
include "./data-handling/db/connection.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $email = $_POST["email"];
    $mobile = $_POST["mobile"];
    $password = $_POST["password"];
    $province = $_POST["province_name"];
    $city = $_POST["city_name"];
    $barangay = $_POST["barangay_name"];
    $street = $_POST["street"];
    $role = 0;

    $stmt = $con->prepare("SELECT id FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Email already exists!";
    } else {
        $_SESSION["registration_data"] = [
            "fname" => $fname,
            "lname" => $lname,
            "email" => $email,
            "mobile" => $mobile,
            "password" => password_hash($password, PASSWORD_DEFAULT),
            "province" => $province,
            "city" => $city,
            "barangay" => $barangay,
            "street" => $street,
            "role" => $role
        ];

        $otp = rand(100000, 999999);
        $_SESSION["otp"] = $otp;

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
            $mail->Subject = 'OTP Verification - CHOLES';
            $mail->Body = "<h3>Hello $fname,</h3>
                           <p>Your OTP code is: <strong>$otp</strong></p>
                           <p>Please enter this code to verify your account.</p>";

            $mail->send();

            header("Location: otp.php");
            exit();
        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
            $error = "Failed to send OTP. Please try again.";
        }
    }

    $stmt->close();
    $con->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Signup</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
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
          <li><a href="index.php">Home<br></a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="catering.php">Catering</a></li>
          <li><a href="events.php">Event Styling</a></li>
          <!-- <li><a href="contact.php">Contact Us</a></li> -->
          <li><a href="login.php">Login</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="signup.php">Create an account</a>

    </div>
  </header>

  <main class="main d-flex align-items-center justify-content-center p-2" style="min-height: 80vh; background-color: #059652;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card shadow p-4">
            <h3 class="text-center">Sign Up</h3>
            <form method="post">
              <?php if (isset($error)) : ?>
                <div class="alert alert-danger text-center">
                  <?php echo $error; ?>
                </div>
              <?php endif; ?>

              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="fname" class="form-control" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="lname" class="form-control" required>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Mobile</label>
                <input type="text" name="mobile" class="form-control" required>
              </div>

              <div class="mb-3">
              <label class="form-label" for="province">Province</label>
                <select id="province" class="form-control" required name="province">
                    <option value="">Select Province</option>
                </select>
                <input type="hidden" id="province_name" name="province_name"> <!-- Hidden input -->
              </div>

              <div class="mb-3">
                <label class="form-label" for="city">City</label>
                <select id="city" class="form-control" required name="city">
                    <option value="">Select City</option>
                </select>
                <input type="hidden" id="city_name" name="city_name"> <!-- Hidden input -->
              </div>

              <div class="mb-3">
                <label class="form-label" for="barangay">Barangay</label>
                <select id="barangay" class="form-control" required name="barangay">
                    <option value="">Select Barangay</option>
                </select>
                <input type="hidden" id="barangay_name" name="barangay_name"> <!-- Hidden input -->
              </div>


              <div class="mb-3">
                <label class="form-label">Street</label>
                <input type="text" name="street" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>

              <button type="submit" class="btn btn-primary w-100">Sign Up</button>
              <p class="text-center mt-3">Already have an account? <a href="login.html">Login</a></p>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>

<script>
let provinces = [];
let cities = [];
let barangays = [];

$(document).ready(function() {
    // Load Province Data
    $.getJSON("province.json", function(data) {
        provinces = data;
        $.each(provinces, function(index, province) {
            $("#province").append(`<option value="${province.province_code}">${province.province_name}</option>`);
        });
    });

    // Load City Data
    $.getJSON("city.json", function(data) {
        cities = data;
    });

    // Load Barangay Data
    $.getJSON("barangay.json", function(data) {
        barangays = data;
    });

    // Province Change Event
    $("#province").change(function() {
        let selectedProvinceCode = $(this).val();
        let selectedProvince = provinces.find(province => province.province_code === selectedProvinceCode);

        $("#city").html('<option value="">Select City</option>');
        $("#barangay").html('<option value="">Select Barangay</option>');

        // Store province name in the hidden input
        $("#province_name").val(selectedProvince ? selectedProvince.province_name : "");

        $.each(cities, function(index, city) {
            if (city.province_code === selectedProvinceCode) {
                $("#city").append(`<option value="${city.city_code}">${city.city_name}</option>`);
            }
        });
    });

    // City Change Event
    $("#city").change(function() {
        let selectedCityCode = $(this).val();
        let selectedCity = cities.find(city => city.city_code === selectedCityCode);

        $("#barangay").html('<option value="">Select Barangay</option>');

        // Store city name in the hidden input
        $("#city_name").val(selectedCity ? selectedCity.city_name : "");

        $.each(barangays, function(index, barangay) {
            if (barangay.city_code === selectedCityCode) {
                $("#barangay").append(`<option value="${barangay.brgy_code}">${barangay.brgy_name}</option>`);
            }
        });
    });

    // Barangay Change Event
    $("#barangay").change(function() {
        let selectedBarangayCode = $(this).val();
        let selectedBarangay = barangays.find(brgy => brgy.brgy_code === selectedBarangayCode);

        // Store barangay name in the hidden input
        $("#barangay_name").val(selectedBarangay ? selectedBarangay.brgy_name : "");
    });
});

</script>
</body>
</html>
