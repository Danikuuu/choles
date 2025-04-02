<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
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

$customerId = $_SESSION['user_id'];

$couponQuery = "
    SELECT c.code, c.discount_type, c.discount_value 
    FROM coupons c
    LEFT JOIN claimed_coupon cc ON c.id = cc.coupon_id 
    WHERE c.status = 'active' 
    AND c.expiry_date >= CURDATE() 
    AND cc.user_id = ? 
    ORDER BY c.expiry_date ASC
    LIMIT 1";

$stmt = $con->prepare($couponQuery);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$couponResult = $stmt->get_result();

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
            <li class="nav-item">
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
                        <h1 class="h3 mb-0 text-gray-800">Packages</h1>
                    </div>

               <div class="d-flex justify-content-center align-items-center">
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
                                    <button type="button" class="btn me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
               </div>

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
                                <p class="text-center"><strong>Pax:</strong> <span id="modal_people_count"></span></p>
                                <p class="text-center"><strong>Venue Styling:</strong> <span id="modal_venue_styling"></span></p>
                                <p class="text-center"><strong>Downpayment:</strong> <span id="modal_downpayment"></span></p>

                                <!-- Reservation Form -->
                                <form id="reservationForm" action="./create_reservation.php" method="post">
                                    <input type="hidden" id="package_id" name="package_id">

                                    <div class="form-group">
                                        <label for="event_date">Event Date</label>
                                        <input type="date" class="form-control" id="event_date" name="event_date" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="event_date">Event Type</label>
                                        <select name="event_type" id="event_type" class="form-control" required>
                                            <option value="">Select Occassion</option>
                                            <option value="birthday">Birthday</option>
                                            <option value="wedding">Wedding</option>
                                            <option value="anniversary">Anniversary</option>
                                            <option value="graduation">Graduation</option>
                                            <option value="corporate_events">Corporate Events</option>
                                            <option value="holloween_party">Holloween Party</option>
                                            <option value="christmas_party">Christman Party</option>
                                            <option value="promotion_party">Promotion Party</option>
                                            <option value="valentine_event">Valentine Event</option>
                                            <option value="reunion">Reunion</option>
                                            <option value="baby_shower">Baby Shower</option>
                                            <option value="retirement_party">Retirement Party</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="eventthems">Event Theme</label>
                                        <input type="text" name="event_theme" id="event_theme" placeholder="Ex. Color blue or princess theme" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="venue">Event Venue</label>
                                        <input type="text" name="event_venue" id="event_venue" placeholder="Enter venue for the event" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="start_time">Start Time</label>
                                        <select class="form-control" name="start_time" id="start_time">
                                            <?php
                                            $startTime = strtotime("07:00"); // 7:00 PM
                                            $endTime = strtotime("20:00");   // 8:00 PM
                                            $interval = 30 * 60;             // 30 minutes interval (in seconds)

                                            // Loop through the times and create options
                                            for ($currentTime = $startTime; $currentTime <= $endTime; $currentTime += $interval) {
                                                $timeFormatted = date("h:i A", $currentTime); // Format the time as 7:00 PM, 7:30 PM, etc.
                                                echo "<option value='" . $timeFormatted . "'>" . $timeFormatted . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="end_time">End Time</label>
                                        <select class="form-control" name="end_time" id="end_time">
                                            <?php
                                            $startTime = strtotime("10:00");
                                            $endTime = strtotime("23:00");
                                            $interval = 30 * 60;             // 30 minutes interval (in seconds)

                                            // Loop through the times and create options
                                            for ($currentTime = $startTime; $currentTime <= $endTime; $currentTime += $interval) {
                                                $timeFormatted = date("h:i A", $currentTime); // Format the time as 7:00 PM, 7:30 PM, etc.
                                                echo "<option value='" . $timeFormatted . "'>" . $timeFormatted . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="coupon">Coupon</label>
                                        <?php
                                            if ($couponResult && $couponResult->num_rows > 0) {
                                                while ($row = $couponResult->fetch_assoc()) {
                                                    $discountType = htmlspecialchars($row['discount_type']);
                                                    $discountValue = htmlspecialchars($row['discount_value']);

                                                    // Format the discount display
                                                    if ($discountType === 'fixed') {
                                                        $formattedDiscount = '₱' . $discountValue;
                                                    } elseif ($discountType === 'percentage') {
                                                        $formattedDiscount = $discountValue . '% off';
                                                    } else {
                                                        $formattedDiscount = $discountValue; // Fallback in case of an unexpected type
                                                    }
                                                    
                                                    echo '<input type="text" class="form-control" 
                                                            value="' . htmlspecialchars($row['code']) . '" readonly
                                                            data-value="' . $discountValue . '" 
                                                            data-type="' . $discountType . '" 
                                                            name="coupon" id="" 
                                                            placeholder="' . htmlspecialchars($row['code']) . ' - ' . $formattedDiscount . '">';
                                                }
                                            } else {
                                                echo '<input type="text" readonly class="form-control" value="" placeholder="No available coupons">';
                                            }
                                            ?>
                                        <p><strong>Note: Coupon is one-time use only</strong></p>
                                    </div>

                                    <div class="form-group">
                                        <label for="menu_selection">Select Menus (<span id="menu_limit"></span> items)</label> <br>
                                        <?php
                                            // Assuming your menuResult has a 'category' field
                                            $itemsByCategory = [];

                                            if ($menuResult->num_rows > 0) {
                                                // Group menu items by category
                                                while ($row = $menuResult->fetch_assoc()) {
                                                    $category = $row['category']; // Replace with your actual category column name
                                                    if (!isset($itemsByCategory[$category])) {
                                                        $itemsByCategory[$category] = [];
                                                    }
                                                    $itemsByCategory[$category][] = $row;
                                                }

                                                // Display menu items by category
                                                foreach ($itemsByCategory as $category => $items) {
                                                    echo '<h5>' . htmlspecialchars($category) . '</h5>'; // Display category name

                                                    foreach ($items as $row) {
                                                        echo '<div class="form-check">
                                                                <input type="checkbox" class="form-check-input menu-checkbox" id="menu_' . $row['id'] . '" name="menu_id[]" value="' . $row['id'] . '">
                                                                <label class="form-check-label" for="menu_' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</label>
                                                            </div>';
                                                    }
                                                }
                                            } else {
                                                echo '<div class="col-12 text-center"><p>No menu found.</p></div>';
                                            }
                                            ?>
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
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".package-card").forEach(card => {
                card.addEventListener("click", function () {
                    let packageId = this.getAttribute("data-package-id");
                    let packageName = this.getAttribute("data-package-name");
                    let menuCount = parseInt(this.getAttribute("data-menu-count")) || 0;

                    // Correctly select elements
                    let packagePrice = this.querySelector(".package-price")?.textContent.trim() || "N/A";
                    let peopleCount = this.querySelector(".package-people")?.textContent.trim() || "N/A";
                    let venueStyling = this.querySelector(".package-styling")?.textContent.trim() || "N/A";
                    let downpayment = this.querySelector(".package-downpayment")?.textContent.trim() || "N/A";
                    let packageImage = this.querySelector("img")?.src || "";

                    // Update modal fields
                    document.getElementById("package_id").value = packageId;
                    document.getElementById("menu_limit").innerText = menuCount;
                    document.getElementById("modal_package_name").innerText = packageName;
                    document.getElementById("modal_package_price").innerText = packagePrice;
                    document.getElementById("modal_people_count").innerText = peopleCount;
                    document.getElementById("modal_venue_styling").innerText = venueStyling;
                    document.getElementById("modal_downpayment").innerText = downpayment;
                    document.getElementById("modal_package_image").src = packageImage;

                    // Reset all checkboxes
                    document.querySelectorAll(".menu-checkbox").forEach(cb => {
                        cb.checked = false;  // Uncheck all
                        cb.disabled = false; // Enable all
                    });

                    // Show modal
                    $("#reservationModal").modal("show");
                });
            });

            // Handle menu limit selection
            document.querySelectorAll(".menu-checkbox").forEach(checkbox => {
                checkbox.addEventListener("change", function () {
                    let checkedCount = document.querySelectorAll(".menu-checkbox:checked").length;
                    let maxSelection = parseInt(document.getElementById("menu_limit").innerText) || 0;

                    document.querySelectorAll(".menu-checkbox").forEach(cb => {
                        if (checkedCount >= maxSelection) {
                            if (!cb.checked) {
                                cb.disabled = true;
                            }
                        } else {
                            cb.disabled = false;
                        }
                    });
                });
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
        const eventDateInput = document.getElementById("event_date");

        // Get the current date
        let today = new Date();
        today.setDate(today.getDate() + 7); // Set the minimum date to next week

        // Format the min date to YYYY-MM-DD
        let minDateStr = today.toISOString().split("T")[0];
        eventDateInput.setAttribute("min", minDateStr);

        // Get reserved dates from PHP
        let reservedDates = <?php echo json_encode($reservedDates); ?>;

        // Disable reserved dates
        eventDateInput.addEventListener("input", function () {
            if (reservedDates.includes(this.value)) {
                alert("This date is already reserved. Please select another date.");
                this.value = ""; // Clear selected date
            }
        });
    });
</script>

<script>
    document.getElementById('start_time').addEventListener('input', function () {
        if (this.value > '21:00') {
            alert('Start time cannot be later than 8:00 PM.');
            this.value = '20:00';
        }
    });
</script>

</body>

</html>