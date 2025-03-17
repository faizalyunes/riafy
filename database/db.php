<?php
$host = "sql12.freesqldatabase.com";
$user = "sql12767771";
$pass = "Fcw9NmDp1A";
$database = "sql12767771";

$connection = mysqli_connect($host, $user, $pass, $database);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}