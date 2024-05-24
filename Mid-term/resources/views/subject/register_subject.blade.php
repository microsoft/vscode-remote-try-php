@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <link rel="stylesheet" href="{{ asset('./css/admin_student.css') }}">
    <div class="card">
        <div class="card-body py-3">
            <h3>{{__('Register')}} {{__('Subject')}}</h3>
            <div id="success-message" class="alert alert-success" style="display: none;">
                Đăng ký môn thành công!
            </div>
            <table class="custom-table" style="text-align: center;">
                <thead>
                    <tr>
                        <th style="text-align: center;">{{ __('Ordinal number') }}</th>
                        <th style="text-align: center;">{{ __('Name') }} {{ __('Subject') }}</th>
                        <th style="text-align: center;">{{ __('Name') }} {{ __('Faculty') }}</th>
                        <th style="text-align: center;">{{ __('Status') }}</th>
                        <th style="text-align: center;">
                            <span>{{__('Choose')}} {{__('All')}}</span>
                            <input type="checkbox" id="checkAll" >
                            <button class="btn btn-success sendData">{{__('Register')}}</button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($paginatedSubjectStatus as $subject)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $subject['subject']->name }}</td>
                        <td>{{ $subject['subject']->faculty->name }}</td>
                        @if($subject['isRegistered'])
                            <td><button class="btn btn-secondary">{{__("Registered")}}</button></td>
                        @else
                            <td><button class="btn btn-danger">{{__("Unregistered")}}</button></td>
                        @endif
                        <td>
                            @if($subject['isRegistered'])
                                <input type="checkbox" disabled="disabled" checked="checked">
                            @else
                                <input type="checkbox" class="check" name="register[]" value="{{$subject['subject']->id}}">
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">
                @include('layouts.pagination', ['data' => $paginatedSubjectStatus])
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#checkAll').change(function() {
                $('.check').prop('checked', $(this).prop('checked'));
            });
            $('.check').change(function() {
                if ($('.check:checked').length === $('.check').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            });
            $('.sendData').click(function() {
                var checkedValues = $("input[name='register[]']:checked").map(function() {
                    return $(this).val();
                }).get();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                let route = `${window.location.origin}/student/register`
                $.ajax({
                    type: "POST",
                    url: route,
                    data: { checkedValues: checkedValues },
                    success: function(response) {
                        if(response.status){
                            location.reload();
                        }else{
                            toastr.error("Đăng ký không thành công!", "Lỗi", {
                                timeOut: 2000,
                                closeButton: true,
                            });
                        }
                    },
                    error: function(error) {
                        toastr.error("Đăng ký không thành công!", "Lỗi", {
                            timeOut: 2000,
                            closeButton: true,
                        });
                    }
                });
            });
        });
    </script>
@endsection
