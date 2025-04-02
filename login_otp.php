<?php
session_start();
include "./data-handling/db/connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_otp = $_POST["login_otp"] ?? '';

    if (!isset($_SESSION["login_otp"])) {
        echo "Invalid OTP. Try again."; // Response for JavaScript
        exit();
    }

    if ($user_otp == $_SESSION["login_otp"]) {
        $_SESSION["user_id"] = $_SESSION["user_id"] ?? null;
        $_SESSION["email"] = $_SESSION["email"] ?? null;
        $_SESSION["fname"] = $_SESSION["fname"] ?? null;
        $_SESSION["lname"] = $_SESSION["lname"] ?? null;
        $role = $_SESSION["role"] ?? null;

        // Return redirect URL instead of PHP header() function
        if ($role == 1) {
            echo "redirect: ./admin/dashboard/index.php";
        } elseif ($role == 2) {
            echo "redirect: ./staff/index.php";
        } else {
            echo "redirect: ./customer/index.php";
        }
        exit();
    } else {
        echo "Invalid OTP";
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>OTP - CHOLES</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/main.css" rel="stylesheet">
</head>
<style>
.height-100 {
    height: 100vh;
}

.card {
    width: 400px;
    border: none;
    height: 300px;
    box-shadow: 0px 5px 20px 0px #d2dae3;
    z-index: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

.card h6 {
    color: #059652;
    font-size: 20px;
}

.inputs input {
    width: 40px;
    height: 40px;
}

input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    margin: 0;
}

.card-2 {
    background-color: #fff;
    padding: 10px;
    width: 350px;
    height: 100px;
    bottom: -50px;
    left: 20px;
    position: absolute;
    border-radius: 5px;
}

.card-2 .content {
    margin-top: 50px;
}

.card-2 .content a {
    color: #059652;
}

.form-control:focus {
    box-shadow: none;
    border: 2px solid #059652;
}

.validate {
    border-radius: 20px;
    height: 40px;
    background-color: #059652;
    border: 1px solid #059652;
    width: 140px;
}
</style>
<body>
    <div class="container height-100 d-flex justify-content-center align-items-center">
        <div class="position-relative">
            <div class="card p-2 text-center">
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
                <h6>Please enter the one time password <br> to login to your account</h6>
                <div> <span>A code has been sent to</span> <small id="maskedNumber">your email</small> </div>
                <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2">
                    <input class="m-2 text-center form-control rounded" type="text" id="first" maxlength="1" />
                    <input class="m-2 text-center form-control rounded" type="text" id="second" maxlength="1" />
                    <input class="m-2 text-center form-control rounded" type="text" id="third" maxlength="1" />
                    <input class="m-2 text-center form-control rounded" type="text" id="fourth" maxlength="1" />
                    <input class="m-2 text-center form-control rounded" type="text" id="fifth" maxlength="1" />
                    <input class="m-2 text-center form-control rounded" type="text" id="sixth" maxlength="1" />
                </div>
                <div class="mt-4"> 
                    <button id="validateBtn" class="btn btn-success px-4 validate">Validate</button> 
                </div>
            </div>
        </div>
    </div>

  <footer id="footer" class="footer position-relative">
    <div class="container text-center">
      <p>© CHOLES All Rights Reserved</p>
    </div>
  </footer>

  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
<script>
document.addEventListener("DOMContentLoaded", function() {
    function OTPInput() {
        const inputs = document.querySelectorAll('#otp > input');
        for (let i = 0; i < inputs.length; i++) {
            inputs[i].addEventListener('input', function() {
                if (this.value.length > 1) {
                    this.value = this.value[0]; //    
                }
                if (this.value !== '' && i < inputs.length - 1) {
                    inputs[i + 1].focus(); //   
                }
            });

            inputs[i].addEventListener('keydown', function(event) {
                if (event.key === 'Backspace') {
                    this.value = '';
                    if (i > 0) {
                        inputs[i - 1].focus();   
                    }
                }
            });
        }
    }

    OTPInput();

    const validateBtn = document.getElementById('validateBtn');
    validateBtn.addEventListener('click', function() {
        let otp = '';
        document.querySelectorAll('#otp > input').forEach(input => otp += input.value);

        fetch(window.location.href, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `login_otp=${otp}`
        })
        .then(response => response.text())
        .then(data => {
            console.log("Server Response:", data); // Debugging

            if (data.includes("Invalid OTP")) {
                alert("Invalid OTP. Please try again.");
            } else if (data.includes("redirect:")) { 
                let redirectUrl = data.replace("redirect:", "").trim();
                window.location.href = redirectUrl;  // Redirect to correct role page
            } else {
                alert("Unexpected response: " + data);
            }
        })
        .catch(error => console.error("Error:", error));
    });

});

document.addEventListener("DOMContentLoaded", function () {
        var toastEl = document.getElementById('toastMessage');
        if (toastEl && toastEl.textContent.trim() !== "") {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
</script>
</html>

