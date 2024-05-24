@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <div class="card">
        <div class="row" style="margin: 10px 20px 0 355px">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info box">
                    <div class="inner">
{{--                        <h3>{{$count_user}}</h3>--}}
                        <p>{{ __('Student') }}</p>
                    </div>
                    <a href="{{route('students.index')}}" class="small-box-footer">{{__('Management')}} {{ __('Student') }}<i style="margin-left: 10px" class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">

                <div class="small-box bg-success box">
                    <div class="inner">
{{--                        <h3>{{$count_department}}</h3>--}}
                        <p>{{ __('Faculty') }}</p>
                    </div>
                    <a href="{{route('faculties.index')}}" class="small-box-footer">{{__('Management')}} {{ __('Faculty') }}<i style="margin-left: 10px" class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning box">
                    <div class="inner">
{{--                        <h3>{{$count_subject}}</h3>--}}
                        <p>{{ __('Point') }} {{__('Student')}}</p>
                    </div>
                    <a href="" class="small-box-footer">{{__('Management')}} {{ __('Point') }}<i style="margin-left: 10px" class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>

@endsection
