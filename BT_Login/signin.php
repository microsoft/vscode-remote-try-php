<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Register</title>
</head>
<body>
    <div id="register">
        <h3 class="text-center text-white pt-5">Register form</h3>
        <div class="container">
            <div id="register-row" class="row justify-content-center align-items-center">
                <div id="register-column" class="col-md-6">
                    <div id="register-box" class="col-md-12">
                        <form id="register-form" class="form" action="registeruser.php" method="post">
                            <h3 class="text-center text-info">Đăng ký</h3>
                            <div class="form-group">
                                <label for="new-username" class="text-info">Tài khoản:</label><br>
                                <input type="text" name="new-username" id="new-username" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="new-password" class="text-info">Mật khẩu:</label><br>
                                <input type="password" name="new-password" id="new-password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="confirm-password" class="text-info">Xác nhận mật khẩu:</label><br>
                                <input type="password" name="confirm-password" id="confirm-password" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="register_submit" class="btn btn-info btn-md" value="Đăng ký">
                            </div>
                            <div id="login-link" class="text-right">
                                <a href="login.php" class="text-info">Đăng nhập</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
