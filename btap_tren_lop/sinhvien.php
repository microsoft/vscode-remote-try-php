<?php
require_once('config.php');
$host = "localhost";
$user ="root";
$password = "ducanh12@#";
$con=mysqli_connect($host,$user,$password);

// Kiểm tra kết nối
if (!$con) {
    die("Kết nối không thành công: " . mysqli_connect_error());
}

mysqli_select_db($con,"sinhvien");

$query=mysqli_query($con,"SELECT tblsinhvien.MSSV,tblsinhvien.HoTen,tblmonhoc.MaMH,tblmonhoc.TenMH,tbldangky.Ky
FROM tblsinhvien
JOIN tbldangky ON tblsinhvien.MSSV = tbldangky.MSSV
JOIN tblmonhoc ON tbldangky.MaMH = tblmonhoc.MaMH;
"); 

// Kiểm tra lỗi truy vấn
if (!$query) {
    die("Lỗi truy vấn: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Result</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
            padding: 5px;
        }
    </style>
</head>
<body>

<h2>Result</h2>

<table>
    <tr>
        <th>MSSV</th>
        <th>Họ và tên</th>
        <th>Mã môn học</th>
        <th>Tên môn học</th>
        <th>Kỳ</th>
        <!-- Thêm các cột khác nếu cần -->
    </tr>
    <?php
    while ($row = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?php echo $row['MSSV']; ?></td>
            <td><?php echo $row['HoTen']; ?></td>
            <td><?php echo $row['MaMH']; ?></td>
            <td><?php echo $row['TenMH']; ?></td>
            <td><?php echo $row['Ky']; ?></td>
            <!-- Thêm các cột khác nếu cần -->
        </tr>
        <?php
    }
    ?>
</table>

</body>
</html>

<?php mysqli_close($con); ?>
