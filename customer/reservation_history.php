<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}

require_once '../data-handling/db/connection.php';

$user_id = $_SESSION["user_id"];

$limit = 5; // Records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Ensure page is at least 1
$offset = ($page - 1) * $limit;

// Count total records for pagination
$count_query = "SELECT COUNT(*) AS total 
                FROM customer_package_menu cpm
                JOIN reservations r ON cpm.id = r.customer_package_id 
                JOIN package p ON cpm.package_id = p.id
                JOIN menu m ON cpm.menu_id = m.id
                JOIN user c ON cpm.customer_id = c.id
                WHERE c.id = ?";

$count_stmt = $con->prepare($count_query);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

$sql = "SELECT 
            r.id AS reservation_id,
            cpm.id AS customer_package_id,
            CONCAT(c.fname, ' ', c.lname) AS customer_name,
            r.event_date,
            r.status,
            r.down_payment, 
            r.downpayment_price,
            r.refund_img,
            r.venue,
            p.package_name,
            p.package_price,
            p.people_count,
            m.name AS menu_name,
            m.description AS menu_description,
            m.category AS menu_category,
            m.image AS menu_image
        FROM customer_package_menu cpm
        JOIN reservations r ON cpm.id = r.customer_package_id 
        JOIN package p ON cpm.package_id = p.id
        JOIN menu m ON cpm.menu_id = m.id
        JOIN user c ON cpm.customer_id = c.id
        WHERE c.id = ? 
        ORDER BY r.event_date DESC";


