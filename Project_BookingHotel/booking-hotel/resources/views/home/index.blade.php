<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('home.css') }}">
    <title>Home</title>
</head>

<body>
    <div id="header">
        <div class="logo">
            <a href="{{route('index')}}"><img src="{{ asset('assets/image/logo.png') }}" alt="logo"></a>
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
        <ul>
                @if(Auth::check())
                    <li><a href="#">Hello, {{ Auth::user()->name }}</a></li>
                    <li><a href="{{ route('logout') }}">LOGOUT</a></li>
                @else
                    <li><a href="{{ route('login') }}">LOGIN</a></li>
                    <li><a href="{{ route('register') }}">REGISTER</a></li>
                @endif
            </ul>
    </div>
</body>

</html>