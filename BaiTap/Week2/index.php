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
      <th>Mssv</th>
      <th>Họ và Tên</th>
      <th>Kỳ</th>
      <th>Đăng Ký</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Kết nối với cơ sở dữ liệu SQLite
    $db = new SQLite3('C:\xampp\htdocs\WorkSpace6\vscode-remote-try-php\BaiTap\Week2\database.db');

    // Truy vấn dữ liệu từ cơ sở dữ liệu
    $query = $db->query('SELECT * FROM tblSinhVien');
    while ($row = $query->fetchArray()) {
        echo '<tr>';
        echo '<td>'.$row['Mssv'].'</td>';
        echo '<td>'.$row['HoTen'].'</td>';
        echo '<td>'.$row['Ky'].'</td>';
        echo '<td>'.$row['DangKy'].'</td>';
        echo '</tr>';
    }

    // Đóng kết nối với cơ sở dữ liệu
    $db->close();
    ?>
  </tbody>
</table>

</body>
</html>