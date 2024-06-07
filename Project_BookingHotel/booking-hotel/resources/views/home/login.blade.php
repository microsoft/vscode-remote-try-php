<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <script src="https://kit.fontawesome.com/7b9d8c4ddc.js" crossorigin="anonymous"></script>
    <title>Sign Up</title>
</head>

<body>
    <div id="header">
        <div class="logo">
            <a href="#"><img src="{{ asset('assets/image/logo.png') }}" alt="logo"></a>
        </div>
    </div>

    <div class="container">
        <div class="container-login">
            <div class="container-left">
                <div class="container-left-img">

                </div>
            </div>
            <div class="container-right">
            <form action="" method="post" id="loginForm">
                @csrf
                <h3 class="container-right-h3">Create your account</h3>
                <p class="container-right-p1">Let's get started with your 14 days free trail</p>
                <a href="{{ route('auth.google') }}" class="container-right-btn1">
                    <i class="fa-brands fa-google btn-gg" style="color: #0765ff;"></i>Continue with Google
                </a>
                <br>
                <!-- <input class="container-right-input-name" type="text" placeholder="Full Name"> -->
                <br>
                <input class="container-right-input-email" type="email" placeholder="Email" name="email" >
                <br>
                <input class="container-right-input-pw" type="password" placeholder="Password" name="password" >
                <!-- <input class="container-right-input-confirm" type="text" placeholder="Confirm"> -->
                <br>
                <p class="container-right-p2">
                <i class="fa-solid fa-circle-check" style="color: #da7d2f;"></i>
                    Remember me for 30 days
                </p>
                <button class="container-right-btn2" name="login_submit" type="submit" id="loginButton">
                    Login
                </button>
                @if($message = Session::get('error'))
                    <div class="alert alert-success alert-block" style="margin: 60px; color: red; margin-top: 10px">
                        <button type="button" class="close" data-dismiss="alert"></button>
                        <strong>{{$message}}</strong>
                    </div>
                @endif
                <p class="container-right-p3">
                    Already have a account?<a href="{{route('register')}}"> Register</a>
                </p>
            </form>
            </div>
        </div>
    </div>
    
</body>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        // Lấy form và nút submit
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');

        // Thêm sự kiện keydown cho form
        loginForm.addEventListener('keydown', (event) => {
            // Kiểm tra nếu phím Enter được nhấn
            if (event.key === 'Enter') {
                // Ngăn chặn hành động mặc định của Enter
                event.preventDefault();
                // Giả lập click vào nút submit
                loginButton.click();
            }
        });
    });
</script>


</html>