
<?php
function ann() {
        echo "<script>
                if (confirm('Tài khoản không tồn tại. Quay lại đăng nhập?')) {
                    window.location.href = 'form_login.php';
                } else {
                    // Do something else or simply close the dialog
                }
              </script>";
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('config.php');

    $host = "localhost";
    $user = "root";
    $password = "ducanh12@#";
    $dbname = "sinhvien";

    $conn = new mysqli($host, $user, $password, $dbname);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối đến cơ sở dữ liệu thất bại: " . $conn->connect_error);
    }

    // Lấy dữ liệu từ biểu mẫu đăng nhập
    $TK = $_POST['txtTenTaiKhoan'];
    $MK = $_POST['txtMatKhau'];

    // Truy vấn để lấy thông tin tài khoản từ cơ sở dữ liệu
    $sql = "SELECT * FROM tblusers WHERE TK = '$TK'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Tài khoản tồn tại trong cơ sở dữ liệu
        $row = $result->fetch_assoc();
        $hashed_password = $row['MK'];

        // So sánh mật khẩu đã nhập với mật khẩu trong cơ sở dữ liệu
        if (sha1($MK) === $hashed_password) {
            // Mật khẩu khớp, đăng nhập thành công
            echo "<h1 style='color: blue;'>Kết quả đăng nhập</h1>";
            echo "Tài khoản: <span style='color: red;'>$TK</span><br>";
            echo "Mật khẩu: <span style='color: red;'>$MK</span><br>";
            echo "Đăng nhập thành công!";
            echo "<br>";
            echo "<a href='form_login.php'>Quay lại</a>"; 
        } else {
            // Mật khẩu không khớp
            echo "<h1 style='color: red;'>Lỗi!</h1>";
            echo "Mật khẩu không chính xác!";
            echo "<br>";
            echo "<a href='form_login.php'>Quay lại</a>"; 

        }

    } else {
        // Tài khoản không tồn tại trong cơ sở dữ liệu
        // echo "<h1 style='color: red;'>Lỗi!</h1>";
        // echo "Tài khoản không tồn tại!";
        // echo "<br>";
        // echo "<a href='form_login.php'>Quay lại</a>"; 
        ann();
    }

    $conn->close();
}
?>
