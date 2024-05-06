<?php
    session_start();
    if(isset($_SESSION['username']) && ($_SESSION['username']!=""))
    {
        $username = $_SESSION['username'];
    }
    if(isset($_SESSION['password']) && ($_SESSION['password']!=""))
    {
        $password = $_SESSION['password'];
    }
    if($username!="") $logined="Xin chào: ".$username;
    else $logined="Login";
    if($password!="") $passworded="Mật khẩu: ".$password;
    else $passworded="...";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Logout</title>
</head>

<body>
    <p style="margin: 20px;"><?=$logined?></p>
    <p style="margin: 20px;"><?=$passworded?></p>   
    <p style="margin: 20px;">
        <a href="logout.php" class="btn btn-info btn-lg">
          <span class="glyphicon glyphicon-log-out"></span> Thoát
        </a>
    </p>  
</body>
</html>