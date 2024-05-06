<?php
session_start();
// Đặt trạng thái login thành false và xóa session
$_SESSION["IsLogin"] = false;
session_destroy();
// Redirect về trang đăng nhập
header("Location: login.htm");
?>