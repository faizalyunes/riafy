<?php
$host = "localhost";
$user = "root";
$pass = "";
$database = "user_system";

$connection = mysqli_connect($host, $user, $pass, $database);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}