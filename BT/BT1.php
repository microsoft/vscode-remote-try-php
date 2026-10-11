<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dữ liệu sinh viên đăng ký môn học</title>
</head>
<body>
    <h1>Dữ liệu sinh viên đăng ký môn học</h1>
    <?php
    // Kết nối đến cơ sở dữ liệu
    $servername = "localhost"; // Tên máy chủ MySQL
    $username = "username"; // Tên người dùng MySQL
    $password = "password"; // Mật khẩu MySQL
    $dbname = "PKA_S"; // Tên cơ sở dữ liệu MySQL

    // Tạo kết nối
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Truy vấn dữ liệu từ bảng SinhVien và DangKy
    $sql = "SELECT SinhVien.MSSV, SinhVien.HoTen, MonHoc.MaMH, MonHoc.TenMH, DangKy.Ky
            FROM SinhVien
            INNER JOIN DangKy ON SinhVien.MSSV = DangKy.MSSV
            INNER JOIN MonHoc ON DangKy.MaMH = MonHoc.MaMH";
    $result = $conn->query($sql);

    // Kiểm tra và hiển thị dữ liệu
    if ($result->num_rows > 0) {
        // Duyệt qua từng hàng kết quả
        while($row = $result->fetch_assoc()) {
            echo "MSSV: " . $row["MSSV"]. " - Họ và tên: " . $row["HoTen"]. " - Mã môn học: " . $row["MaMH"]. " - Tên môn học: " . $row["TenMH"]. " - Kỳ: " . $row["Ky"]. "<br>";
        }
    } else {
        echo "Không có dữ liệu";
    }
    $conn->close();
    ?>
</body>
</html>