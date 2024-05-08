<?php
    session_start();
    $_SESSION["IsLogin"] = false;

    include('./db_connect.php');
    ob_start();
    ob_end_flush();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $db->query($query);

    if ($result->num_rows > 0) {
        $_SESSION["IsLogin"] = true;
        header("Location: welcome.htm");
    } else {
        header("Location: login.htm");
    }
?>
