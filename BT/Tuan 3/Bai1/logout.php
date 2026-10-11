<?php
session_start(); // Khởi tạo session

// Xóa tất cả biến session
session_unset(); 

// Hủy session
session_destroy();

// Thông báo đăng xuất thành công
echo "Bạn đã đăng xuất thành công!";

// Chuyển hướng về trang chủ
header("Location: login.html"); 
exit();
?>
