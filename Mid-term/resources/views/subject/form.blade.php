@extends('layouts.app')

@section('content')
    <div class="card p-10">
        <a href="{{route('subjects.index') }}" style="margin-bottom: 25px;width: 100px" class="btn btn-info">{{ __('Back') }}</a>

        @if(isset($subject))
            {!! Form::model($subject, ['route' => ['subjects.update', $subject->id], 'method' => 'put']) !!}
        @else
            {!! Form::open(['route' => 'subjects.store', 'method' => 'post']) !!}
        @endif
        <div class="form-group mb-10">
            {!! Form::label('name', __('Name subject'), ['class' => 'fw-bold']) !!}
            <span class="required-field">*</span>
            {!! Form::text('name', isset($subject) ? $subject->name : old('name'), ['class' => 'form-control']) !!}
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-10">
            {!! Form::label('description', __('Faculty'), ['class' => 'fw-bold']) !!}
            <span class="required-field">*</span>
            {!! Form::select('faculty_id', ['' => __('Choose Faculty')] + $faculties->pluck('name', 'id')->toArray(), isset($subject) ? $subject->faculty_id : old('faculty_id'), ['class' => 'form-control']) !!}
            @error('description')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group mb-10">
            {!! Form::label('description', __('Description'), ['class' => 'fw-bold']) !!}
            <span class="required-field">*</span>
            {!! Form::text('description', isset($subject) ? $subject->description : old('description'), ['class' => 'form-control']) !!}
            @error('description')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="modal-footer">
            {!! Form::submit(__('Submit'), ['class' => 'btn btn-primary']) !!}
            <a href="{{route('subjects.index')}} " class="btn btn-default">{{__('Back')}}</a>
        </div>
        {!! Form::close() !!}
    </div>
    <script>
        $(document).ready(function() {
            $('select[name="faculty_id"]').select2();
        });
    </script>
@endsection
