<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả đăng nhập</title>
</head>
<body>
    <?php
        $sUser = $_REQUEST["txtTenTaiKhoan"];
        $sPass = $_REQUEST['txtMatKhau'];
    ?>
    <h1 style="color: blue;">Kết quả đăng nhập</h1>
    <span style="font-weight: bold;">Tài khoản: </span> <span style="color: red; font-weight:bold"><?php echo $sUser?></span>
    <br /><br>
    <span style="font-weight: bold;">Mật khẩu: </span> <span style="color: red; font-weight:bold"><?php echo $sPass?></span>
</body>
</html>