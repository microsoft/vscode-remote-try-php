<?php
    include ('../../Models/database.php');
 
    // Kiểm tra xem nút Đăng nhập đã được bấm chưa
    if(isset($_POST['login_submit'])) {
        // Lấy dữ liệu từ form
        $email = $_POST['email'];
        $password = $_POST['password'];

        $check_password = md5($password); 
        $query = "SELECT * FROM tbllogin WHERE email='$email' AND password='$check_password'";
        $result = mysqli_query($con, $query);

        // Kiểm tra kết quả trả về
        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result); // Lấy dữ liệu từ kết quả truy vấn
            $_SESSION["IsLogin"] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['password'] = $password;
            $_SESSION['email'] = $email;    
            

            if($_SESSION['role'] == 'admin') {
                header("Location: ../../../resources/views/admin.blade.php");
                exit();
            } elseif ($_SESSION['role'] == 'user') {
                header("Location: ../../../resources/views/HelloWorld.blade.php");
                exit();
            } else {
                echo "<script>alert('Vai trò không hợp lệ!'); window.location.href = 'login.php';</script>";
            }
        } else {
            echo "<script>alert('Tên đăng nhập hoặc mật khẩu không đúng!'); window.location.href = 'login.php';</script>";
        }
    }


?>