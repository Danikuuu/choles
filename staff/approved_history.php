<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 1) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}


require_once '../data-handling/db/connection.php';

$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch paginated data
$sql = "SELECT 
            r.id AS reservation_id,
            cpm.id AS customer_package_id,
            CONCAT(c.fname, ' ', c.lname) AS customer_name,
            c.email,
            r.event_date,
            r.status,
            r.event_type,
            r.event_theme,
            r.start_time,
            r.end_time,
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
        WHERE r.status = 'approved'
        ORDER BY r.event_date ASC
        LIMIT $limit OFFSET $offset";

$result = $con->query($sql);

// Get total number of records for pagination
$total_sql = "SELECT COUNT(*) AS total FROM reservations";
$total_result = $con->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $limit);
?>

<!-- todo once the view button is clicked the details will display in a modal -->
 <!-- it will display the details inluding the image payment to compare to his her gcash -->
  <!-- add pagination -->
   <!-- add search bar -->

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CHOLES Admin - Menu</title>

    <link rel="stylesheet" href="../admin/dashboard/vendor/fontawesome-free/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- <link href="css/sb-admin-2.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="../admin/dashboard/css/sb-admin-2.css">

</head>

<body id="page-top">

    <div id="wrapper">

    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color:  #059652;">

<a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
    <div class="sidebar-brand-text mx-3">CHOLES <sup>Staff</sup></div>
</a>

<hr class="sidebar-divider my-0">

<hr class="sidebar-divider">

<div class="sidebar-heading">
    Menu Management
</div>

<li class="nav-item active">
    <a class="nav-link" href="./index.php">
        <i class="fas fa-fw fa-utensils"></i>
        <span>Menu</span></a>
</li>

<li class="nav-item">
    <a class="nav-link" href="./package.php">
        <i class="fas fa-fw fa-utensils"></i>
        <span>Packages</span></a>
</li>


<hr class="sidebar-divider">

<div class="sidebar-heading">
    Reservations
</div>

<li class="nav-item">
    <a class="nav-link" href="./reservation.php">
        <i class="fas fa-fw fa-folder"></i>
        <span>Reservations</span></a>
</li>

<hr class="sidebar-divider">

<div class="sidebar-heading">
    Feedback
</div>

<li class="nav-item ">
    <a class="nav-link" href="./feedback.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Feedback</span></a>
</li>


