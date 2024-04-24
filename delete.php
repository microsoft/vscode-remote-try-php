<?php
// Kết nối đến cơ sở dữ liệu
$host = "localhost";
$user = "root";
$password = "ducanh12@#";
$database = "pka_s";
$con = mysqli_connect($host, $user, $password, $database);

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Kiểm tra xem có MSSV được gửi từ trang index.php không
if (isset($_GET['MSSV'])) {
    // Lấy MSSV (ID) của sinh viên cần xóa
    $MSSV = $_GET['MSSV'];

    // Xác nhận xóa sinh viên nếu được xác nhận từ JavaScript
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        // Chuẩn bị truy vấn DELETE
        $delete_query = "DELETE FROM tblsinhvien WHERE MSSV = '$MSSV'";

        // Thực thi truy vấn DELETE
        if (mysqli_query($con, $delete_query)) {
            echo "<script>alert('Sinh viên đã được xóa thành công.'); window.location = 'index.php';</script>";
        } else {
            echo "<script>alert('Lỗi khi xóa sinh viên: " . mysqli_error($con) . "');</script>";
        }        
    }
}
// Đóng kết nối
mysqli_close($con);
?>
