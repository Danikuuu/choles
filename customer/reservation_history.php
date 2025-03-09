<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1) {
    header("Location: ../index.php");
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../data-handling/db/connection.php';

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}
echo "Database connected successfully.<br>";

$sql = "SELECT 
    cpm.id AS customer_package_id,
    cpm.customer_id,
    r.event_date,
    p.package_name,
    m.name AS menu_name,
    m.description AS menu_description,
    m.category AS menu_category,
    m.image AS menu_image
FROM customer_package_menu cpm
JOIN reservations r ON cpm.id = r.customer_package_id 
JOIN package p ON cpm.package_id = p.id
JOIN menu m ON cpm.menu_id = m.id
ORDER BY r.event_date DESC;
";

$result = $con->query($sql);

if (!$result) {
    die("Query failed: " . $con->error);
} else {
    echo "Query executed successfully.<br>";
}

if ($result->num_rows === 0) {
    echo "No records found.";
} else {
    echo "<table border='1'>
            <tr>
                <th>Customer Package ID</th>
                <th>Customer ID</th>
                <th>Event Date</th>
                <th>Package Name</th>
                <th>Menu Name</th>
                <th>Description</th>
                <th>Category</th>
                <th>Image</th>
            </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['customer_package_id']}</td>
                <td>{$row['customer_id']}</td>
                <td>{$row['event_date']}</td>
                <td>{$row['package_name']}</td>
                <td>{$row['menu_name']}</td>
                <td>{$row['menu_description']}</td>
                <td>{$row['menu_category']}</td>
                <td><img src='{$row['menu_image']}' width='100' height='100'></td>
              </tr>";
    }
    echo "</table>";
}

$con->close();
?>


<!-- change the ui -->
 <!-- also add a modal that will display the details of the history and also put an input that will let the user upload an image to verify the downpayment -->
