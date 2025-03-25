<?php
session_start();
include "./data-handling/db/connection.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$con->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim(htmlspecialchars($_POST["email"]));
    $password = trim($_POST["password"]);
    $recaptchaResponse = $_POST["g-recaptcha-response"];

    // Check if reCAPTCHA was completed
    if (empty($recaptchaResponse)) {
        $_SESSION["error"] = "Please complete the reCAPTCHA verification!";
        header("Location: login.php");
        exit();
    }

    // Verify reCAPTCHA response
    $secretKey = "6LdUMPUqAAAAAFZIGMIsx26RiIwZmCPKsBMBIPew";
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verifyURL . "?secret=" . $secretKey . "&response=" . $recaptchaResponse);
    $responseData = json_decode($response);

    if (!$responseData->success) {
        $_SESSION["error"] = "reCAPTCHA verification failed!";
        header("Location: login.php");
        exit();
    }

    // Proceed with login authentication
    $stmt = $con->prepare("SELECT id, password, fname, lname, role FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $fname, $lname, $role);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["user_id"] = $id;
            $_SESSION["email"] = $email;
            $_SESSION["fname"] = $fname;
            $_SESSION["lname"] = $lname;
            $_SESSION["role"] = $role;

            if ($role == 1) { 
                header("Location: ./admin/dashboard/index.php");
            } elseif ($role == 2) { 
                header("Location: ./staff/index.php");
            } else {
                header("Location: ./customer/index.php");
            }
            exit();
        } else {
            $_SESSION["error"] = "Invalid password!";
        }
    } else {
        $_SESSION["error"] = "User not found!";
    }

    $stmt->close();
    $con->close();
    header("Location: login.php");
    exit();
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
            <form method="post" action="login.php">
            <div class=" p-3" style="z-index: 11">
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
                                    <button type="button" class="btn me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter your email">
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter your password">
              </div>
              <div class="mb-3 d-flex justify-content-center ialign-items-center">
                  <div class="g-recaptcha" data-sitekey="6LdUMPUqAAAAALZHOSRgzVY2T5e-usP6Iv3kjoss"></div>
              </div>
              <button type="submit" class="btn btn-primary w-100">Login</button>
              <p class="text-center mt-3">Forgot password? <a href="./forgot-password.php">Click here!</a></p>
              <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign up</a></p>
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
