<?php
// Kiểm tra xem có thông báo thành công từ trang result_register.php không
if(isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<div style='text-align: center; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 5px; padding: 10px; margin-bottom: 20px;'>Đăng ký thành công. Đăng nhập vào hệ thống.</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
            color: #007bff;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
            margin-bottom: 15px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Đăng nhập hệ thống</h1>

    <form action="result_login.php" method="POST">
        <label for="txtTenTaiKhoan">Nhập tài khoản:</label>
        <input type="text" id="txtTenTaiKhoan" name="txtTenTaiKhoan">
        <br>
        <br>
        <label for="txtMatKhau">Nhập mật khẩu:</label>
        <input type="password" id="txtMatKhau" name="txtMatKhau">
        <br>
        <br>
        <input type="submit" value="Đăng nhập">
        <input type="submit" value="Đăng ký">
    </form>
</body>
</html>
