<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script src={{ asset('Js/main.js') }} ></script>
</head>
<body>
    @csrf
    <form action="/api/login" method="post">
        <label for="">Username</label>
        <input type="text" name="username" id="username"><br><br>
        <label for="">Password</label>
        <input type="password" name="password" id="password"><br><br>
        <input type="submit" value="Đăng nhập" id="btn-login">
    </form>
</body>
</html>