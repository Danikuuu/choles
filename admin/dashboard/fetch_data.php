<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php");
    exit();
}

require_once '../../data-handling/db/connection.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$filter = $_GET['filter'] ?? 'year';
$selectedYear = $_GET['year'] ?? date('Y');
$selectedMonth = $_GET['month'] ?? date('m');

$response = [];

if ($filter === 'year') {
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
        AND YEAR(r.event_date) = ? 
        AND r.status = 'completed'
    LEFT JOIN customer_package_menu cpm 
        ON r.customer_package_id = cpm.id
    LEFT JOIN package p 
        ON cpm.package_id = p.id
    GROUP BY months.month
    ORDER BY months.month";

    $stmt = $con->prepare($earningsQuery);
    if (!$stmt) {
        echo json_encode(["error" => "Database error: " . $con->error]);
        exit();
    }

    $stmt->bind_param("i", $selectedYear);
    $stmt->execute();
    $result = $stmt->get_result();

    $allMonths = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    $finalEarnings = array_fill(0, 12, 0);

    while ($row = $result->fetch_assoc()) {
        $monthIndex = $row["month"] - 1;
        $finalEarnings[$monthIndex] = number_format($row["total_earnings"], 2, '.', '');
    }

    $response["labels"] = $allMonths;
    $response["earnings"] = $finalEarnings;
} else {
    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
    $earningsQuery = "SELECT 
        DAY(event_date) AS day, 
        COALESCE(SUM(p.package_price), 0) AS total_earnings
    FROM reservations r
    LEFT JOIN customer_package_menu cpm 
        ON r.customer_package_id = cpm.id
    LEFT JOIN package p 
        ON cpm.package_id = p.id
    WHERE MONTH(r.event_date) = ? 
        AND YEAR(r.event_date) = ? 
        AND r.status = 'completed'
    GROUP BY DAY(r.event_date)
    ORDER BY DAY(r.event_date)";

    $stmt = $con->prepare($earningsQuery);
    if (!$stmt) {
        echo json_encode(["error" => "Database error: " . $con->error]);
        exit();
    }

    $stmt->bind_param("ii", $selectedMonth, $selectedYear);
    $stmt->execute();
    $result = $stmt->get_result();

    $dailyEarnings = array_fill(0, $daysInMonth, 0);
    while ($row = $result->fetch_assoc()) {
        $dayIndex = $row['day'] - 1;
        $dailyEarnings[$dayIndex] = number_format($row['total_earnings'], 2, '.', '');
    }

    $response["labels"] = range(1, $daysInMonth);
    $response["earnings"] = $dailyEarnings;
}

// Order status counts
$statusQuery = "SELECT status, COUNT(*) as count FROM reservations GROUP BY status";
$statusResult = $con->query($statusQuery);

$statusCounts = [];
$labels = [];

while ($row = $statusResult->fetch_assoc()) {
    $labels[] = ucfirst($row['status']);
    $statusCounts[] = (int) $row['count'];
}

$response["statusLabels"] = $labels;
$response["statusCounts"] = $statusCounts;

// Most Sold Packages
$mostSoldPackageQuery = "SELECT p.package_name, COUNT(*) as total_sold 
    FROM reservations r
    JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
    JOIN package p ON cpm.package_id = p.id
    WHERE r.status = 'completed' 
        AND YEAR(r.event_date) = ? 
        " . ($filter === 'month' ? "AND MONTH(r.event_date) = ?" : "") . "
    GROUP BY p.package_name 
    ORDER BY total_sold DESC";

$stmt = $con->prepare($mostSoldPackageQuery);
if ($filter === 'month') {
    $stmt->bind_param("ii", $selectedYear, $selectedMonth);
} else {
    $stmt->bind_param("i", $selectedYear);
}
$stmt->execute();
$mostSoldPackageResult = $stmt->get_result();

$mostSoldPackages = [];
while ($row = $mostSoldPackageResult->fetch_assoc()) {
    $mostSoldPackages[] = [
        "name" => $row["package_name"],
        "count" => (int)$row["total_sold"]
    ];
}
$response["mostSoldPackages"] = $mostSoldPackages;


