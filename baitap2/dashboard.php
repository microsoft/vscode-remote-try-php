<?php
session_start();
if ($_SESSION["Login"] == false)
    header("location: login.html");

if($_SERVER["REQUEST_METHOD"] == "POST"){
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .logoutBtn {
            background-color: #4CAF50;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin-top: 20px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .logoutBtn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h2>Đã đăng nhập thành công</h2>
    <form action="dashboard.php" method="post">
            <button class="logoutBtn" type="submit">Log out</button>
        </form>
    
</body>
</html>