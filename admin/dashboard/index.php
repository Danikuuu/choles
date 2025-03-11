<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0) {
    header("Location: ../index.php"); // Redirect to home or login
    exit();
}

require_once '../../data-handling/db/connection.php';

$monthEarnings = "SELECT SUM(p.package_price) AS earnings_this_month
                    FROM reservations r
                    JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
                    JOIN package p ON cpm.package_id = p.id
                    WHERE r.status = 'completed'
                    AND MONTH(r.event_date) = MONTH(CURRENT_DATE())
                    AND YEAR(r.event_date) = YEAR(CURRENT_DATE());";
$monthResult = $con->query($monthEarnings);

$monthEarnings = 0; 
if ($monthResult) {
    $row = $monthResult->fetch_assoc();
    $monthEarnings = $row['earnings_this_month'] ?? 0;
}

$yearResult = "SELECT SUM(p.package_price) AS earnings_this_year
                      FROM reservations r
                      JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
                      JOIN package p ON cpm.package_id = p.id
                      WHERE r.status = 'completed'
                      AND YEAR(r.event_date) = YEAR(CURRENT_DATE());";

$yearResult = $con->query($yearResult);

$yearEarnings = 0;
if ($yearResult) {
    $row = $yearResult->fetch_assoc();
    $yearEarnings = $row['earnings_this_year'] ?? 0;
}

$reservationQuery = "SELECT COUNT(*) AS total_reservations FROM reservations";

$reservationResult = $con->query($reservationQuery);

$numberOfReservations = 0;
if ($reservationResult) {
    $row = $reservationResult->fetch_assoc();
    $numberOfReservations = $row['total_reservations'] ?? 0;
}

$pendingReservationQuery = "SELECT COUNT(*) AS total_pending_reservations FROM reservations WHERE status = 'pending'";

$pendingResult = $con->query($pendingReservationQuery);

$totalPendingReservations = 0; 
if ($pendingResult) {
    $row = $pendingResult->fetch_assoc();
    $totalPendingReservations = $row['total_pending_reservations'] ?? 0; 
}

$earningsQuery = "SELECT 
    months.month, 
    COALESCE(SUM(p.package_price), 0) AS total_earnings
FROM (
    SELECT 1 AS month UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION
    SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION 
    SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
) AS months
LEFT JOIN reservations r 
    ON MONTH(r.event_date) = months.month 
    AND YEAR(r.event_date) = YEAR(CURRENT_DATE()) 
    AND r.status = 'completed'
LEFT JOIN customer_package_menu cpm 
    ON r.customer_package_id = cpm.id
LEFT JOIN package p 
    ON cpm.package_id = p.id
GROUP BY months.month
ORDER BY months.month;

";

$result = $con->query($earningsQuery);

$allMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
$months = [];
$earnings = [];

while ($row = $result->fetch_assoc()) {
    $months[] = date("M", mktime(0, 0, 0, $row["month"], 1));
    $earnings[] = $row["total_earnings"];
}

$finalEarnings = array_fill(0, 12, 0);
foreach ($months as $index => $month) {
    $finalEarnings[array_search($month, $allMonths)] = $earnings[$index];
}

$monthsJSON = json_encode($allMonths);
$earningsJSON = json_encode($finalEarnings);

$statusCount = "SELECT status, COUNT(*) as count FROM reservations GROUP BY status";
$statusResult = $con->query($statusCount);

$statusCounts = [];
$labels = [];
$colors = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'];
$i = 0;

while ($row = $statusResult->fetch_assoc()) {
    $labels[] = $row['status'];
    $statusCounts[] = $row['count'];
}
$labelsJSON = json_encode($labels);
$statusCountsJSON = json_encode($statusCounts);

$stocksQuery = "SELECT item_name, quantity FROM equipment_inventory";
$stocksResult = $con->query($stocksQuery);

$itemNames = [];
$quantities = [];

while ($row = $stocksResult->fetch_assoc()) {
    $itemNames[] = $row['item_name'];
    $quantities[] = $row['quantity'];
}

$itemNamesJSON = json_encode($itemNames);
$quantitiesJSON = json_encode($quantities);


