<?php
    session_start();
    $_SESSION["IsLogin"] = false;
    
    include('./db_connect.php');
    ob_start();
    ob_end_flush();
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        $_SESSION["IsLogin"] = true;
        header("Location: home.php");
    } else {
        header("Location: login.php?error=Tên đăng nhập hoặc mật khẩu không đúng. Vui lòng thử lại.");
    }
?>
