<?php
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mydb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy dữ liệu từ form đăng ký
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

// Mã hóa mật khẩu
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Thêm dữ liệu vào cơ sở dữ liệu
$sql = "INSERT INTO tbllogin2 (username, pass, email) VALUES ('$username', '$hashed_password', '$email')";

if ($conn->query($sql) === TRUE) {
    // Đăng ký thành công, chuyển hướng về trang đăng nhập và hiển thị thông báo
    echo "<script>alert('Registered successfully'); window.location='login.html';</script>";

} else {
    // Nếu có lỗi, hiển thị thông báo lỗi
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>