<?php
session_start();

if (!isset($_SESSION["user_id"]) && $_SESSION["role"] !== 1) {
    header("Location: ../index.php");
    exit();
}


require_once '../../data-handling/db/connection.php';

$sql = "SELECT * FROM package ORDER BY created_at DESC";
$result = $con->query($sql);

?>

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

            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
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

            <!-- Anomyties -->
            <li class="nav-item">
                <a class="nav-link" href="./inventory.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Equipments</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./feedback.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Feedback</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="./users.php">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>users</span></a>
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
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">CHOLES Admin</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4 position-relative">
                        <h1 class="h3 mb-0 text-gray-800">Packages</h1>
                            <!-- Toast Container -->
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
                                <i class="fas fa-plus"></i> Add Package
                            </button>

                            <button class="btn btn-success"><i class="fas fa-archive"></i> Archive</button>
                        </div>
                    </div>

                </div>

                <!-- Add Menu Modal -->
                <div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMenuModalLabel">Add New Package</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addMenuForm" enctype="multipart/form-data" action="./add_package.php" method="post">
                                    <!-- Menu Name -->
                                    <div class="form-group">
                                        <label for="packageName">Package Name</label>
                                        <input type="text" class="form-control" id="packageName" name="packageName" required>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <label for="packageDescription">Description</label>
                                        <textarea class="form-control" id="packageDescription" name="packageDescription" rows="3" required></textarea>
                                    </div>

                                    <!-- Price -->
                                    <div class="form-group">
                                        <label for="packagePrice">Package Price</label>
                                        <input type="text" class="form-control" id="packagePrice" name="packagePrice" required>
                                    </div>

                                    <!-- People Count -->
                                    <div class="form-group">
                                        <label for="packagePeople">People Count</label>
                                        <input type="text" class="form-control" id="packagePeople" name="packagePeople" required>
                                    </div>

                                    <!-- Number of menu -->
                                    <div class="form-group">
                                        <label for="packageMenu">Number of Menus</label>
                                        <input type="text" class="form-control" id="packageMenu" name="packageMenu" required>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <select name="packagestyling" id="packagestyling">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </div>

                                    <!-- Table Count -->
                                    <div class="form-group">
                                        <label for="packageTables">Number of Tables</label>
                                        <input type="text" class="form-control" id="packageTables" name="packageTables" required>
                                    </div>

                                    <!-- Chair count -->
                                    <div class="form-group">
                                        <label for="packageChairs">Number of Chairs</label>
                                        <input type="text" class="form-control" id="packageChairs" name="packageChairs" required>
                                    </div>

                                    <!-- Chair count -->
                                    <div class="form-group">
                                        <label for="packageGlass">Number of Glass</label>
                                        <input type="text" class="form-control" id="packageGlass" name="packageGlass" required>
                                    </div>

                                    <!-- Plate count -->
                                    <div class="form-group">
                                        <label for="packagePlates">Number of Plates</label>
                                        <input type="text" class="form-control" id="packagePlates" name="packagePlates" required>
                                    </div>

                                    <!-- Spoon count -->
                                    <div class="form-group">
                                        <label for="packageSpoon">Number of Spoon</label>
                                        <input type="text" class="form-control" id="packageSpoon" name="packageSpoon" required>
                                    </div>

                                    <!-- Fork count -->
                                    <div class="form-group">
                                        <label for="packageFork">Number of Fork</label>
                                        <input type="text" class="form-control" id="packageFork" name="packageFork" required>
                                    </div>

                                    <!-- Venue -->
                                    <div class="form-group">
                                        <label for="packageVenue">Venue</label>
                                        <input type="text" class="form-control" id="packageVenue" name="packageVenue" required>
                                    </div>

                                     <!-- Image Upload -->
                                     <div class="form-group">
                                        <label for="packageImage">Upload Image</label>
                                        <input type="file" class="form-control-file" id="packageImage" name="packageImage" required>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-success">Save Menu</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
 
                <div class="row justify-content-center align-items-center p-5">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    <?php echo htmlspecialchars($row['package_name']); ?>
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    ₱<?php echo htmlspecialchars($row['package_price']); ?>
                                                </div>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-users"></i> <?php echo htmlspecialchars($row['people_count']); ?>
                                                </span>

                                                <span class="badge badge-warning">
                                                    <i class="fas fa-pizza-slice"></i> <?php echo htmlspecialchars($row['menu_count']); ?>
                                                </span>

                                                <span class="badge badge-danger">
                                                    <i class="fas fa-paint-brush"></i> <?php echo htmlspecialchars($row['menu_count']); ?>
                                                </span>

                                                <span class="badge badge-info">
                                                    <i class="fas fa-table"></i> <?php echo htmlspecialchars($row['menu_count']); ?>
                                                </span>

                                                <span class="badge badge-primary">
                                                    <i class="fas fa-chair"></i> <?php echo htmlspecialchars($row['menu_count']); ?>
                                                </span>
                                            </div>

                                            <div class="col-auto">
                                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Menu Image" style="width: 150px; height: 100px; object-fit: cover;">
                                            </div>
                                            <div>
                                                <button class="btn btn-success">Edit</button>
                                    
                                                <button class="btn btn-success">Archive</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p>No packages found.</p>
                        </div>
                    <?php endif; ?>
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