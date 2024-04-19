<html>
    <body>
        <?php
            $sTenDangNhap = $_REQUEST["txtTenDangNhap"];
            $sMatKhau = $_REQUEST["txtMatKhau"];
        ?>

        <h1 style="color: blue;">Kết quả đăng nhập</h1>
        <span>Tên đăng nhập là: </span><?php echo '<span style="color: red; font-weight: bold">' . $sTenDangNhap . '</span> '; ?><br/>
        <span>Mật khẩu: </span><?php echo '<span style="color: red; font-weight: bold; margin-left: 50px">' . $sMatKhau . '</span> '; ?>
    </body>
</html>