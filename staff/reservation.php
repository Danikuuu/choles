<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 1) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}


require_once '../data-handling/db/connection.php';

$sql = "SELECT 
            r.id AS reservation_id,
            cpm.id AS customer_package_id,
            CONCAT(c.fname, ' ', c.lname) AS customer_name,
            r.event_date,
            r.status,
            r.downpayment_price,
            r.down_payment,
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
        ORDER BY r.event_date DESC;";

$result = $con->query($sql);

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

    <!-- <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css"> -->
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

            <li class="nav-item">
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

            <li class="nav-item active">
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


                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                             <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                             data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $_SESSION["fname"]," ", $_SESSION["lname"]; ?></span>
                                <img class="img-profile rounded-circle"
                                    src="../admin//dashboard/img/undraw_profile.svg">
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
                        <div class="d-flex justify-content-center align-items-center">
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
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status = htmlspecialchars($row['status']);

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['package_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['venue']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['people_count']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['event_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['package_price']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['downpayment_price']) . "</td>";
                            echo "<td>" . $status . "</td>";
                            echo "<td>
                                    <button class='btn btn-success btn-sm view-btn'
                                        data-customer='" . htmlspecialchars($row['customer_name']) . "' 
                                        data-package='" . htmlspecialchars($row['package_name']) . "' 
                                        data-venue='" . htmlspecialchars($row['venue']) . "' 
                                        data-event-date='" . htmlspecialchars($row['event_date']) . "' 
                                        data-price='" . htmlspecialchars($row['package_price']) . "' 
                                        data-downpayment='" . htmlspecialchars($row['downpayment_price']) . "' 
                                        data-status='" . $status . "'
                                        data-image='" . htmlspecialchars($row['down_payment']) . "'>
                                        View
                                    </button>";

                            // Display "Approve" button only if status is NOT "cancelled" or "completed"
                            if ($status !== 'cancelled' && $status !== 'completed' && $status !== 'approved' && $status !== 'refunded') {
                                echo "<form method='POST' action='./update_status.php' style='display:inline;'>
                                        <input type='hidden' name='reservationId' value='" . htmlspecialchars($row['reservation_id']) . "'>
                                        <input type='hidden' name='status' value='approved'>
                                        <button type='submit' class='btn btn-success btn-sm'>Approve</button>
                                    </form>";
                            }

                            // Display "Complete" button only if status is NOT "cancelled" or "completed"
                            if ($status !== 'cancelled' && $status !== 'completed' && $status !== 'refunded') {
                                echo "<form method='POST' action='./update_status.php' style='display:inline;'>
                                        <input type='hidden' name='reservationId' value='" . htmlspecialchars($row['reservation_id']) . "'>
                                        <input type='hidden' name='status' value='completed'>
                                        <button type='submit' class='btn btn-success btn-sm'>Complete</button>
                                    </form>";
                            }

                            // Display "Cancel" button only if status is NOT "approved" or "completed"
                            if ($status !== 'approved' && $status !== 'completed' && $status !== 'cancelled' && $status !== 'refunded') {
                                echo "<form method='POST' action='./update_status.php' style='display:inline;'>
                                        <input type='hidden' name='reservationId' value='" . htmlspecialchars($row['reservation_id']) . "'>
                                        <input type='hidden' name='status' value='cancelled'>
                                        <button type='submit' class='btn btn-info btn-sm'>Cancel</button>
                                    </form>";
                            }

                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
                    }
                ?>
                </tbody>

            </table>
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
        // Get data attributes from the button
        let customer = $(this).data("customer");
        let package = $(this).data("package");
        let venue = $(this).data("venue");
        let eventDate = $(this).data("event-date");
        let price = $(this).data("price");
        let downpayment = $(this).data("downpayment");
        let status = $(this).data("status");
        let image = $(this).data("image");

        // Set modal content
        $("#modalCustomer").text(customer);
        $("#modalPackage").text(package);
        $("#modalVenue").text(venue);
        $("#modalEventDate").text(eventDate);
        $("#modalPrice").text(price);
        $("#modalDownpayment").text(downpayment);
        $("#modalStatus").text(status);

        // Display image if available
        if (image) {
            $("#modalImage").html(`<img src="../customer/${image}" class="img-fluid" style="max-width: 300px; alt="Downpayment Proof">`);
        } else {
            $("#modalImage").html("No image available");
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
  $(document).ready(function () {
      $('.dropdown-toggle').dropdown();
  });
</script>

</body>

</html>