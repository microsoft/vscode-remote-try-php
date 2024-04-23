<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php

echo "hello";
$host = "localhost";
$user ="root";
$password="ducanh12@#";
$con=mysqli_connect($host,$user,$password);

mysqli_select_db($con,"sinhvien"); 
session_start();
$query=mysqli_query($con,"select * from tblsinhvien"); 

//$row = mysqli_fetch_array($query);

$rowcount=mysqli_num_rows($query);
//made query after establishing connection with database.

//echo "my result <a href='data/" . htmlentities($row['classtype'], ENT_QUOTES, 'UTF-8') . ".php'>sinh vien</a>";

printf($rowcount);

?>
</body>
</html> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin Sinh viên</title>
</head>
<body>
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

    $query = mysqli_query($con,"SELECT tblsinhvien.MSSV, tblsinhvien.HoTen, tbldangKy.Ky, tbldangky.ChiTietTT 
                                FROM tblsinhvien
                                JOIN tbldangky ON tblsinhvien.MSSV = tbldangky.MSSV;
                                ");

    // Kiểm tra số lượng bản ghi trả về
    $rowcount = mysqli_num_rows($query);

    if ($rowcount > 0) {
        echo "<h2>Danh sách Sinh viên đăng ký môn học</h2>";
        echo "<table border='1'>";
        echo "<tr><th>MSSV</th><th>Họ và tên</th><th>Kỳ</th><th>Đăng ký</th></tr>";

        // Lặp qua các hàng dữ liệu và hiển thị thông tin
        while ($row = mysqli_fetch_assoc($query)) {
            echo "<tr>";
            echo "<td>" . $row['MSSV'] . "</td>";
            echo "<td>" . $row['HoTen'] . "</td>";
            echo "<td>" . $row['Ky'] . "</td>";
            echo "<td>" . $row['ChiTietTT'] . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "Không có sinh viên nào trong cơ sở dữ liệu.";
    }

    // Đóng kết nối
    mysqli_close($con);
    ?>
</body>
</html>
