<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); // Redirect to home or login
    exit();
}


require_once '../../data-handling/db/connection.php';

$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch paginated data
$sql = "SELECT id, fname, lname, email, mobile FROM user WHERE role = 2 LIMIT $limit OFFSET $offset";
$result = $con->query($sql);

// Get total number of records for pagination
$total_sql = "SELECT COUNT(*) AS total FROM user WHERE role = 2";
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

    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <div id="wrapper">

        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color:  #059652;">

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-text mx-3">CHOLES <sup>Admin</sup></div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Menu Management
            </div>

            <li class="nav-item">
                <a class="nav-link" href="./menu.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Menu</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./package.php">
                    <i class="fas fa-fw fa-utensils"></i>
                    <span>Packages</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./messages.php">
                    <i class="fas fa-envelope"></i> Messages
                    <span id="unreadBadge" class="badge badge-danger" style="display: none;"></span>
                </a>
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

            <li class="nav-item">
                <a class="nav-link" href="./coupon.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Coupon</span></a>
            </li>

            <!-- Anomyties -->
            <li class="nav-item">
                <a class="nav-link" href="./inventory.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Equipments</span></a>
            </li>

            <li class="nav-item ">
                <a class="nav-link" href="./feedback.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Feedback</span></a>
            </li>

            <li class="nav-item ">
                <a class="nav-link" href="./users.php">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Users</span></a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="./staff.php">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Staff</span></a>
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
                        <div>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#addMenuModal">
                                <i class="fas fa-plus"></i> Add Staff
                            </button>
                        </div>
                    </div>
                    <!-- Add Staff Modal -->
                    <div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addMenuModalLabel">Add New Staff</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="addMenuForm" enctype="multipart/form-data" action="./add_new_staff.php" method="post">
                                        <!-- Menu Name -->
                                        <div class="form-group">
                                            <label for="addMenuName">Staff First Name</label>
                                            <input type="text" class="form-control" id="staffFname" name="staffFname" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="addMenuName">Staff Last Name</label>
                                            <input type="text" class="form-control" id="staffLname" name="staffLname" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="addMenuName">Staff Email</label>
                                            <input type="email" class="form-control" id="staffEmail" name="staffEmail" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="addMenuName">Staff Mobile</label>
                                            <input type="number" class="form-control" id="staffMobile" name="staffMobile" required>
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

                                        <div class="form-group">
                                            <label for="addMenuName">Staff Street</label>
                                            <input type="text" class="form-control" id="street_name" name="street_name" required>
                                        </div>

                                        <!-- Submit Button -->
                                        <button type="submit" class="btn btn-success">Save Menu</button>
                                    </form>
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
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($result->num_rows > 0) : ?>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['fname']) . " " . htmlspecialchars($row['lname']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['mobile']); ?></td>
                    <td>
                        <button class='btn btn-success btn-sm view-btn'
                            data-id='<?= htmlspecialchars($row['id']); ?>'
                            data-fname='<?= htmlspecialchars($row['fname']); ?>' 
                            data-lname='<?= htmlspecialchars($row['lname']); ?>' 
                            data-email='<?= htmlspecialchars($row['email']); ?>' 
                            data-mobile='<?= htmlspecialchars($row['mobile']); ?>'>
                            Edit
                        </button>

                        <form method='POST' action='./delete_staff.php' style='display:inline;'>
                            <input type='hidden' name='id' value='<?= htmlspecialchars($row['id']); ?>'>
                            <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm("Are you sure you want to delete this staff?");'>Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr><td colspan='4' class='text-center'>No records found</td></tr>
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
                            <div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="viewModalLabel">Edit Staff Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="./edit_staff.php" enctype="multipart/form-data" method="post">
                                            <input type="hidden" name="id" id="id">
                                                <div class="mb-3">
                                                    <label class="form-label" for="firstName">First Name</label>
                                                    <input class="form-control" type="text" name="fname" id="fname">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="firstName">Last Name</label>
                                                    <input class="form-control" type="text" name="lname" id="lname">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="firstName">Email</label>
                                                    <input class="form-control" type="text" name="email" id="email">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="firstName">Mobile Number</label>
                                                    <input class="form-control" type="Number" name="mobile" id="mobile">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label" for="firstName">New Password</label>
                                                    <input class="form-control" type="password" name="password]" id="password]">
                                                </div>
                                                <button type="submit" class="btn btn-success">Save</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
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
        let id = $(this).data("id");
        let fname = $(this).data("fname");
        let lname = $(this).data("lname");
        let email = $(this).data("email");
        let mobile = $(this).data("mobile");

        // Set modal content
        $("#id").val(id);
        $("#fname").val(fname);
        $("#lname").val(lname);
        $("#email").val(email);
        $("#mobile").val(mobile);

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
let provinces = [];
let cities = [];
let barangays = [];

$(document).ready(function() {
    // Load Province Data
    $.getJSON("../../province.json", function(data) {
        provinces = data;
        $.each(provinces, function(index, province) {
            $("#province").append(`<option value="${province.province_code}">${province.province_name}</option>`);
        });
    });

    // Load City Data
    $.getJSON("../../city.json", function(data) {
        cities = data;
    });

    // Load Barangay Data
    $.getJSON("../../barangay.json", function(data) {
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