<?php
    session_start();
    // Xóa tất cả các biến session
    session_unset();
    // Hủy phiên session
    session_destroy();
    // Xóa cookie phiên nếu có
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }
    // Chuyển hướng người dùng đến trang đăng nhập
    header("Location: ../View/login.html");
    exit(); // Kết thúc quá trình xử lý
?>
