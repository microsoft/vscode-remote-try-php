<?php

$dbhost = "localhost:3307";
$dbuser = "username";
$dbpass = "password";
$dbname = "database_name";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}
?>
