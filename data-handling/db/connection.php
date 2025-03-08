<?php

error_reporting(0);

$host = "localhost";

$username = "root";

$password ="";

$db = "choles_db";

$con = mysqli_connect($host,$username,$password,$db);



// Check connection

if (!$con) {

    die("Connection failed: " . mysqli_connect_error());

}


?>