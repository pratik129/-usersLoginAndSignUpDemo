<?php

$hostname = "localhost";
$username = "root";
$passowrd = "";
$database = "support_db";
$con = mysqli_connect($hostname, $username, $passowrd, $database);

if (!$con) {
    die("Connection failed".mysqli_connect_error());
}