// Most Sold Menus
$mostSoldMenuQuery = "SELECT m.name, COUNT(*) as total_sold 
    FROM reservations r
    JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
    JOIN menu m ON cpm.menu_id = m.id
    WHERE r.status = 'completed' 
        AND YEAR(r.event_date) = ? 
        " . ($filter === 'month' ? "AND MONTH(r.event_date) = ?" : "") . "
    GROUP BY m.name 
    ORDER BY total_sold DESC";

$stmt = $con->prepare($mostSoldMenuQuery);
if ($filter === 'month') {
    $stmt->bind_param("ii", $selectedYear, $selectedMonth);
} else {
    $stmt->bind_param("i", $selectedYear);
}
$stmt->execute();
$mostSoldMenuResult = $stmt->get_result();

$mostSoldMenus = [];
while ($row = $mostSoldMenuResult->fetch_assoc()) {
    $mostSoldMenus[] = [
        "name" => $row["name"],
        "count" => (int)$row["total_sold"]
    ];
}
$response["mostSoldMenus"] = $mostSoldMenus;

// Monthly Earnings (Filtered)
$monthEarningsQuery = "SELECT SUM(p.package_price) AS earnings_this_month
                        FROM reservations r
                        JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
                        JOIN package p ON cpm.package_id = p.id
                        WHERE r.status = 'completed'
                        AND MONTH(r.event_date) = ?
                        AND YEAR(r.event_date) = ?";

$stmt = $con->prepare($monthEarningsQuery);
$stmt->bind_param("ii", $selectedMonth, $selectedYear);
$stmt->execute();
$monthResult = $stmt->get_result();

$monthEarnings = 0;
if ($monthResult) {
    $row = $monthResult->fetch_assoc();
    $monthEarnings = $row['earnings_this_month'] ?? 0;
}
$response["monthEarnings"] = $monthEarnings;

// Yearly Earnings (Filtered)
$yearEarningsQuery = "SELECT SUM(p.package_price) AS earnings_this_year
                        FROM reservations r
                        JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
                        JOIN package p ON cpm.package_id = p.id
                        WHERE r.status = 'completed'
                        AND YEAR(r.event_date) = ?";

$stmt = $con->prepare($yearEarningsQuery);
$stmt->bind_param("i", $selectedYear);
$stmt->execute();
$yearResult = $stmt->get_result();

$yearEarnings = 0;
if ($yearResult) {
    $row = $yearResult->fetch_assoc();
    $yearEarnings = $row['earnings_this_year'] ?? 0;
}
$response["yearEarnings"] = $yearEarnings;

// Total Reservations (Filtered)
$reservationQuery = "SELECT COUNT(*) AS total_reservations 
                     FROM reservations
                     WHERE YEAR(event_date) = ?
                     " . ($filter === 'month' ? "AND MONTH(event_date) = ?" : "");

$stmt = $con->prepare($reservationQuery);
if ($filter === 'month') {
    $stmt->bind_param("ii", $selectedYear, $selectedMonth);
} else {
    $stmt->bind_param("i", $selectedYear);
}
$stmt->execute();
$reservationResult = $stmt->get_result();

$numberOfReservations = 0;
if ($reservationResult) {
    $row = $reservationResult->fetch_assoc();
    $numberOfReservations = $row['total_reservations'] ?? 0;
}
$response["numberOfReservations"] = $numberOfReservations;

// Pending Reservations (Filtered)
$pendingReservationQuery = "SELECT COUNT(*) AS total_pending_reservations 
                            FROM reservations 
                            WHERE status = 'pending' 
                            AND YEAR(event_date) = ? 
                            " . ($filter === 'month' ? "AND MONTH(event_date) = ?" : "");

$stmt = $con->prepare($pendingReservationQuery);
if ($filter === 'month') {
    $stmt->bind_param("ii", $selectedYear, $selectedMonth);
} else {
    $stmt->bind_param("i", $selectedYear);
}
$stmt->execute();
$pendingResult = $stmt->get_result();

$totalPendingReservations = 0; 
if ($pendingResult) {
    $row = $pendingResult->fetch_assoc();
    $totalPendingReservations = $row['total_pending_reservations'] ?? 0; 
}
$response["totalPendingReservations"] = $totalPendingReservations;

echo json_encode($response);
?>
