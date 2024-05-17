<?php
session_start();

// Kết nối CSDL (sử dụng file connect.php)
require_once('includes/db_config.php');

// Xử lý form đăng ký
if (isset($_POST['register'])) {
    // Lấy dữ liệu từ form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password) || empty($email)) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Địa chỉ email không hợp lệ.";
    } else {
        // Kiểm tra username đã tồn tại hay chưa
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            $error = "Tên đăng nhập đã tồn tại.";
        } else {
            // Mã hóa mật khẩu bằng SHA1
            $hashed_password = sha1($password);

            // Lưu thông tin người dùng vào CSDL
            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";
            mysqli_query($conn, $sql);

            if (mysqli_affected_rows($conn) > 0) {
                $success = "Đăng ký thành công!";
            } else {
                $error = "Lỗi khi đăng ký.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Đăng ký</h1>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

    <form action="register.php" method="post">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit" name="register">Đăng ký</button>
    </form>
</body>
</html>
