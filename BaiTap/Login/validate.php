<?php
session_start();
ob_start();
// Kết nối đến CSDL
$servername = "localhost";
$username = "root"; // Thay bằng username của bạn
$password = ""; // Thay bằng password của bạn
$dbname = "mydb"; // Thay bằng tên CSDL của bạn

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lấy thông tin từ form đăng nhập
$username = $_POST['username'];
$password = $_POST['password']; // Mã hóa mật khẩu bằng sha1()

// Xử lí thông tin đăng nhập
$sql = "SELECT * FROM tbllogin2 WHERE username='$username'";
$result = $conn->query($sql);
$txt_error = "Username và password không hợp lệ";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // So sánh mật khẩu đã mã hóa
    if ($row['pass'] == $password) {
        // Đăng nhập thành công, lưu trạng thái vào Session
        $_SESSION["Login"] = true;
        header("location: dashbord.php"); // Chuyển hướng đến trang dashboard sau khi đăng nhập thành công
    } else {
        // Mật khẩu không đúng
        header("Location: login.html?error=invalid_credentials");
        if(isset($txt_error) && ($txt_error != "")) {
            echo $txt_error;
        }
        
    }
} else {
    // Tài khoản không tồn tại
    echo $txt_error;
}

$conn->close();

?>
