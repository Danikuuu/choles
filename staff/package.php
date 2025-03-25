<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 0) {
    header("Location: ../index.php");
    exit();
}

require_once '../data-handling/db/connection.php';

$sql = "SELECT * FROM package";
$result = $con->query($sql);

$menuQuery = "SELECT * FROM menu WHERE status = 0";
$menuResult = mysqli_query($con, $menuQuery);

$eventDate = "SELECT event_date FROM reservations";
$dateResult = $con->query($eventDate);

$reservedDates = [];

while ($row = $dateResult->fetch_assoc()) {
    $reservedDates[] = $row['event_date'];
}

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

            <li class="nav-item active">
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
                <h1 class="h3 mb-0 text-gray-800">Packages</h1>
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">

                <div class="row justify-content-center align-items-center p-5">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-xl-3 col-md-6 mb-4" style="cursor:pointer;">
                                <div class="card shadow h-100 py-2 package-card" 
                                    data-package-id="<?php echo $row['id']; ?>" 
                                    data-package-name="<?php echo htmlspecialchars($row['package_name']); ?>" 
                                    data-menu-count="<?php echo $row['menu_count']; ?>">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    <?php echo htmlspecialchars($row['package_name']); ?>
                                                </div>
                                                <span class="badge badge-success package-price">₱ <?php echo htmlspecialchars($row['package_price']); ?></span>
                                                <span class="badge badge-success package-venue"><?php echo htmlspecialchars($row['venue']); ?></span>
                                                <span class="badge badge-success package-downpayment">₱ <?php echo htmlspecialchars($row['downpayment']); ?></span>
                                                <span class="badge badge-success package-people"><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['people_count']); ?></span>
                                                <span class="badge <?php echo ($row['venue_styling'] == 1) ? 'badge-success' : 'badge-danger'; ?> package-styling">
                                                    <i class="fas fa-paint-brush"></i> <?php echo ($row['venue_styling'] == 1) ? 'Included' : 'Not Included'; ?>
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <img src="../admin/dashboard/<?php echo htmlspecialchars($row['image']); ?>" alt="Package Image" style="width: 150px; height: 100px; object-fit: cover;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p>No package found.</p>
                        </div>
                    <?php endif; ?>
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
                                <p class="text-center"><strong>Downpayment:</strong> <span id="modal_downpayment"></span></p>


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
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".package-card").forEach(card => {
                card.addEventListener("click", function () {
                    let packageId = this.getAttribute("data-package-id");
                    let packageName = this.getAttribute("data-package-name");
                    let menuCount = parseInt(this.getAttribute("data-menu-count")) || 0;

                    // Correctly select elements
                    let packagePrice = this.querySelector(".package-price")?.textContent.trim() || "N/A";
                    let venue = this.querySelector(".package-venue")?.textContent.trim() || "N/A";
                    let peopleCount = this.querySelector(".package-people")?.textContent.trim() || "N/A";
                    let venueStyling = this.querySelector(".package-styling")?.textContent.trim() || "N/A";
                    let downpayment = this.querySelector(".package-downpayment")?.textContent.trim() || "N/A";
                    let packageImage = this.querySelector("img")?.src || "";

                    // Update modal fields

                    document.getElementById("modal_package_name").innerText = packageName;
                    document.getElementById("modal_package_price").innerText = packagePrice;
                    document.getElementById("modal_venue").innerText = venue;
                    document.getElementById("modal_people_count").innerText = peopleCount;
                    document.getElementById("modal_venue_styling").innerText = venueStyling;
                    document.getElementById("modal_downpayment").innerText = downpayment;
                    document.getElementById("modal_package_image").src = packageImage;


                    // Show modal
                    $("#reservationModal").modal("show");
                });
            });

        });
    </script>


</body>

</html>