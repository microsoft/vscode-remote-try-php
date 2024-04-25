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
    Tài khoản : <span style="color: red;"><?php echo $sUser?></span>
    <br /><br>
    Mật khẩu : <span style="color: red;"><?php echo $sPass?></span>
</body>
</html>