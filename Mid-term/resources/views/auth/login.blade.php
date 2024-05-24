<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ __('Login') }}</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('./css/login.css')}}">
	<link rel="shortcut icon" href="{{asset('./icon.png')}}" />
</head>

<body>
    <div class="top-right links" style="display: flex;justify-content: flex-end">
        <a href="{{ route('language.change', ['vi']) }}" class="dropdown-item" style="color: #e5e5e5">
            {{ __('Vietnamese') }}
        </a>
        <a href="{{ route('language.change', ['en']) }}" class="dropdown-item" style="color: #e5e5e5">
            English
        </a>
    </div>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    @if (session('error'))

    @endif
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="row mb-3">
            <label for="username" class="col-md-4 col-form-label text-md-end">{{ __('Username') }}</label>

            <div class="col-md-6">
                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                    name="name" value="{{ old('username') }}" required autocomplete="username" autofocus>
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="row mb-3">
            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
            <div class="col-md-6">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <div class="row mb-0">
            <div class="col-md-8 offset-md-4">
                <button type="submit" class="btn btn-primary">
                    {{ __('Login') }}
                </button>
            </div>
        </div>
    </form>
</body>

</html>
