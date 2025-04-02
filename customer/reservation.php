<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}

require_once '../data-handling/db/connection.php';

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CHOLES - Menu</title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="../admin//dashboard/vendor/fontawesome-free/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="../admin/dashboard/css//sb-admin-2.min.css">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color:  #059652;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-text mx-3">CHOLES <sup>Catering</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Menu</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Reservations
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item active">
                <a class="nav-link" href="./reservation.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Reservations</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./messages.php">
                    <i class="fas fa-envelope"></i> Messages
                    <span id="unreadBadge" class="badge badge-danger" style="display: none;"></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./reservation_history.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Reservation History</span></a>
            </li>

            <!-- Heading -->
            <div class="sidebar-heading">
                Feedback
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="./feedback.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Feedback</span></a>
            </li>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="couponnDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ticket-alt fa-fw"></i>
                                <!-- Counter - Notifications -->
                                <span class="badge badge-danger badge-counter" id="couponCount">0</span>
                            </a>
                            <!-- Dropdown - Notifications -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="notificationDropdown">
                                <h6 class="dropdown-header">
                                    Coupons
                                </h6>
                                <div id="couponList">
                                    <p class="text-center p-3 text-gray-600">No new coupon</p>
                                </div>
                            </div>
                        </li>
                    <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Notifications -->
                                <span class="badge badge-danger badge-counter" id="notificationCount">0</span>
                            </a>
                            <!-- Dropdown - Notifications -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="notificationDropdown">
                                <h6 class="dropdown-header">
                                    Notifications
                                </h6>
                                <div id="notificationList">
                                    <p class="text-center p-3 text-gray-600">No new notifications</p>
                                </div>
                            </div>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION["fname"]," ", $_SESSION["lname"]; ?></span>
                                    <img class="img-profile rounded-circle"
                                    src="../admin/dashboard/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="./profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../destroy.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
                <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Reservation</h1>
                    </div>

                <div class="row justify-content-center align-items-center p-5">

                
                    <a href="./reservation_history.php" class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow h-100 py-2" style="cursor: pointer;">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    My Reservations
                                                </div>
                                                <span class="badge badge-success">
                                                    view my reservations
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-fw text-secondary fa-folder fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                    </a>

                            
                            <a href="./package.php" class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow h-100 py-2" style="cursor: pointer;">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Create Reservation
                                                </div>
                                                <span class="badge badge-success">
                                                    create new reservation
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-fw text-secondary fa-folder fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>

                            
                            <a href="./index.php" class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow h-100 py-2" style="cursor: pointer;">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    View Menu
                                                </div>
                                                <span class="badge badge-success">
                                                    display all menu items
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-fw text-secondary fa-folder fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>

                </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>CHOLES Catering Services 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../destroy.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../admin//dashboard/vendor/jquery/jquery.min.js"></script>
    <script src="../admin//dashboard/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../admin//dashboard/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="j../admin//dashboard/s/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="../admin//dashboard/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="../admin//dashboard/js/demo/chart-area-demo.js"></script>
    <script src="../admin//dashboard/js/demo/chart-pie-demo.js"></script>
    <script src="../admin//dashboard/js/demo/chart-bar-demo.js"></script>
    <script src="../admin//dashboard/js/demo/datatables-demo.js"></script>  
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    function fetchNotifications() {
        fetch("fetch_notifications.php") // Replace with your backend endpoint
            .then(response => response.json())
            .then(data => {
                let count = data.length;
                let notificationCount = document.getElementById("notificationCount");
                let notificationList = document.getElementById("notificationList");

                if (count > 0) {
                    notificationCount.innerText = count;
                    notificationCount.style.display = "inline-block";

                    notificationList.innerHTML = "";
                    data.forEach(notification => {
                        let item = document.createElement("a");
                        item.href = "#"; // Update with actual link
                        item.classList.add("dropdown-item", "d-flex", "align-items-center");
                        item.innerHTML = `
                            <div class="mr-3">
                                <div class="icon-circle bg-primary">
                                    <i class="fas fa-info text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">${notification.date}</div>
                                <span class="font-weight-bold">${notification.message}</span>
                            </div>
                        `;
                        notificationList.appendChild(item);
                    });
                } else {
                    notificationCount.style.display = "none";
                    notificationList.innerHTML = '<p class="text-center p-3 text-gray-600">No new notifications</p>';
                }
            });
    }

    // Fetch notifications when page loads
    fetchNotifications();

    // Mark notifications as seen when dropdown is clicked
    document.getElementById("notificationDropdown").addEventListener("click", function () {
        fetch("mark_reservations_seen.php", { method: "POST" });
        document.getElementById("notificationCount").style.display = "none";
    });

    // Auto-refresh notifications every 30 seconds
    setInterval(fetchNotifications, 30000);
});
    </script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    function fetchCoupons() {
        fetch("fetch_coupon.php")
            .then(response => response.json())
            .then(data => {
                let count = data.length;
                let couponCount = document.getElementById("couponCount");
                let couponList = document.getElementById("couponList");

                if (count > 0) {
                    couponCount.innerText = count;
                    couponCount.style.display = "inline-block";
                    couponList.innerHTML = "";

                    data.forEach(coupon => {
                        let item = document.createElement("a");
                        item.href = "#"; 
                        item.classList.add("dropdown-item", "d-flex", "align-items-center");
                        item.innerHTML = `
                            <div class="mr-3">
                                <div class="icon-circle bg-success">
                                    <i class="fas fa-ticket-alt text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">Expires: ${coupon.expiry_date}</div>
                                <span class="font-weight-bold">${coupon.code} - ${coupon.discount_value} ${coupon.discount_type}</span>
                                <div class="text-muted">${coupon.status}</div>
                                <button data-id="${coupon.id}" class="btn btn-primary btn-sm claim-btn mt-2">Claim</button>
                            </div>
                        `;
                        couponList.appendChild(item);
                    });
                } else {
                    couponCount.style.display = "none";
                    couponList.innerHTML = '<p class="text-center p-3 text-gray-600">No available coupons</p>';
                }
            });
    }

    function claimCoupon(couponId) {
        console.log("Attempting to claim coupon with ID:", couponId);

        fetch("claim_coupon.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `coupon_id=${couponId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Coupon claimed successfully!");
                fetchCoupons();
            } else {
                alert("Failed to claim coupon: " + data.message);
            }
        });
    }

    document.getElementById("couponList").addEventListener("click", function (event) {
        if (event.target.classList.contains("claim-btn")) {
            let couponId = event.target.getAttribute("data-id");
            console.log("Claim button clicked. Coupon ID:", couponId);

            if (couponId) {
                claimCoupon(couponId);
            } else {
                console.error("Coupon ID is null or undefined!");
            }
        }
    });

    fetchCoupons();
    setInterval(fetchCoupons, 30000);
});

</script>
</body>

</html>