</ul>

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">

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

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">CHOLES Admin</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="./profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../../destroy.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4 position-relative">
                        <h1 class="h3 mb-0 text-gray-800">Reservations</h1>
                        <div class="">
                            <a class="mr-3 text-white btn btn-sm btn-warning" href="./pending_history.php">Pending</a>
                            <a class="mr-3 text-white btn btn-sm btn-primary" href="./completed_history.php">Completed</a>
                            <a class="mr-3 text-white btn btn-sm btn-success" href="./approved_history.php">Approved</a>
                            <a class="mr-3 text-white btn btn-sm btn-danger" href="./cancelled_history.php">Cancelled</a>
                            <a class="mr-3 text-white btn btn-sm btn-info" href="./refund_history.php">Refund</a>
                        </div>

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
                            <div>
                        </div>
                    </div>

                </div>

 
                <div class="row justify-content-center align-items-center px-5">
                <div class="card shadow w-100">
                <div class="card shadow w-100">
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
                <?php if ($result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['customer_name']); ?></td>
                                <td><?= htmlspecialchars($row['package_name']); ?></td>
                                <td><?= htmlspecialchars($row['venue']); ?></td>
                                <td><?= htmlspecialchars($row['people_count']); ?></td>
                                <td><?= htmlspecialchars($row['event_date']); ?></td>
                                <td><?= htmlspecialchars($row['package_price']); ?></td>
                                <td><?= htmlspecialchars($row['downpayment_price']); ?></td>
                                <td>
                                    <span class="badge <?= $row['status'] == 'Expired' ? 'bg-danger' : 'bg-success'; ?>">
                                        <?= htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class='btn btn-success btn-sm view-btn' 
                                        data-id='<?= htmlspecialchars($row['reservation_id']); ?>'
                                        data-customer='<?= htmlspecialchars($row['customer_name']); ?>'
                                        data-package='<?= htmlspecialchars($row['package_name']); ?>'
                                        data-venue='<?= htmlspecialchars($row['venue']); ?>'
                                        data-event-date='<?= htmlspecialchars($row['event_date']); ?>'
                                        data-event-type='<?= htmlspecialchars($row['event_type']); ?>'
                                        data-event-theme='<?= htmlspecialchars($row['event_theme']); ?>'
                                        data-start-time='<?= htmlspecialchars($row['start_time']); ?>'
                                        data-end-time='<?= htmlspecialchars($row['end_time']); ?>'
                                        data-price='<?= htmlspecialchars($row['package_price']); ?>'
                                        data-downpayment='<?= htmlspecialchars($row['downpayment_price']); ?>'
                                        data-status='<?= htmlspecialchars($row['status']); ?>'
                                        data-image='<?= htmlspecialchars($row['down_payment']); ?>'
                                        data-refund-image='<?= htmlspecialchars($row['refund_img']); ?>'
                                        data-refund-proof='<?= htmlspecialchars($row['refund_proof']); ?>'>
                                        View
                                    </button>

                                    <?php if ($row['status'] !== 'cancelled' && $row['status'] !== 'completed' && $row['status'] !== 'approved' && $row['status'] !== 'refunded') : ?>
                                        <form method='POST' action='./update_status.php' style='display:inline;'>
                                            <input type='hidden' name='reservationId' value='<?= htmlspecialchars($row['reservation_id']); ?>'>
                                            <input type='hidden' name='status' value='approved'>
                                            <button type='submit' class='btn btn-success btn-sm'>Approve</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($row['status'] !== 'cancelled' && $row['status'] !== 'completed' && $row['status'] !== 'refunded') : ?>
                                        <form method='POST' action='./update_status.php' style='display:inline;'>
                                            <input type='hidden' name='reservationId' value='<?= htmlspecialchars($row['reservation_id']); ?>'>
                                            <input type='hidden' name='status' value='completed'>
                                            <button type='submit' class='btn btn-success btn-sm'>Complete</button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($row['status'] !== 'approved' && $row['status'] !== 'completed' && $row['status'] !== 'cancelled' && $row['status'] !== 'refunded') : ?>
                                        <form method='POST' action='./update_status.php' style='display:inline;'>
                                            <input type='hidden' name='reservationId' value='<?= htmlspecialchars($row['reservation_id']); ?>'>
                                            <input type='hidden' name='status' value='cancelled'>
                                            <button type='submit' class='btn btn-info btn-sm'>Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr><td colspan='9' class='text-center'>No records found</td></tr>
                    <?php endif; ?>
                </tbody>

            </table>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1) : ?>
                        <li class="page-item"><a class="page-link" href="?page=<?= ($page - 1); ?>">Previous</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                        <li class="page-item <?= ($i == $page ? "active" : ""); ?>">
                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages) : ?>
                        <li class="page-item"><a class="page-link" href="?page=<?= ($page + 1); ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
            <!-- View Details Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Reservation Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Customer Name</th>
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
                        <th>Event Type</th>
                        <td id="modalEventType"></td>
                    </tr>
                    <tr>
                        <th>Event Theme</th>
                        <td id="modalEventTheme"></td>
                    </tr>
                    <tr>
                        <th>Start Time</th>
                        <td id="modalEventStart"></td>
                    </tr>
                    <tr>
                        <th>End Time</th>
                        <td id="modalEventEnd"></td>
                    </tr>
                    <tr>
                        <th>Package Price</th>
                        <td id="modalPrice"></td>
                    </tr>
                    <tr>
                        <th>Downpayment</th>
                        <td id="modalDownpayment"></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td id="modalStatus"></td>
                    </tr>
                    <tr>
                        <th>Downpayment Proof</th>
                        <td id="modalImage"></td>
                    </tr>
                    <tr id="RefundRow">
                        <th>Refund Image</th>
                        <td id="modalRefund"></td>
                    </tr>
                    <tr id="RefundProofRow">
                        <th>Refund Proof Image</th>
                        <td id="modalRefundProof"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
        </div>
                </div>

            </div>
            

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>CHOLES Catering Services 2025</span>
                    </div>
                </div>
            </footer>

        </div>


    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

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
                    <a class="btn btn-primary" href="../../destroy.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <script src="js/sb-admin-2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $(".view-btn").click(function () {
        let reservationId = $(this).data("id");
        let customer = $(this).data("customer");
        let package = $(this).data("package");
        let venue = $(this).data("venue");
        let eventDate = $(this).data("event-date");
        let price = $(this).data("price");
        let downpayment = $(this).data("downpayment");
        let status = $(this).data("status");
        let image = $(this).data("image");
        let refund = $(this).data("refund-image");
        let refundproof = $(this).data("refund-proof");
        let eventType = $(this).data("event-type");
        let eventTheme = $(this).data("event-theme");
        let eventStart = $(this).data("start-time");
        let eventEnd = $(this).data("end-time");

        // Set modal content
        $("#modalCustomer").text(customer);
        $("#modalPackage").text(package);
        $("#modalVenue").text(venue);
        $("#modalEventDate").text(eventDate);
        $("#modalPrice").text(price);
        $("#modalDownpayment").text(downpayment);
        $("#modalStatus").text(status);
        $("#modalEventType").text(eventType);
        $("#modalEventTheme").text(eventTheme);
        $("#modalEventStart").text(eventStart);
        $("#modalEventEnd").text(eventEnd);

        // Display downpayment proof
        if (image) {
            $("#modalImage").html(`<img src="../../customer/${image}" class="img-fluid" style="max-width: 300px;" alt="Downpayment Proof">`);
        } else {
            $("#modalImage").html("No image available");
        }

        // Display refund image
        if (refund) {
            $("#RefundRow").show();
            $("#modalRefund").html(`<img src="../../customer/${refund}" class="img-fluid" style="max-width: 300px;" alt="Refund Image">`);
        } else {
            $("#RefundRow").hide();
        }

        // Show refund proof form only if status is "cancelled"
        if (status === "cancelled") {
            $("#RefundProofRow").show();
            $("#modalRefundProof").html(`
                <img src="${refundproof}" class="img-fluid" style="max-width: 300px;" alt="Refund Proof">
                <form action="./upload_refund_proof.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="${reservationId}">
                    <input type="file" name="refund_proof" id="refund_proof">
                    <input type="submit" value="Submit" class="btn btn-sm btn-success">
                </form> <br>
            `);
        } else if(status === "refunded") {
            $("#RefundProofRow").show();
            $("#modalRefundProof").html(`
                <img src="${refundproof}" class="img-fluid" style="max-width: 300px;" alt="Refund Proof">
            `);
        } else {
            $("#RefundProofRow").hide();
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
</body>

</html>