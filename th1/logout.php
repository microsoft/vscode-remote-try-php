<?php
session_start();
if ($_SESSION["IsLogin"] == false) {
    header("Location: login.htm");
    exit(); // Dừng thực thi mã hiện tại sau khi chuyển hướng
}
$_SESSION["IsLogin"] = false;
header("Location: login.html");

?>