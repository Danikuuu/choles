<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../data-handling/db/connection.php';


if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == 0 || $_SESSION["role"] == 2) {
    die(json_encode(["error" => "Unauthorized access"]));
}

$query = "UPDATE reservations SET status = 'reviewed' WHERE status = 'pending' OR cancelled > 0";
$con->query($query);

?>
