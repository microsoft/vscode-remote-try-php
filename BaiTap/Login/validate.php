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
$password = $_POST['password'];

// Mã hóa mật khẩu bằng sha1()
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Xử lí thông tin đăng nhập
$sql = "SELECT * FROM tbllogin2 WHERE username='$username'";
$result = $conn->query($sql);
// $txt_error = "Username và password không hợp lệ";
if ($result->num_rows > 0) {  
    $row = $result->fetch_assoc();

    // So sánh mật khẩu đã mã hóa
    $hashed_password = $row['pass'];
    if (password_verify($password, $hashed_password)) {
        // Đăng nhập thành công, lưu trạng thái vào Session
        $_SESSION["Login"] = true;
        header("location: dashbord.php"); // Chuyển hướng đến trang dashboard sau khi đăng nhập thành công
    } else  {
        // Mật khẩu không đúng
        echo "<script>alert('Sai mật khẩu'); window.location='login.html?error=invalid_credentials';</script>"; 
    }
} else {
    // Tài khoản không tồn tại
    echo "<script>alert('Tài khoản không tồn tại'); window.location='login.html';</script>"; 

}


$conn->close();

?>
