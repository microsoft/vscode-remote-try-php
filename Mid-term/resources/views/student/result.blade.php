@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('./css/admin_student.css') }}">
    <style>
        .custom-select {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .custom-select select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: white;
            font-size: 14px;
        }

        .custom-select .select2-selection {
            display: block;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 8px;
            min-height: 34px;
        }

        .custom-select .select2-selection:hover {
            border-color: #aaa;
        }

        .custom-select .select2-selection .select2-selection__arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            right: 8px;
        }

        .select2-results__option {
            padding: 8px;
        }
    </style>

    <div class="card">
        <div class="card-body py-3">
            <h3>{{__('Result')}} {{__('Register')}} {{$student->full_name}}</h3>
            @if((Auth::user()->roles[0]->name == 'AD'))
                <a href="{{route('result_all')}}"  class="btn btn-info mb-3">{{ __('Back') }} </a>
                <a href="{{route('points.change',$student->id)}}"  class="btn btn-success mb-3">{{ __('Update') }} Nhiều Môn Học</a>
            @endif
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>{{__('Full Name')}}</th>
                        <th>{{__('Subject')}}</th>
                        <th>{{__('Point')}}</th>
                        @if((Auth::user()->roles[0]->name == 'AD'))
                            <th>{{__('Point management')}}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                @foreach($student->subjects as $index =>$data)
                    <tr>
                        @if($index === 0)
                            <td rowspan="{{ count($student->subjects) }}">{{ $student->full_name }}</td>
                        @endif
                        <td>{{$data->name}}</td>
                        <td>
                            @if($data->pivot->point !=null)
                                {{$data->pivot->point}}
                            @else
                                N/A
                            @endif
                        </td>
                        @if((Auth::user()->roles[0]->name == 'AD'))
                            <td><button type="button" value="{{$data->id}}"  class="btn btn-warning edit" data-toggle="modal" data-target="#modal_edit">{{ __('Update') }}</button></td>
                        @endif
                    </tr>
                @endforeach
                @if(count($faculties) == $count && $count != 0)
                    <tr>
                        <td colspan="2">{{__('The average point is')}}</td>
                        <td>{{$average}}</td>
                        @if((Auth::user()->roles[0]->name == 'AD'))
                            <td></td>
                        @endif
                    </tr>
                @else
                    <tr>
                        <td colspan="2">{{__('Did not complete all subjects')}}</td>
                        <td></td>
                        @if((Auth::user()->roles[0]->name == 'AD'))
                            <td></td>
                        @endif
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <button style="display: none" value="{{$student->id}}" class="student_id"></button>
    <div class="modal fade" id="modal_edit" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="justify-content: flex-end">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {{ Form::open(['id' => 'form_update_point']) }}
                    <div class="form-group">
                        <label for="username">{{__('Name')}} {{__('Subject')}}:</label>
                        {{ Form::hidden('subject_id', null, ['id' => 'subject_id']) }}
                        {{ Form::hidden('student_id', $student->id, ['id' => 'student_id']) }}
                        {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name_subject', 'required','readonly']) }}
                    </div>
                    <div class="form-group">
                        <label for="username">{{__('Point')}}:</label>
                        {{ Form::number('point', null, ['class' => 'form-control', 'id' => 'point', 'step' => '0.01', 'required']) }}
                        <div class="text-danger" id="point_error"></div>
                    </div>
                    <div class="modal-footer">
                        {{ Form::submit(__('Submit'), ['class' => 'btn btn-primary']) }}
                        <button type="button" class="btn btn-default" data-dismiss="modal">{{__('Close')}}</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/admin_subject.js') }}"></script>

@endsection
