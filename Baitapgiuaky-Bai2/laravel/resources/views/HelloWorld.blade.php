<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/home.css">
    <title>Home</title>
</head>

<body>
    <div id="header">
        <div class="logo">
            <a href="#"><img src="../Assets/image/logo.png" alt="logo"></a>
        </div>
        <div class="menu">
            <div class="list-menu">
                <ul>
                    <li><a href="#">HOME</a></li>
                    <li><a href="#">ABOUT</a></li>
                    <li><a href="#">ROOM</a></li>
                    <li><a href="#">RESTAURANT</a></li>
                    <li><a href="#">CONTACT</a></li>
                </ul>
            </div>
        </div>
        <div class="login">
        <?php
        if (isset($_SESSION['email'])) {
            // Người dùng đã đăng nhập
            echo '
            <ul>
                <li>Hello, ' . htmlspecialchars($_SESSION['username']) . '!</li>
                <li><a href="../../app/Http/Controllers/logout.php">LOGOUT</a></li>
            </ul>
            ';
        } else {
            // Người dùng chưa đăng nhập
            echo '<ul>
                    <li><a href="login.blade.php">LOGIN</a></li>
                    <li><a href="signup.blade.php">REGISTER</a></li>
                </ul>';
        }
        ?>
        </div>
    </div>
    
</body>

</html>