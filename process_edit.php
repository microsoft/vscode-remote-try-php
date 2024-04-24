<?php
$host = "localhost";
$user = "root";
$password = "ducanh12@#";
$database = "pka_s";
$con = mysqli_connect($host, $user, $password, $database);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $MSSV = $_POST['MSSV'];
    $HoTen = $_POST['HoTen'];
    $Email = $_POST['Email'];
    $DiaChi = $_POST['DiaChi'];

    // Chuẩn bị truy vấn UPDATE để cập nhật thông tin sinh viên
    $update_query = "UPDATE tblsinhvien SET HoTen = '$HoTen', Email = '$Email', DiaChi = '$DiaChi' WHERE MSSV = '$MSSV'";

    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Thông tin sinh viên đã được cập nhật thành công.'); window.location = 'index.php';</script>";
    } else {
        echo "<script>alert('Lỗi khi cập nhật thông tin sinh viên: " . mysqli_error($con) . "');</script>";
    }
}

mysqli_close($con);
?>
