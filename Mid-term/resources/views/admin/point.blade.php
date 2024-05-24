@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('./css/admin_student.css') }}">
    <div class="card">

        <div class="card-body py-3">

            <div class="file-input-wrapper">
                <h3>Thống kê sinh viên đăng ký môn học</h3>
            </div>
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Số thứ tự</th>
                            <th>Tên sinh viên</th>
                            <th>Số môn đã đăng ký</th>
                            <th>Số môn chưa đăng ký</th>
                            <th>Quản lý điểm</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($points as $point)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $point['student']->full_name }}</td>
                            <td>{{ $point['registeredSubjects'] }}</td>
                            <td>
                                @if($point['unregisteredSubjects'] >0)
                                    {{ $point['unregisteredSubjects'] }}
                                    <button type="button" value="{{ $point['student']->id }}" class="btn btn-secondary unsubject" data-toggle="modal" data-target="#myModal1">{{ __('Xem chi tiết') }}</button>
                                @else
                                    Đã đăng ký đủ môn
                                @endif
                            </td>
                            <td>
                                <a class="btn-success btn" href="{{route('admin.point.manage',$point['student']->id)}}">Quản lý điểm</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="pagination">
                    @include('layouts.pagination', ['users' => $paginatorInstance])
                </div>
        </div>
    </div>

    <div class="modal fade" id="myModal1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="justify-content: flex-end">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.point.send') }}" method="post">
                        @csrf
                        <table class="reg" style="border-collapse: collapse; width: 100%;">
                            <thead>
                                <tr>
                                    <th
                                        style="background-color: #f2f2f2; padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">
                                        ID</th>
                                    <th
                                        style="background-color: #f2f2f2; padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">
                                        Môn học</th>
                                </tr>
                            </thead>
                            <input type="hidden" class="id_user_unregsister" name="id_user" value="">
                            <tbody id="subjectTableBody">
                            </tbody>
                        </table>
                        <div style="display: flex;justify-content: center;align-items: center" id="buttonContainer">
                            <button style="margin-top:15px" class="btn btn-primary sendMailButton">{{__('Send')}} {{__('Mail')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/admin_faculty.js') }}"></script>
@endsection
