<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    header("Location: ../../index.php");
    exit();
}
 
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');
require_once('../../data-handling/db/connection.php'); 

$cateringName = "CHOLES Catering Services";

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Catering Report');
$pdf->SetTitle('Catering Sales Report');
$pdf->SetHeaderData('', 0, 'Catering Sales Report', "Generated on: " . date('Y-m-d'));
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->SetFont('dejavusans', '', 12);
$pdf->AddPage();

// Fetch sales earnings for each month
$monthlySalesQuery = "SELECT MONTH(event_date) AS month_num, MONTHNAME(event_date) AS month, 
                      SUM(p.package_price) AS earnings 
                      FROM reservations r
                      JOIN customer_package_menu cpm ON r.customer_package_id = cpm.id
                      JOIN package p ON cpm.package_id = p.id
                      WHERE r.status = 'completed'
                      AND YEAR(r.event_date) = YEAR(CURRENT_DATE())
                      GROUP BY month_num, month
                      ORDER BY month_num;";

$monthlySalesResult = $con->query($monthlySalesQuery);

// Initialize an array for all months with 0 sales by default
$monthlySales = [
    "January" => 0, "February" => 0, "March" => 0, "April" => 0, "May" => 0, "June" => 0,
    "July" => 0, "August" => 0, "September" => 0, "October" => 0, "November" => 0, "December" => 0
];

// Fill the array with actual sales data
while ($row = $monthlySalesResult->fetch_assoc()) {
    $monthlySales[$row['month']] = $row['earnings'] ?? 0;
}

// Fetch most ordered menu items
$mostOrderedMenuItemsQuery = "SELECT m.name AS menu_name, COUNT(*) AS menu_count 
                               FROM customer_package_menu cpm
                               JOIN menu m ON cpm.menu_id = m.id
                               GROUP BY cpm.menu_id
                               ORDER BY menu_count DESC
                               LIMIT 5;";
$mostOrderedMenuItemsResult = $con->query($mostOrderedMenuItemsQuery);
$mostOrderedMenuItems = [];
while ($row = $mostOrderedMenuItemsResult->fetch_assoc()) {
    $mostOrderedMenuItems[$row['menu_name']] = $row['menu_count'];
}

// Fetch most ordered packages
$mostOrderedPackagesQuery = "SELECT p.package_name, COUNT(*) AS reservation_count 
                              FROM customer_package_menu cpm
                              JOIN package p ON cpm.package_id = p.id
                              GROUP BY cpm.package_id
                              ORDER BY reservation_count DESC
                              LIMIT 3;";
$mostOrderedPackagesResult = $con->query($mostOrderedPackagesQuery);
$mostOrderedPackages = [];
while ($row = $mostOrderedPackagesResult->fetch_assoc()) {
    $mostOrderedPackages[$row['package_name']] = $row['reservation_count'];
}

// Generate the HTML content
$html = "
<style>
    .report-container {
        text-align: center;
        padding: 20px;
    }
    h2 {
        color: #2c3e50;
        font-weight: bold;
        margin-bottom: 5px;
    }
    h3 {
        color: #34495e;
        font-size: 18px;
        margin-bottom: 20px;
    }
    .divider {
        border-top: 2px solid #ddd;
        margin: 15px 0;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    th {
        background-color: #2c3e50;
        color: white;
        font-weight: bold;
    }
    .highlight {
        font-weight: bold;
        color: #d35400;
    }
</style>

<div class='report-container'>
    <h2>$cateringName</h2>
    <h3>Monthly Sales Report</h3>
    <div class='divider'></div>
    
    <table>
        <tr>
            <th>Month</th>
            <th>Sales (₱)</th>
        </tr>";

foreach ($monthlySales as $month => $sales) {
    $html .= "<tr>
                <td>$month</td>
                <td>₱" . number_format($sales, 2) . "</td>
              </tr>";
}

$html .= "</table>

    <h3>Most Reserved Menu Items</h3>
    <table>
        <tr>
            <th>Menu Item</th>
            <th>Orders</th>
        </tr>";

foreach ($mostOrderedMenuItems as $item => $orders) {
    $html .= "<tr>
                <td>$item</td>
                <td>$orders</td>
              </tr>";
}

$html .= "</table>

    <h3>Most Reserved Packages</h3>
    <table>
        <tr>
            <th>Package</th>
            <th>Orders</th>
        </tr>";

foreach ($mostOrderedPackages as $package => $orders) {
    $html .= "<tr>
                <td>$package</td>
                <td>$orders</td>
              </tr>";
}

$html .= "</table>
</div>
";

// Write content to PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('catering_report.pdf', 'D'); // 'D' forces download


exit;
?>
