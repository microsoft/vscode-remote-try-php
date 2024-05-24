@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-body py-3">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ __(session('error')) }}
                </div>
            @endif
{{--            @if(Auth::user()->roles[0]->name == 'ST' && Auth::user()->is_disabled == '1')--}}
{{--                <h1>Bạn đã bị buộc thôi học</h1>--}}
{{--            @else--}}
{{--                <h1>Trang chủ sinh viên</h1>--}}
{{--            @endif--}}
        </div>
    </div>
@endsection
