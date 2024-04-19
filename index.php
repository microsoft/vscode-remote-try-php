<?php
// Xử lý biểu mẫu tìm kiếm khi được gửi đi
    if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["txtTenTaiKhoan"]) && isset($_GET["txtMatKhau"])) {
        $username = $_GET["txtTenTaiKhoan"];
        $password = $_GET["txtMatKhau"];

        if ($username === "admin" && $password === "12345") {
            $message = "Welcome, admin";
            $messageStyle = 'style="font-family: Tahoma; color: red;"';
        } else {
            $message = "Tên tài khoản hoặc Mật khẩu sai. Vui lòng nhập lại!";
            $messageStyle = '';
        }
        $authenticated = true;
    } else {
        $authenticated = false;
    }
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Đăng nhập</title>
    </head>
<body>
    <h1>ĐĂNG NHẬP HỆ THỐNG</h1>
    <?php if ($authenticated) { ?>
        <p <?= $messageStyle ?>><?= $message ?></p>
        <?php } else { ?>
        <form action="index.php" method="GET">
        <label for="txtTenTaiKhoan">Tài khoản:</label>
            <input type="text" id="txtTenTaiKhoan" name="txtTenTaiKhoan">
            <br>
            <br>
            <label for="txtMatKhau">Mật khẩu:</label>
            <input type="password" id="txtMatKhau" name="txtMatKhau">
            <br>
            <br>
            <input type="submit" value="Đăng nhập">
        </form>
    <?php } ?>
</body>
</html>