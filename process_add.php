<?php
// Lấy thông tin kết nối đến MySQL từ biến POST
$host = "localhost";
$user = "root";
$password = "ducanh12@#";
$database = "pka_s";

// Kết nối đến cơ sở dữ liệu MySQL
$con = mysqli_connect($host, $user, $password, $database);

// Kiểm tra kết nối
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Lấy dữ liệu từ biểu mẫu 'add.php' sử dụng phương thức POST
$MSSV = mysqli_real_escape_string($con, $_POST['MSSV']);
$HoTen = mysqli_real_escape_string($con, $_POST['HoTen']);
$Email = mysqli_real_escape_string($con, $_POST['Email']);
$Diachi = mysqli_real_escape_string($con, $_POST['Diachi']);

// Chuẩn bị truy vấn INSERT để thêm sinh viên vào bảng tblsinhvien
$query = "INSERT INTO tblsinhvien (MSSV, HoTen, Email, Diachi) VALUES ('$MSSV', '$HoTen', '$Email', '$Diachi')";

// Thực thi truy vấn INSERT
if (mysqli_query($con, $query)) {
    // echo "New student added successfully!";
    echo "<script>alert('Sinh viên đã được thêm thành công.'); window.location = 'index.php';</script>";
} else {
    echo "Error: " . mysqli_error($con);
}

// Đóng kết nối MySQL
mysqli_close($con);
?>
