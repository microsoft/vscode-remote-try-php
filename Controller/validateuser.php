<?php
session_start();
// Kết nối CSDL (thay đổi thông tin kết nối của bạn ở đây)
$conn = mysqli_connect("localhost", "root", "123456", "login");

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối không thành công: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Truy vấn kiểm tra thông tin đăng nhập
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        // Đăng nhập thành công, đặt session và redirect
        $_SESSION["IsLogin"] = true;
        header("Location: logout.php");
    } else {
        // Đăng nhập thất bại, redirect về trang login
        header("Location: ../View/login.html");
    }
}
?>