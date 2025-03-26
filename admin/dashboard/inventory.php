<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php"); // Redirect to home or login
    exit();
}

require_once '../../data-handling/db/connection.php';

$sql = "SELECT * FROM equipment_inventory";
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
            <li class="nav-item active">
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
                        <h1 class="h3 mb-0 text-gray-800">Equipment Inventory</h1>
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
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Add Menu Modal -->
                <div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addMenuModalLabel">Add New Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addMenuForm" enctype="multipart/form-data" action="./add_inventory.php" method="post">
                                    <!-- Menu Name -->
                                    <div class="form-group">
                                        <label for="itemName">Item Name</label>
                                        <input type="text" class="form-control" id="itemName" name="itemName" required>
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <label for="itemQuantity">Quantity</label>
                                        <input type="number" class="form-control" name="itemQuantity" id="itemQuantity">
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-success">Save Menu</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Display Menu Items -->
                <div class="row justify-content-center align-items-center p-5">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-xl-3 col-md-6 mb-4">
                                <div class="card shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    <?php echo htmlspecialchars($row['item_name']); ?>
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    <i class="fas fa-basket"></i> <?php echo htmlspecialchars($row['quantity']); ?>
                                                </div>
                                                <span class="badge badge-success">
                                                    <?php echo htmlspecialchars($row['unit']); ?>
                                                </span>
                                            </div>

                                            <div>
                                                <button class="btn btn-success btn-sm edit-btn"
                                                    data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                                    data-name="<?php echo htmlspecialchars($row['item_name']); ?>"
                                                    data-quantity="<?php echo htmlspecialchars($row['quantity']); ?>"
                                                    data-unit="<?php echo htmlspecialchars($row['unit']); ?>"
                                                    data-toggle="modal"
                                                    data-target="#editModal">Edit</button>

                                                    <button class="btn btn-success btn-sm add-quantity-btn"
                                                    data-add-id="<?php echo htmlspecialchars($row['id']); ?>"
                                                    data-add-name="<?php echo htmlspecialchars($row['item_name']); ?>"
                                                    data-add-unit="<?php echo htmlspecialchars($row['unit']); ?>"
                                                    data-toggle="modal"
                                                    data-target="#addQuantityModal">Add Quantity</button>

                                                    <button class="btn btn-success btn-sm minus-quantity-btn"
                                                    data-minus-id="<?php echo htmlspecialchars($row['id']); ?>"
                                                    data-minus-name="<?php echo htmlspecialchars($row['item_name']); ?>"
                                                    data-minus-unit="<?php echo htmlspecialchars($row['unit']); ?>"
                                                    data-toggle="modal"
                                                    data-target="#minusQuantityModal">Decrease Quantity</button>

                                                <form method="POST" action="delete_inventory_item.php" style="display:inline;">
                                                    <input type="hidden" name="inventoryId" value="<?= htmlspecialchars($row['id']); ?>">
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

                <!-- Modal for Editing -->
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Item</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="update_inventory.php" enctype="multipart/form-data" method="POST">
                                    <input type="hidden" name="id" id="edit-id">

                                    <div class="form-group">
                                        <label for="edit-name">Item Name</label>
                                        <input type="text" class="form-control" id="edit-name" name="item_name" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-quantity">Quantity</label>
                                        <input type="number" class="form-control" id="edit-quantity" name="quantity" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit-unit">Unit</label>
                                        <input type="text" class="form-control" id="edit-unit" name="unit" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Add Quantity -->
                <div class="modal fade" id="addQuantityModal" tabindex="-1" role="dialog" aria-labelledby="eaddQuantityLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addQuantityLabel">Add Stock</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="increase_quantity.php" enctype="multipart/form-data" method="POST">
                                    <input type="hidden" name="id" id="add-id">

                                    <div class="form-group">
                                        <label for="add-name">Item Name</label>
                                        <input type="text" class="form-control" id="add-name" name="item_name" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label for="add-quantity">Quantity</label>
                                        <input type="number" class="form-control" id="add-quantity" name="quantity" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="add-unit">Unit</label>
                                        <input type="text" class="form-control" id="add-unit" name="unit" disabled>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="minusQuantityModal" tabindex="-1" role="dialog" aria-labelledby="minusQuantityLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addQuantityLabel">Decrease Stock</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="decrease_quantity.php" enctype="multipart/form-data" method="POST">
                                    <input type="hidden" name="id" id="minus-id">

                                    <div class="form-group">
                                        <label for="minus-name">Item Name</label>
                                        <input type="text" class="form-control" id="minus-name" name="item_name" disabled>
                                    </div>

                                    <div class="form-group">
                                        <label for="minus-quantity">Quantity</label>
                                        <input type="number" class="form-control" id="minus-quantity" name="quantity" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="minus-unit">Unit</label>
                                        <input type="text" class="form-control" id="minus-unit" name="unit" disabled>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
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
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".edit-btn").forEach(button => {
            button.addEventListener("click", function() {
                document.getElementById("edit-id").value = this.getAttribute("data-id");
                document.getElementById("edit-name").value = this.getAttribute("data-name");
                document.getElementById("edit-quantity").value = this.getAttribute("data-quantity");
                document.getElementById("edit-unit").value = this.getAttribute("data-unit");
            });
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".add-quantity-btn").forEach(button => {
            button.addEventListener("click", function() {
                document.getElementById("add-id").value = this.getAttribute("data-add-id");
                document.getElementById("add-name").value = this.getAttribute("data-add-name");
                document.getElementById("add-unit").value = this.getAttribute("data-add-unit");
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".minus-quantity-btn").forEach(button => {
            button.addEventListener("click", function() {
                document.getElementById("minus-id").value = this.getAttribute("data-minus-id");
                document.getElementById("minus-name").value = this.getAttribute("data-minus-name");
                document.getElementById("minus-unit").value = this.getAttribute("data-minus-unit");
            });
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