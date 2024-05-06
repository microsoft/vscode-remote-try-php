<?php
session_start();
if ($_SESSION["Login"] == false)
    header("location: login.html");

if($_SERVER["REQUEST_METHOD"]== "POST"){
    $_SESSION["Login"] = false;
    header("location: login.html");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Đã đăng nhập thành công</h2>
    <form action="dashbord.php" method="post">
            <button class="logoutBtn" type="submit">Log out</button>
        </form>
    
</body>
</html>