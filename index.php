<?php
// Xử lý biểu mẫu tìm kiếm khi được gửi đi
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["txtTenTaiKhoan"]) && isset($_GET["txtMatKhau"])) {
    $username = $_GET["txtTenTaiKhoan"];
    $password = $_GET["txtMatKhau"];

    // Kiểm tra thông tin đăng nhập
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
</head>
<body>
    <h1>ĐĂNG NHẬP</h1>

    <?php if ($authenticated) { ?>
        <p <?= $messageStyle ?>><?= $message ?></p>
    <?php } else { ?>
        <form action="index.php" method="GET">
            <label for="txtTenTaiKhoan">Nhập tài khoản:</label>
            <input type="text" id="txtTenTaiKhoan" name="txtTenTaiKhoan">
            <br>
            <br>
            <label for="txtMatKhau">Nhập mật khẩu:</label>
            <input type="password" id="txtMatKhau" name="txtMatKhau">
            <br>
            <br>
            <input type="submit" value="Đăng nhập">
        </form>
    <?php } ?>
</body>
</html>