$history = "SELECT 
            r.id AS reservation_id,
            cpm.id AS customer_package_id,
            CONCAT(c.fname, ' ', c.lname) AS customer_name,
            r.event_date,
            r.status,
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
        ORDER BY r.event_date DESC
        LIMIT 5";

$historyResult = $con->query($history);
?>

<!-- add a predictive analysis here to recommend what menu or package is good in this quarter of the year -->

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CHOLES Admin - Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar" style="background-color:  #059652;">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">CHOLES <sup>Admin</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Menu Management
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
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

            <li class="nav-item ">
                <a class="nav-link" href="./users.php">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Users</span></a>
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
                                <a class="dropdown-item" href="" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <a href="../../generate_report.php" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Earnings (This Month)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱ <?php echo $monthEarnings ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Earnings (This Year)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">₱ <?php echo $yearEarnings ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Reservations
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $numberOfReservations ?></div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pending Requests</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalPendingReservations; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-success">Earnings Overview</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-success">Reservations</h6>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-primary"></i> Completed
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-warning"></i> Pending
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-info"></i> Approved
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-danger"></i> Cancelled
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">

                        <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Stocks</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-bar">
                                        <canvas id="myBarChart1"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-6 mb-4">

                            <!-- Illustrations -->
                            <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-success">History</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Package</th>
                                            <th>Venue</th>
                                            <th>Date/th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        if ($historyResult->num_rows > 0) {
                                            while ($row = $historyResult->fetch_assoc()) { // Corrected variable name
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row['customer_name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['package_name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['venue']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['event_date']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5' class='text-center'>No records found</td></tr>"; // Adjusted colspan to 5
                                        }
                                    ?>
                                    </tbody>
                                </table>
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
                    <a class="btn btn-primary" href="../../destroy.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <!-- <script src="vendor/jquery-easing/jquery.easing.min.js"></script> -->

    <!-- Custom scripts for all pages-->
    <!-- <script src="js/sb-admin-2.min.js"></script> -->

    <!-- Page level plugins -->
    <!-- <script src="vendor/chart.js/Chart.min.js"></script> -->

    <!-- Page level custom scripts -->
    <!-- <script src="js/demo/chart-area-demo.js"></script> -->
    <!-- <script src="js/demo/chart-pie-demo.js"></script> -->
    <!-- <script src="js/demo/chart-bar-demo.js"></script> -->
    <!-- <script src="js/demo/datatables-demo.js"></script>   -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Line Chart -->
<script>
    Chart.defaults.font.family = 'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
    Chart.defaults.color = '#858796';


    const months = <?php echo $monthsJSON; ?>; 
    const earnings = <?php echo $earningsJSON; ?>;

    console.log(months)

    const canvas = document.getElementById("myAreaChart");
    if (canvas) {
        const ctx = canvas.getContext("2d");
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months, 
                datasets: [{
                    label: "Total Earnings ₱",
                    data: earnings,
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2
                }]
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 7 } },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {  
                        tooltips: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyFontColor: "#858796",
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            xPadding: 15,
                            yPadding: 15,
                            displayColors: false,
                            caretPadding: 10,
                        }
                    }
                }
            }
        });
    }
</script>

<script>
    const labels = <?php echo $labelsJSON; ?>;
    const statusCounts = <?php echo $statusCountsJSON; ?>;
    
    var ctx = document.getElementById("myPieChart");
    if (ctx) {
        var myPieChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: statusCounts, 
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                plugins:{
                    legend: {
                        display: false
                    }
                },
                cutoutPercentage: 70,
            },
        });
    }
</script>

<script>
    const itemNames = <?php echo $itemNamesJSON; ?>;
    const quantities = <?php echo $quantitiesJSON; ?>;

    // Bar Chart Example
    var ctx = document.getElementById("myBarChart1");
    var myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: itemNames, // Use database item names
            datasets: [{
                label: "Stock Quantity",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: quantities, // Use database quantities
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        autoSkip: false, // Display all item names
                        maxRotation: 45,
                        minRotation: 45
                    },
                    maxBarThickness: 25,
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        padding: 10,
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            plugins:{
                legend: {
                    display: false
                }
            },
            tooltips: {
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel;
                    }
                }
            },
        }
    });
</script>

</body>

</html>