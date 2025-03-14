<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0) {
    header("Location: index.php");
    exit();
}
// require_once('./vendor/tecnickcom/tcpdf/tcpdf.php'); 
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');
require_once('../../data-handling/db/connection.php'); 

$cateringName = "CHOLES Catering Services";

// Get sales earnings for the current month
$monthEarnings = "SELECT SUM(p.package_price) AS earnings_this_month
                    FROM reservations r
                    JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
                    JOIN package p ON cpm.package_id = p.id
                    WHERE r.status = 'completed'
                    AND MONTH(r.event_date) = MONTH(CURRENT_DATE())
                    AND YEAR(r.event_date) = YEAR(CURRENT_DATE());";
$monthResult = $con->query($monthEarnings);
$totalSales = ($monthResult->num_rows > 0) ? $monthResult->fetch_assoc()['earnings_this_month'] : 0;

// Get sales earnings for the current year
$yearEarnings = "SELECT SUM(p.package_price) AS earnings_this_year
                 FROM reservations r
                 JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
                 JOIN package p ON cpm.package_id = p.id
                 WHERE r.status = 'completed'
                 AND YEAR(r.event_date) = YEAR(CURRENT_DATE());";
$yearResult = $con->query($yearEarnings);
$totalYearlySales = ($yearResult->num_rows > 0) ? $yearResult->fetch_assoc()['earnings_this_year'] : 0;

// Get total reservations for the current month
$reservationQuery = "SELECT COUNT(*) AS total_reservations 
                     FROM reservations 
                     WHERE YEAR(event_date) = YEAR(CURRENT_DATE()) 
                     AND MONTH(event_date) = MONTH(CURRENT_DATE());";
$reservationResult = $con->query($reservationQuery);
$totalReservations = ($reservationResult->num_rows > 0) ? $reservationResult->fetch_assoc()['total_reservations'] : 0;

// Get the top 3 most reserved packages
$mostReservedPackageQuery = "SELECT p.package_name, COUNT(*) AS reservation_count 
                        FROM customer_package_menu cpm
                        JOIN package p ON cpm.package_id = p.id
                        GROUP BY cpm.package_id
                        ORDER BY reservation_count DESC
                        LIMIT 3;";
$mostReservedPackageResult = $con->query($mostReservedPackageQuery);
$mostReservedPackages = [];
while ($row = $mostReservedPackageResult->fetch_assoc()) {
    $mostReservedPackages[] = $row['package_name'] . " (" . $row['reservation_count'] . ")";
}
$mostOrderedPackage = !empty($mostReservedPackages) ? implode(", ", $mostReservedPackages) : "No data";

// Get the top 5 most chosen menu items
$mostReservedMenuQuery = "SELECT m.name AS menu_name, COUNT(*) AS menu_count 
                          FROM customer_package_menu cpm
                          JOIN menu m ON cpm.menu_id = m.id
                          GROUP BY cpm.menu_id
                          ORDER BY menu_count DESC
                          LIMIT 5;";
$mostReservedMenuResult = $con->query($mostReservedMenuQuery);
$mostReservedMenus = [];
while ($row = $mostReservedMenuResult->fetch_assoc()) {
    $mostReservedMenus[] = $row['menu_name'] . " (" . $row['menu_count'] . ")";
}
$mostOrderedMenu = !empty($mostReservedMenus) ? implode(", ", $mostReservedMenus) : "No data";

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Catering Report');
$pdf->SetTitle('Catering Sales Report');
$pdf->SetHeaderData('', 0, 'Sales Report', "Generated on: " . date('Y-m-d'));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('helvetica', '', 12);
$pdf->AddPage();


$html = "
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        padding: 20px;
        background-color: #f8f9fa;
    }
    .report-container {
        background: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    h3 {
        text-align: center;
        color: #34495e;
        margin-bottom: 15px;
    }
    hr {
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }
    p {
        font-size: 16px;
        color: #555;
        line-height: 1.6;
        margin: 10px 0;
    }
    .highlight {
        font-weight: bold;
        color: #d35400;
    }
</style>

<div class='report-container'>
    <h2>$cateringName</h2>
    <h3>Monthly Sales Report</h3>
    <hr>
    <p><span class='highlight'>Sales this Month:</span> ₱$totalSales</p>
    <p><span class='highlight'>Sales this Year:</span> ₱$totalYearlySales</p>
    <p><span class='highlight'>Total Reservations this Month:</span> $totalReservations</p>
    <p><span class='highlight'>Most Ordered Menu Items:</span> $mostOrderedMenu</p>
    <p><span class='highlight'>Most Ordered Packages:</span> $mostOrderedPackage</p>
</div>
";

// Write content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('catering_report.pdf', 'D'); // 'D' forces download

exit;
?>
