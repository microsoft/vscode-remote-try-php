<?php
    include ('../../Models/database.php');

    if (!isset($_SESSION["IsLogin"])) {
        $_SESSION["IsLogin"] = false;
    }

    if(isset($_POST['register_submit'])) {
        $new_username = $_POST['new-username'];
        $new_password = $_POST['new-password'];
        $new_email = $_POST['new-email'];
        $confirm_password = $_POST['confirm-password'];

        // Kiểm tra xem mật khẩu và xác nhận mật khẩu có khớp nhau không
        if($new_password != $confirm_password) {
            echo "<script>alert('Mật khẩu và xác nhận mật khẩu không khớp!'); window.location.href = 'register.php';</script>";
            exit(); // Thoát khỏi script nếu mật khẩu không khớp
        }

        // Kiểm tra xem tên người dùng đã tồn tại chưa
        $check_query = "SELECT * FROM tbllogin WHERE username='$new_username'";
        $check_result = mysqli_query($con, $check_query);

        if(mysqli_num_rows($check_result) > 0) {
            echo "<script>alert('Tên đăng nhập đã tồn tại!'); window.location.href = 'register.php';</script>";
            exit(); // Thoát khỏi script nếu tên đăng nhập đã tồn tại
        }
        
        $hashed_password = md5($new_password);

        // Nếu không có vấn đề gì, thực hiện thêm người dùng vào cơ sở dữ liệu
        $insert_query = "INSERT INTO tbllogin (username,email,password) VALUES ('$new_username','$new_email','$hashed_password')";
        mysqli_query($con, $insert_query);

        echo "<script>alert('Đăng ký thành công!'); window.location.href = '../../../resources/views/login.blade.php';</script>";
    }
?>
