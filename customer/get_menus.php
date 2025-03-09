<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 1) {
    header("Location: ../index.php");
    exit();
}
require_once '../data-handling/db/connection.php';

$query = "SELECT id, menu_name FROM menu WHERE status = 0";
$result = $con->query($query);

$options = "";
while ($row = $result->fetch_assoc()) {
    $options .= "<option value='{$row['id']}'>{$row['menu_name']}</option>";
}

echo $options;
?>
