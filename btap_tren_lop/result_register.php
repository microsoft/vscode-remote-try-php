<?php
    require_once('config.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Xử lý đăng ký
        $host = "localhost";
        $user = "root";
        $password = "ducanh12@#";
        $dbname = "sinhvien";

        $conn = new mysqli($host, $user, $password, $dbname);

        // Kiểm tra kết nối
        if ($conn->connect_error) {
            die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
        }

        // Lấy dữ liệu từ biểu mẫu đăng ký
        $MSSV = $_POST['txtMSSV'];
        $HoTen = $_POST['txtHoTen'];
        $Lop = $_POST['txtLop'];
        $Khoa = $_POST['txtKhoa'];
        $SDT = $_POST['txtSDT'];
        $TK = $_POST['txtTK'];
        $MatKhau = sha1($_POST['txtMatKhau']);

        // Kiểm tra xem MSSV đã tồn tại chưa
        $sql_check = "SELECT * FROM tblsinhvien WHERE MSSV = '$MSSV'";
        $result_check = $conn->query($sql_check);
        if ($result_check->num_rows > 0) {
            echo "Lỗi: MSSV đã tồn tại!";
            echo "<br>";
            echo "<a href='form_register.php'>Quay lại</a>";
            exit();
        }

        // Kiểm tra xem tên tài khoản đã tồn tại chưa
        $sql_check_user = "SELECT * FROM tblusers WHERE TK = '$TK'";
        $result_check_user = $conn->query($sql_check_user);
        if ($result_check_user->num_rows > 0) {
            echo "Lỗi: Tên tài khoản đã tồn tại!";
            echo "<br>";
            echo "<a href='form_register.php'>Quay lại</a>";
            exit();
        }

        $sql = "INSERT INTO tblusers (MSSV, TK, MK) VALUES ('$MSSV', '$TK', '$MatKhau')";
        $sql2 = "INSERT INTO tblsinhvien (MSSV, HoTen, Lop, Khoa, SDT) VALUES ('$MSSV', '$HoTen', '$Lop', '$Khoa', '$SDT')";

        if ($conn->query($sql) === TRUE && $conn->query($sql2) === TRUE) {
            echo "Đăng ký thành công!";
            echo "<br>";
            echo "<a href='form_login.php'>Đăng nhập</a>"; 
            echo "           ";
            echo "<a href='form_register.php'>Quay lại</a>";
        } else {
            echo "Lỗi: " . $sql . "<br>" . $conn->error;
            echo "<br>";
            echo "<a href='form_register.php'>Quay lại</a>";
        }

        $conn->close();
    }
?>
