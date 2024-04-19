<html>
    <body>
        <?php
            $username = $_REQUEST["username"];
            $password = $_REQUEST["password"];
        ?>
        <h1>Kết quả đăng nhập</h1>
        Tên đăng nhập là : <? echo'<span style="color:red;font-weight:bold">' . $username . '</span>'?>
        <br>
        Mật khẩu là : <? echo'<span style="color:red;font-weight:bold">' . $password . '</span>'?>
        
    </body>
</html>