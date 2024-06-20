<?php
session_start();

// Reset trạng thái login
$_SESSION["IsLogin"] = false;

// Redirect về trang login
header("Location: login.html");
?>