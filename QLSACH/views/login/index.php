<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập</title>
</head>
<body>
    <h2>Đăng nhập</h2>
    <form action="../controllers/LoginController.php?action=login" method="post">
        <label for="username">Tên người dùng:</label>
        <input type="text" id="username" name="username"><br><br>
        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" value="Đăng nhập">
    </form>
</body>
</html>
