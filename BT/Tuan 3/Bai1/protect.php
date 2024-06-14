<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION["IsLogin"]) || $_SESSION["IsLogin"] == false) {
    header("Location: login.html");
    exit();
}

// Mã trang của bạn...

// Sử dụng hàm băm mật khẩu mạnh
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Thêm mã token CSRF
$token = bin2hex(random_bytes(32));
echo '<input type="hidden" name="token" value="' . $token . '">';

// Xử lý form
if (isset($_POST["token"]) && $_POST["token"] === $token) {
    // Xử lý form an toàn
} else {
    // Lỗi mã token CSRF
    echo "Mã token CSRF không hợp lệ!";
}
