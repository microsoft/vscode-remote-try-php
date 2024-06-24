<!DOCTYPE html>
<html>
<head>
    <title>Trang Dashboard</title>
</head>
<body>
    <h1>Trang Dashboard</h1>

    <?php
    session_start();

    // Kiểm tra trạng thái đăng nhập
    if ($_SESSION["IsLogin"] == true) {
        // Hiển thị thông tin người dùng
        echo "<p>Xin chào</p>";

        // Hiển thị nút đăng xuất
        echo '<form action="logout.php" method="post">';
        echo '<input type="submit" value="Đăng xuất">';
        echo '</form>';
    } else {
        // Chuyển hướng về trang đăng nhập nếu chưa đăng nhập
        header("Location: login.htm");
        exit();
    }
    ?>
</body>
</html>