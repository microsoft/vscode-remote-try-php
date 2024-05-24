@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('./css/admin_student.css') }}">
    <div class="card">

        <div class="card-body py-3">

            <div class="file-input-wrapper">
                <form style="display: flex;" action="{{route('students.import')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div>
                        <label>{{__('Please upload only Excel (.xlsx or .xls) files')}}</label>
                        <input type="file" required class="form-control" name="file_import">
                    </div>
                    <button style="margin-top: 23px;margin-left: 31px;" class="btn-success btn">{{__("Send")}}</button>
                </form>
                <h3>{{__('Statistics of students registered for the subject')}}</h3>
            </div>
            <table class="custom-table">
                <thead>
                <tr>
                    <th>{{__('Ordinal number')}}</th>
                    <th>{{__('Full Name')}}</th>
                    <th>{{__('Number of registered subjects')}}</th>
                    <th>{{__('Number of subjects not registered')}}</th>
                    <th>{{__('Point management')}}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($result as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item['student']->full_name }}</td>
                        <td>{{ $item['reg'] }}</td>
                        <td>
                            @if($item['unreg'] >0)
                                <form action="{{route('mail.send',$item['student']->id)}}" method="post" class="mb-3">
                                    @csrf
                                    <button style="width: 114px;" class="btn btn-info">{{__('Gửi Mail')}}</button>
                                </form>
                                <button style="width: 114px" type="button" value="{{ $item['student']->id }}" class="btn btn-secondary unreg" data-toggle="modal" data-target="#myModal1">{{ __('Xem chi tiết') }}</button>
                            @else
                                {{__('Already registered for all subjects')}}
                            @endif
                        </td>
                        <td>
                            <a href="{{route('point',$item['student']->id)}}" class="btn-success btn" href="">{{__('Point management')}}</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">
                @include('layouts.pagination', ['data' => $paginatorInstance])
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
                    <table class="reg" style="border-collapse: collapse; width: 100%;">
                        <thead>
                        <tr>
                            <th style="background-color: #f2f2f2; padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">ID</th>
                            <th style="background-color: #f2f2f2; padding: 10px; text-align: left; border-bottom: 1px solid #ddd;">{{__('Subject')}}</th>
                        </tr>
                        </thead>
                            <tbody id="unreg">
                        </tbody>
                    </table>
                    <div style="display: flex;justify-content: center;align-items: center" id="buttonContainer">
                        <button style="margin-top:15px" class="btn btn-primary sendMailButton">{{__('Send')}} {{__('Mail')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/admin_faculty.js') }}"></script>
@endsection
