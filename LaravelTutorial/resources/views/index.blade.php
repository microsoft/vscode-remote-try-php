@if (Auth::check())
    Chào mừng {{ Auth::user()->username }}
    <a href={{ route('logout') }}>Đăng xuất</a>
@else
    <a href={{ route('login') }}>Đăng nhập</a>
    <a href={{ route('register') }}>Đăng ký</a>
@endif