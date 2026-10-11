<?php
session_start(); // Khởi tạo session

// Kết nối CSDL (thay đổi thông tin kết nối phù hợp với CSDL của bạn)
$dbhost = "localhost:3307";
$dbuser = "root";
$dbpass = "";
$dbname = "database_name";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Lấy thông tin đăng nhập từ form
$username = $_POST["username"];
$password = $_POST["password"];

// Mã hóa mật khẩu (bằng md5 hoặc thuật toán băm khác)
$hashed_password = md5($password);

// Viết truy vấn SQL để kiểm tra thông tin đăng nhập
$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$hashed_password'";
$result = mysqli_query($conn, $sql);

// Kiểm tra số lượng kết quả
if (mysqli_num_rows($result) > 0) {
    // Đăng nhập thành công
    $_SESSION["IsLogin"] = true; // Lưu trạng thái đăng nhập
    header("Location: welcome.php"); // Chuyển hướng đến trang chào mừng
    exit();
} else {
    // Đăng nhập thất bại
    echo "Tên người dùng hoặc mật khẩu không hợp lệ!";
    header("Location: login.html"); // Chuyển hướng về trang login
}

// Đóng kết nối CSDL
mysqli_close($conn);