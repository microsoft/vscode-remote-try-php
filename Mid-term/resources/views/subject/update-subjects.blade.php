@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('./css/admin_student.css') }}">
    <style>
        .inline-row {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .inline-row select, .inline-row input[type="text"] {
            margin-right: 10px;
        }

        .inline-row button {
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
        .point-input{
            margin-right: 35px;
        }
        .add{
            width: 8%;
            margin-left: 27px;
        }
        .form{
            padding: 0 150px;
        }
    </style>
    <div style="margin-left: 150px;">
        <h3>{{__('Update many points for students')}}</h3>
    </div>
    <div class="d-flex" style="margin-left: 123px;">
        <a href="{{route('point',$student->id)}}" style="margin-left: 27px;" class="btn btn-info  mb-3">{{__('Back')}}</a>
        {!! Form::button('+', ['type' => 'button', 'class' => 'btn btn-success add mb-3', 'id' => 'addRowButton']) !!}
    </div>
    {!! Form::open(['class' => 'form','id' => 'form_update_point']) !!}
    {!! Form::hidden('student_id', $student->id) !!}
        <div class="body" id="Body">
        </div>
    {!! Form::submit(__('Lưu điểm'), ['class' => 'btn btn-primary pull-right', 'id' => 'saveScoreButton']) !!}
    {!! Form::close() !!}

    <script>
        $(document).ready(function () {
            let url = window.location.pathname;
            let id = url.substring(url.lastIndexOf('/') + 1);
            var optionValue = getOptionSelected();
            let route = `${window.location.origin}/api/point-subjects/` + id;
            $.ajax({
                type: 'get',
                url: route,
                success: function (data) {
                    var subjectsRegister = data.subjectsRegister;
                    subjectsRegister.forEach(function(subjectRegister) {
                        disableButton(clickLimit, limit);
                        var selectedSubjectId = subjectRegister.pivot.subject_id;
                        var selectedPoint = subjectRegister.pivot.point;
                        if (selectedPoint !== null) {
                            var html = '<div class="inline-row">' +
                                '<select name="subject_id[]" class="form-control subject">' +
                                '<option value="">-- Chọn môn học --</option>';
                            var selectedFound = false;
                            @foreach($subjects as $subject)
                                @if ($subject->pivot->point == '')
                                html += '<option value="{{ $subject->id }}">{{ $subject->name }}</option>';
                                @else
                                if ({{ $subject->id }} ===  selectedSubjectId  && !selectedFound) {
                                    html += '<option value="{{ $subject->id }}" selected>{{ $subject->name }}</option>';
                                    selectedFound = true;
                                }
                            @endif
                                @endforeach
                                html += '</select>' +
                                '<input type="number" name="point[]" class="form-control point-input" placeholder="{{ __('Point') }}" value="' + (selectedPoint ? selectedPoint : '') + '">' +
                                '<button type="button" class="btn btn-danger deleteRowButton">X</button>' +
                                '<div class="error-message"></div>' +
                                '</div>';
                            $('#Body').append(html);
                            updateClickLimit(1);
                            filterSubject(optionValue);
                        }
                    });
                },
                error: function (xhr, status, error) {
                    console.log('Sai dữ liệu');
                }
            });
            var limit = {{count($subjects)}};
            if({{count($subjects)}} === 0){
                var html = `<div class="inline-row" style="display: block">
                <h3 style="text-align: center">Sinh viên chưa đăng ký môn học</h3>
            </div>`;
                $('#saveScoreButton').hide()
                $('#Body').append(html);
            }
            var clickLimit = $('.select-point .subject').length;
            disableButton(clickLimit, limit);
            filterSubject(optionValue);
            $('.body').on('click', '.deleteRowButton', function () {
                $(this).closest('.inline-row').remove();
                updateClickLimit(-1);
            });
            $('.add').on('click', function () {
                updateClickLimit(1);
                disableButton(clickLimit, limit);
                var html = `<div class="inline-row">
                {!! Form::select('subject_id[]', ['' => '-- Chọn môn học --'] + $subjects->pluck('name', 'id')->toArray(), null, ['class' => 'form-control subject']) !!}
                {!! Form::number('point[]', null, ['class' => 'form-control point-input', 'placeholder' => __('Point')]) !!}
                <button type="button" class="btn btn-danger deleteRowButton">X</button>
                <div class="error-message"></div>
            </div>`;
                $('#Body').append(html);
                updateOptionValue();
                filterSubject(optionValue);
            });
            $('.body').on('change', '.subject', function () {
                var $thisRow = $(this).closest('.inline-row');
                var selectedSubjectId = $(this).val();
                var studentSubjects = @json($subjects);
                var matchingSubject = studentSubjects.find(function (subject) {
                    return subject.id == selectedSubjectId;
                });
                if (matchingSubject) {
                    $thisRow.find('.point-input').val(matchingSubject.pivot.point);
                } else {
                    $thisRow.find('.point-input').val('');
                }
                updateOptionValue();
                filterSubject(optionValue);
            });

            function getOptionSelected() {
                return $('.body .subject option:selected').map(function () {
                    return $(this).val();
                }).get();
            }

            function disableButton(clickLimit, limit) {
                $('.add').prop('disabled', clickLimit >= limit);
            }

            function updateClickLimit(value) {
                clickLimit += value;
                disableButton(clickLimit, limit);
            }

            function updateOptionValue() {
                optionValue = getOptionSelected();
            }

            function filterSubject(e) {
                $('.subject').each(function () {
                    $(this).find('option').each(function () {
                        $(this).toggle($.inArray($(this).val(), e) === -1);
                    });
                });
            }
        });

        $('#form_update_point').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('points.update') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if(response.errors){
                        let errors = response.errors
                        for (let field in errors) {
                            if (errors.hasOwnProperty(field)) {
                                let errorMessage = errors[field][0];
                                toastr.error(`${errorMessage}`, "Lỗi", {
                                    timeOut: 2000,
                                    closeButton: true,
                                });
                            }
                        }
                    }
                    if(response.status){
                        let url = window.location.pathname;
                        let id = url.substring(url.lastIndexOf('/') + 1);
                        let route = `${window.location.origin}/admin/point/` + id;
                        window.location.href = route;
                    }
                },
                error: function(error) {
                    toastr.error("Lỗi hãy kiểm tra lại!", "Lỗi", {
                        timeOut: 2000,
                        closeButton: true,
                    });
                }
            });
        });
    </script>


@endsection