$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id); 
$stmt->execute();
$result = $stmt->get_result();
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
            <li class="nav-item">
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

            <li class="nav-item active">
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

            <!-- Divider -->
            <hr class="sidebar-divider">

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
                        <h1 class="h3 mb-0 text-gray-800">Reservation History</h1>
                    </div>

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


                <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Package</th>
                        <th>Venue</th>
                        <th>Number of People</th>
                        <th>Date</th>
                        <th>Price</th>
                        <th>Downpayment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['package_name']) ?></td>
                    <td><?= htmlspecialchars($row['venue']) ?></td>
                    <td><?= htmlspecialchars($row['people_count']) ?></td>
                    <td><?= htmlspecialchars($row['event_date']) ?></td>
                    <td><?= htmlspecialchars($row['package_price']) ?></td>
                    <td><?= htmlspecialchars($row['downpayment_price']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <button class="btn btn-success btn-sm view-btn"
                            data-customer="<?= htmlspecialchars($row['customer_name']) ?>"
                            data-id="<?= htmlspecialchars($row['reservation_id']) ?>"
                            data-package="<?= htmlspecialchars($row['package_name']) ?>"
                            data-venue="<?= htmlspecialchars($row['venue']) ?>"
                            data-event-date="<?= htmlspecialchars($row['event_date']) ?>"
                            data-price="<?= htmlspecialchars($row['package_price']) ?>"
                            data-downpayment_price="<?= htmlspecialchars($row['downpayment_price']) ?>"
                            data-status="<?= htmlspecialchars($row['status']) ?>"
                            data-downpayment="<?= htmlspecialchars($row['down_payment']) ?>"
                            data-refund-image="<?= htmlspecialchars($row['refund_img']) ?>">
                            View
                        </button>
                        
                        <?php if ($row['status'] == 'pending'): ?>
                            <form method="POST" action="update_status.php" style="display:inline;">
                                <input type="hidden" name="reservationId" value="<?= htmlspecialchars($row['reservation_id']) ?>">
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-danger ml-1 btn-sm" disabled>Cancel</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">No records found</td></tr>
        <?php endif; ?>
                </tbody>

            </table>
            <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a></li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
            <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewModalLabel">Booking Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td id="modalCustomer"></td>
                                </tr>
                                <tr>
                                    <th>Package Name</th>
                                    <td id="modalPackage"></td>
                                </tr>
                                <tr>
                                    <th>Venue</th>
                                    <td id="modalVenue"></td>
                                </tr>
                                <tr>
                                    <th>Event Date</th>
                                    <td id="modalEventDate"></td>
                                </tr>
                                <tr>
                                    <th>Package Price</th>
                                    <td id="modalPrice"></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td id="modalStatus"></td>
                                </tr>
                                <tr>
                                    <th>Downpayment</th>
                                    <td id="modalDownpayment"></td>
                                </tr>
                                <tr id="downpaymentRow">
                                    <th>Downpayment Image</th>
                                    <td id="downpaymentContent"></td>
                                </tr>
                                <tr id="RefundRow">
                                    <th>Refund Image</th>
                                    <td id="modalRefund"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
        $(document).ready(function () {
            $(".view-btn").click(function () {
                // Get data attributes from the button
                let customer = $(this).data("customer");
                let reservationId = $(this).data("id");
                let package = $(this).data("package");
                let venue = $(this).data("venue");
                let eventDate = $(this).data("event-date");
                let price = $(this).data("price");
                let downpayment_price = $(this).data("downpayment_price");
                let status = $(this).data("status");
                let downpayment = $(this).data("downpayment");
                let refund = $(this).data("refund-image");

                // Set modal content
                $("#modalCustomer").text(customer);
                $("#modalId").val(reservationId);
                $("#modalPackage").text(package);
                $("#modalVenue").text(venue);
                $("#modalEventDate").text(eventDate);
                $("#modalPrice").text(price);
                $("#modalStatus").text(status);
                $("#modalDownpayment").text(downpayment_price);
                $("#modalRefund").text(refund);

                console.log(refund);

                // Handle Downpayment Section
                let downpaymentRow = $("#downpaymentRow");
                let downpaymentRefund = $("#RefundRow");
                let Refund = $("#modalRefund");
                let downpaymentContent = $("#downpaymentContent");
                console.log("Downpayment Image Path:", downpayment);
                console.log("Refund Image Path:", refund);


                if (status === "pending") {
                    downpaymentRefund.hide();
                    downpaymentRow.show();
                    downpaymentContent.html(`
                    <img src="${downpayment}" alt="Downpayment Receipt" class="img-fluid" style="max-width: 300px;">
                        <form action="./upload_downpayment.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="${reservationId}">
                            <input type="file" name="downpayment" id="downpayment">
                            <input type="submit" value="Submit" class="btn btn-sm btn-success">
                        </form> <br>
                    `);
                } else if (status === "approved" || status === "completed") {
                    downpaymentRefund.hide();
                    downpaymentRow.show();
                    downpaymentContent.html(`
                        <img src="${downpayment}" alt="Downpayment Receipt" class="img-fluid" style="max-width: 300px;">
                    `);
                } else if (status === "cancelled") {
                    downpaymentRow.show();
                    downpaymentContent.html(`
                        <img src="${downpayment}" alt="Downpayment Receipt" class="img-fluid" style="max-width: 300px;">
                    `); 
                    downpaymentRow.show();
                    Refund.html(`
                    <img src="${refund}" alt="Refund account" class="img-fluid" style="max-width: 300px;">
                        <form action="./upload_refund.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="${reservationId}">
                            <input type="file" name="refund" id="refund">
                            <input type="submit" value="Submit" class="btn btn-sm btn-success">
                        </form> <br>
                    `);
                } else if (status === "refunded") {
                    downpaymentRow.show();
                    downpaymentContent.html(`
                        <img src="${downpayment}" alt="Downpayment Receipt" class="img-fluid" style="max-width: 300px;">
                    `); 
                    downpaymentRow.show();
                    Refund.html(`
                    <img src="${refund}" alt="Refund account" class="img-fluid" style="max-width: 300px;">
                    `);
                }

                // Show modal
                $("#viewModal").modal("show");
            });
        });
    </script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        var toastEl = document.getElementById('toastMessage');
        if (toastEl && toastEl.textContent.trim() !== "") {
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
    });
    </script>
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