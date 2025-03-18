<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); // Redirect to home or login
    exit();
}


require_once '../../data-handling/db/connection.php';

$sql = "SELECT id, name, description, category, image, created_at FROM menu ORDER BY created_at DESC";
$result = $con->query($sql);


$category_query = "SELECT DISTINCT category FROM menu ORDER BY category ASC";
$category_result = $con->query($category_query);

$categories = [];
if ($category_result->num_rows > 0) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = htmlspecialchars($row['category']);
    }
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

            <li class="nav-item active">
                <a class="nav-link" href="./menu.php">
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
                    <i class="fas fa-fw fa-user"></i>
                    <span>Users</span></a>
            </li>

            <li class="nav-item ">
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
                        <h1 class="h3 mb-0 text-gray-800">Menu</h1>
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
                                <i class="fas fa-plus"></i> Add Menu
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Add Menu Modal -->
                <div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMenuModalLabel">Add New Menu</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addMenuForm" enctype="multipart/form-data" action="./add_menu.php" method="post">
                                    <!-- Menu Name -->
                                    <div class="form-group">
                                        <label for="addMenuName">Menu Name</label>
                                        <input type="text" class="form-control" id="addMenuName" name="addMenuName" required>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <label for="addMenuDescription">Description</label>
                                        <textarea class="form-control" id="addMenuDescription" name="addMenuDescription" rows="3" required></textarea>
                                    </div>

                                    <!-- category -->
                                    <div class="form-group">
                                        <label for="addMenuCategory">Category</label>
                                        <select class="form-control" name="addMenuCategory" id="addMenuCategory">
                                            <?php if (!empty($categories)): ?>
                                                <?php foreach ($categories as $category): ?>
                                                    <option value="<?= $category; ?>"><?= $category; ?></option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option disabled>No category found</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="form-group">
                                        <label for="menuImage">Upload Image</label>
                                        <input type="file" class="form-control-file" id="menuImage" name="menuImage" required>
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
                                                   CHOLES Menu
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <?= htmlspecialchars($row['name']); ?>
                                                </div>
                                                <span class="badge badge-success">
                                                    <?= htmlspecialchars($row['category']); ?>
                                                </span>
                                            </div>
                                            <div class="col-auto">
                                                <img src="<?= htmlspecialchars($row['image']); ?>" alt="Menu Image" style="width: 150px; height: 100px; object-fit: cover;">
                                            </div>

                                            <div>
                                                <button class="btn btn-success btn-sm edit-menu-btn"
                                                    data-id="<?= $row['id']; ?>"
                                                    data-name="<?= htmlspecialchars($row['name']); ?>"
                                                    data-description="<?= htmlspecialchars($row['description']); ?>"
                                                    data-category="<?= htmlspecialchars($row['category']); ?>"
                                                    data-toggle="modal"
                                                    data-target="#menuDetailsModal">
                                                    Edit
                                                </button>

                                                <form method="POST" action="delete_menu.php" style="display:inline;">
                                                    <input type="hidden" name="menuId" value="<?= htmlspecialchars($row['id']); ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p>No menus found.</p>
                        </div>
                    <?php endif; ?>
                </div>


<!-- Edit menu modal -->
<div class="modal fade" id="menuDetailsModal" tabindex="-1" role="dialog" aria-labelledby="menuDetailsModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Menu</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editMenuForm" enctype="multipart/form-data" action="./edit_menu.php" method="post">
                    <input type="hidden" id="menuId" name="menuId">

                    <!-- Menu Name -->
                    <div class="form-group">
                        <label for="menuName">Menu Name</label>
                        <input type="text" class="form-control" id="menuName" name="menuName" required>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label for="menuDescription">Description</label>
                        <textarea class="form-control" id="menuDescription" name="menuDescription" rows="3" required></textarea>
                    </div>

                    <!-- Category -->
                    <div class="form-group">
                        <label for="editmenuCategory">Category</label>
                        <select class="form-control" name="editMenuCategory" id="editMenuCategory">
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category; ?>"><?= $category; ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option disabled>No category found</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Image Upload -->
                    <div class="form-group">
                        <label for="menuImage">Upload Image</label>
                        <input type="file" class="form-control-file" id="menuImage" name="menuImage">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-success">Save Menu</button>
                </form>
            </div>
        </div>
    </div>
</div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".edit-menu-btn").forEach(button => {
                button.addEventListener("click", function () {
                    const menuId = this.getAttribute("data-id");
                    const menuName = this.getAttribute("data-name");
                    const menuDescription = this.getAttribute("data-description");
                    const menuCategory = this.getAttribute("data-category");

                    // Set values in modal fields
                    document.getElementById("menuId").value = menuId;
                    document.getElementById("menuName").value = menuName;
                    document.getElementById("menuDescription").value = menuDescription;

                    // Set the category dropdown
                    let categorySelect = document.getElementById("editMenuCategory");
                    for (let option of categorySelect.options) {
                        if (option.value === menuCategory) {
                            option.selected = true;
                            break;
                        }
                    }
                });
            });
        });

        </script>


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