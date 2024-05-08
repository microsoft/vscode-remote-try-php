<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bảng Sinh Viên</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<table class="student-table">
  <thead>
    <tr>
      <th>MSSV</th>
      <th>Họ và Tên</th>
      <th>Kỳ</th>
      <th>Đăng Ký</th>
    </tr>
  </thead>
  <tbody>
  <?php
      // Thông tin kết nối MySQL
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "myDB"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối không thành công: " . $conn->connect_error);
}

// Truy vấn dữ liệu từ bảng sinh viên
$sql = "SELECT * FROM sinhvien"; 

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Hiển thị dữ liệu
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["mssv"]. "</td><td>" . $row["hoten"]. "</td><td>" . $row["ky"]. "</td><td>" . $row["dangky"]. "</td></tr>";
    }
} else {
    echo "0 kết quả";
}

// Đóng kết nối
$conn->close();
      ?>
  </tbody>
</table>

</body>
</html>