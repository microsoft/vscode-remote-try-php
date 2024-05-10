<?php
// Kết nối đến cơ sở dữ liệu
$servername = "";
$username = "";
$password = "";
$dbname = "";
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
}

// Gọi controller và hiển thị trang Sach
require_once 'controllers/SachController.php';
$controller = new SachController($conn);
$controller->index();

// Đóng kết nối
$conn->close();
?>
