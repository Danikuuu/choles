<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1) {
    header("Location: ../index.php");
    exit();
}

require_once '../data-handling/db/connection.php';

$user_id = $_SESSION["user_id"];

$sql = "SELECT 
            r.id AS reservation_id,
            cpm.id AS customer_package_id,
            CONCAT(c.fname, ' ', c.lname) AS customer_name,
            r.event_date,
            r.status,
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
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">CHOLES <sup>Catering</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Menu</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Menu Management
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="./reservation.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Reservations</span></a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="./reservation_history.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Reservation History</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Reservations
            </div>

            <li class="nav-item">
                <a class="nav-link" href="./reservation.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Reservations</span></a>
            </li>

            <!-- Anomyties -->
            <li class="nav-item">
                <a class="nav-link" href="./inventory.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Equipments</span></a>
            </li>
            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="./feedback.php">
                    <i class="fas fa-fw fa-chart-area"></i>
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
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['package_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['venue']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['people_count']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['event_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['package_price']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>
                                    <button class='btn btn-success btn-sm view-btn'
                                        data-customer='" . htmlspecialchars($row['customer_name']) . "'
                                        data-id='" . htmlspecialchars($row['reservation_id']) . "'
                                        data-package='" . htmlspecialchars($row['package_name']) . "'
                                        data-venue='" . htmlspecialchars($row['venue']) . "'
                                        data-event-date='" . htmlspecialchars($row['event_date']) . "'
                                        data-price='" . htmlspecialchars($row['package_price']) . "'
                                        data-status='" . htmlspecialchars($row['status']) . "'
                                        data-downpayment='" . htmlspecialchars($row['down_payment']) . "'>
                                        View
                                    </button>";

                            if ($row['status'] == 'pending') {
                                echo "<form method='POST' action='update_status.php' style='display:inline;'>
                                        <input type='hidden' name='reservationId' value='" . htmlspecialchars($row['reservation_id']) . "'>
                                        <input type='hidden' name='status' value='cancelled'>
                                        <button type='submit' class='btn btn-danger btn-sm'>Cancel</button>
                                    </form>";
                            } else {
                                echo "<button class='btn btn-danger ml-1 btn-sm' disabled>Cancel</button>";
                            }

                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>No records found</td></tr>";
                    }
                ?>
                </tbody>

            </table>
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
                                <tr id="downpaymentRow">
                                    <th>Downpayment</th>
                                    <td id="downpaymentContent"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

                <!-- Bootstrap Modal -->
                <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="reservationModalLabel">Create Reservation</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Package Details -->
                                <div class="text-center">
                                    <img id="modal_package_image" src="" alt="Package Image" style="width: 100%; height: 200px; object-fit: cover;">
                                </div>
                                <h4 class="text-center mt-2" id="modal_package_name"></h4>
                                <p class="text-center"><strong>Price:</strong> <span id="modal_package_price"></span></p>
                                <p class="text-center"><strong>Venue:</strong> <span id="modal_venue"></span></p>
                                <p class="text-center"><strong>People Count:</strong> <span id="modal_people_count"></span></p>
                                <p class="text-center"><strong>Venue Styling:</strong> <span id="modal_venue_styling"></span></p>

                                <!-- Reservation Form -->
                                <form id="reservationForm" action="./create_reservation.php" method="post">
                                    <input type="hidden" id="package_id" name="package_id">
                                    <input type="hidden" id="customer_id" name="customer_id" value="10">

                                    <div class="form-group">
                                        <label for="event_date">Event Date</label>
                                        <input type="date" class="form-control" id="event_date" name="event_date" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="menu_selection">Select Menus (<span id="menu_limit"></span> items)</label> <br>
                                        <?php if ($menuResult->num_rows > 0): ?>
                                            <?php while ($row = $menuResult->fetch_assoc()): ?>
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input menu-checkbox" id="menu_<?php echo $row['id']; ?>" name="menu_id[]" value="<?php echo $row['id']; ?>">
                                                    <label class="form-check-label" for="menu_<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></label>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="col-12 text-center">
                                                <p>No menu found.</p>
                                            </div>
                                        <?php endif; ?>

                                    <button type="submit" class="btn btn-success">Confirm Reservation</button>
                                </form>
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
                let status = $(this).data("status");
                let downpayment = $(this).data("downpayment");

                // Set modal content
                $("#modalCustomer").text(customer);
                $("#modalId").val(reservationId);
                $("#modalPackage").text(package);
                $("#modalVenue").text(venue);
                $("#modalEventDate").text(eventDate);
                $("#modalPrice").text(price);
                $("#modalStatus").text(status);

                // Handle Downpayment Section
                let downpaymentRow = $("#downpaymentRow");
                let downpaymentContent = $("#downpaymentContent");
                console.log("Downpayment Image Path:", downpayment);


                if (status === "pending") {
                    downpaymentRow.show();
                    downpaymentContent.html(`
                        <form action="./upload_downpayment.php" method="post" enctype="multipart/form-data">
                            <input type="text" name="id" value="${reservationId}">
                            <input type="file" name="downpayment" id="downpayment">
                            <input type="submit" value="Submit" class="btn btn-sm btn-success">
                        </form>
                    `);
                } else if (status === "approved" || status === "completed") {
                    downpaymentRow.show();
                    downpaymentContent.html(`
                        <img src="${downpayment}" alt="Downpayment Receipt" class="img-fluid" style="max-width: 300px;">
                    `);
                } else if (status === "cancelled") {
                    downpaymentRow.hide();
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


</body>

</html>