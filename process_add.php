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

// Lấy dữ liệu từ biểu mẫu 'add.php' sử dụng phương thức POST
$MSSV = mysqli_real_escape_string($con, $_POST['MSSV']);
$HoTen = mysqli_real_escape_string($con, $_POST['HoTen']);
$Email = mysqli_real_escape_string($con, $_POST['Email']);
$Diachi = mysqli_real_escape_string($con, $_POST['Diachi']);


$check_query = "SELECT * FROM tblsinhvien WHERE MSSV = '$MSSV'";
$result = mysqli_query($con, $check_query);

if (mysqli_num_rows($result) > 0) {
    // Nếu tồn tại bản ghi với mã sinh viên đã nhập
    echo "<script>alert('Mã sinh viên đã tồn tại. Vui lòng nhập mã sinh viên khác.');</script>";
    echo "<script>history.back();</script>"; 
} else {
    $insert_query = "INSERT INTO tblsinhvien (MSSV, HoTen, Email, Diachi) VALUES ('$MSSV', '$HoTen', '$Email', '$Diachi')";
    if (mysqli_query($con, $insert_query)) {
        echo "<script>alert('Sinh viên đã được thêm thành công.'); window.location = 'index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// Đóng kết nối MySQL
mysqli_close($con);
?>
