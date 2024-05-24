@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('./css/profile.css') }}">
    <div class="card">
        <div class="card-body py-3">
            <div class="profile">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ __(session('success')) }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ __(session('error')) }}
                    </div>
                @endif
                <div class="profile-header">
                    <h1>{{__('Profile')}}</h1>
                </div>
                <div class="profile-content">
                    <form action="{{ route('change_avatar') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div>
                            @if($user->avartar == '')
                            <label for="avatar-input">
                                <img style="margin-left:24px" src="{{ asset('logo_user.png') }}" alt="User Avatar"
                                    class="avatar" id="avatar">
                            </label>
                            @else
                            <label for="avatar-input">
                                <img style="margin-left:24px" src="{{ asset('images/'.$user->avartar) }}" alt="User Avatar"  class="avatar" id="avatar">
                            </label>
                            @endif
                            <input type="file" accept="image/*" name="image" id="avatar-input" style="display: none;">
                        </div>
                        <div style="display: flex;justify-content: center;align-items: center;margin-top: 10px">
                            <button type="submit" style="display: none" class="btn btn-dark submit-btn">Submit</button>
                            <button type="button" style="display: none" class="btn btn-info cancer">Cancer</button>
                        </div>
                    </form>
                </div>
                <h1 style="text-align: center">Chức vụ: @if(Auth::user()->roles[0]->name ==  'AD')
                        Admin
                    @else
                        Sinh viên
                @endif</h1>
            </div>
        </div>
    </div>
    <script>
        const avatarInput = document.getElementById('avatar-input');
        const avatar = document.getElementById('avatar');
        const submitBtn = document.querySelector('.submit-btn');
        const revertBtn = document.querySelector('.cancer');
        let originalAvatarSrc;

        function setAvatarSrc(src) {
            avatar.src = src;
        }
        avatarInput.addEventListener('change', function(event) {
            const selectedFile = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function(event) {
                setAvatarSrc(event.target.result);

                submitBtn.style.display = 'block';
                revertBtn.style.display = 'block';
            };
            reader.readAsDataURL(selectedFile);
        });

        revertBtn.addEventListener('click', function() {
            setAvatarSrc(originalAvatarSrc);
            submitBtn.style.display = 'none';
            revertBtn.style.display = 'none';
        });

        window.onload = function() {
            originalAvatarSrc = avatar.src;
        };
    </script>
@endsection
