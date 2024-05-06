<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydatabase";

// Tạo kết nối
$connection = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($connection->connect_error) {
    die("Không thể kết nối đến CSDL: " . $connection->connect_error);
}
?>