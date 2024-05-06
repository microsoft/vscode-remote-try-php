<?php
include "connect.php"; 

session_start();
if ($_SESSION["IsLogin"] == false) {
    header("Location: login.htm");
    exit(); // Dừng thực thi mã hiện tại sau khi chuyển hướng
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($connection, $sql);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION["IsLogin"] = true; 
        header("Location: dashboard.php"); 
    } else {
        header("Location: login.html"); 
    }
}
?>

