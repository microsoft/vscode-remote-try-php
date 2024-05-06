<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sinh Viên</title>
    <style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
    </style>
</head>

<body>
    <h3>Danh sách sinh viên đăng ký môn học</h3>
    <table>
        <thead>
            <tr>
                <th>MSSV</th>
                <th>Họ và tên</th>
                <th>Môn học</th>
                <th>Kỳ học</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $host = "localhost";
                $user = "root";
                $password = "123456";
                $con = mysqli_connect($host, $user, $password);

                mysqli_select_db($con, "sinhvien");
                session_start();
                $query = mysqli_query($con, "SELECT sv.MSSV, sv.HoTen, mh.TenMH, dk.Ky
                                                FROM tblsinhvien AS sv
                                                JOIN tbldangky AS dk ON sv.MSSV = dk.MSSV
                                                JOIN tblmonhoc AS mh ON mh.MaMH = dk.MaMH;");
                while ($row = mysqli_fetch_array($query)) {
                    echo "<tr>";
                    echo "<td>".$row['MSSV']."</td>";
                    echo "<td>".$row['HoTen']."</td>";
                    echo "<td>".$row['TenMH']."</td>";
                    echo "<td>".$row['Ky']."</td>";
                    echo "</tr>";
                }
                mysqli_close($con);
            ?>
        </tbody>
    </table>
</body>

</html>