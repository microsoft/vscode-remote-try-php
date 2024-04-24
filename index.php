<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin Sinh viên</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
            display: flex;
            justify-content: center;
            /* align-items: center; */
            min-height: 100vh;
            margin: 0;
        }
        .container {
            width: 80%;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: navy;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td a {
            text-decoration: none;
            color: blue;
            margin-right: 8px;
        }
        td a.delete-btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: red;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
        td a.delete-btn:hover {
            background-color: #dc3545;
        }
        td a.edit-btn {
            display: inline-block;
            padding: 8px 12px;
            background-color: #4CAF50;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 8px;
        }
        td a.edit-btn:hover {
            background-color: #45a049;
        }

        a.add-btn {
            display: block;
            width: 120px;
            padding: 8px 12px;
            background-color: #0056b3; /* Màu xanh của Bootstrap */
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px; /* Khoảng cách giữa nút và bảng */
            text-align: center;
        }
        a.add-btn:hover {
            background-color: #0056b3; /* Màu xanh lá cây nhạt khi di chuột vào */
        }
    </style>
</head>
<body>
    <script>
        function confirmDelete(MSSV) {
            if (confirm('Bạn có chắc chắn muốn xóa sinh viên này không?')) {
                // Người dùng đã xác nhận xóa
                window.location.href = 'delete.php?MSSV=' + MSSV + '&confirm=yes';
            }
        }
    </script>
    <script>
        function goToIndex() {
                window.location.href = 'index.php';
        }
    </script>

    <div class="container">
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

        $query = mysqli_query($con, "SELECT *  
                                     FROM tblsinhvien");

        // Kiểm tra số lượng bản ghi trả về
        $rowcount = mysqli_num_rows($query);

        if ($rowcount > 0) {
            echo "<h2>Danh sách Sinh viên</h2>";
            echo "<a href='add.php' class='add-btn'>New student</a>";
            echo "<table>";
            echo "<tr>
                <th>Image</th>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Action</th></tr>";

            // Lặp qua các hàng dữ liệu và hiển thị thông tin
            while ($row = mysqli_fetch_assoc($query)) {
                echo "<tr>";
                echo "<td>" . "</td>";
                echo "<td>" . $row['MSSV'] . "</td>";
                echo "<td>" . $row['HoTen'] . "</td>";
                echo "<td>" . $row['Email'] . "</td>";
                echo "<td>" . $row['DiaChi'] . "</td>";
                echo "<td>";
                echo "<a href='edit.php?MSSV=" . $row['MSSV'] . "' class='edit-btn'>Edit</a>";
                echo "<a href='#' class='delete-btn' onclick='confirmDelete(" . $row['MSSV'] . ")'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "<h2>Danh sách Sinh viên đăng ký môn học</h2>";
            echo "<a href='add.php' class='add-btn'>New student</a>";
            // echo "<p>Không có sinh viên nào trong cơ sở dữ liệu.</p>";
        }

        // Đóng kết nối
        mysqli_close($con);
        ?>
    </div>
</body>
</html>
