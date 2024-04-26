<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
</head>
<body>
    <h1 style="color: blue;">Đăng nhập hệ thống</h1>

    <form action="result_login.php" method="GET">
        <label for="txtTenTaiKhoan">Nhập tài khoản:</label>
        <input type="text" id="txtTenTaiKhoan" name="txtTenTaiKhoan">
        <br>
        <br>
        <label for="txtMatKhau" style="font-weight: bold">Nhập mật khẩu:</label>
        <input type="password" id="txtMatKhau" name="txtMatKhau">
        <br>
        <br>
        <input type="submit" value="Đăng nhập">
    </form>
</body>
</html>