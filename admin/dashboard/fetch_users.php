<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../data-handling/db/connection.php';


if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    die(json_encode(["error" => "Unauthorized access"]));
}

header('Content-Type: application/json'); // Ensure JSON output


$user_id = $_SESSION['user_id'];

$sql = "SELECT id, fname FROM user WHERE id != ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
?>
