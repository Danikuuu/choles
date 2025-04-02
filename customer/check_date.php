<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1 || $_SESSION["role"] == 2) {
    header("Location: ../index.php");
    exit();
}

require_once '../data-handling/db/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["event_date"])) {
    $selectedDate = $_POST["event_date"];
    $maxCapacity = 150;

    // Fetch total people reserved for this date
    $query = "SELECT SUM(p.people_count) AS total_people_reserved
              FROM reservations r
              JOIN customer_package_menu cpm ON cpm.id = r.customer_package_id 
              JOIN package p ON cpm.package_id = p.id
              WHERE r.event_date = ? AND r.status = 'pending'
              GROUP BY r.event_date";

    $stmt = $con->prepare($query);
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalPeopleReserved = $row ? $row['total_people_reserved'] : 0;

    // Check if the reservation exceeds the limit
    if ($totalPeopleReserved >= $maxCapacity) {
        echo json_encode(["error" => "Reservation limit exceeded for this date. Please choose another date."]);
    } else {
        echo json_encode([
            "selected_date" => $selectedDate,
            "total_people_reserved" => $totalPeopleReserved
        ]);
    }
}

?>

