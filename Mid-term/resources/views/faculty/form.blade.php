@extends('layouts.app')

@section('content')

    <div class="card p-10">
        <a href="{{ route('faculties.index') }}" style="margin-bottom: 25px;width: 100px" class="btn btn-info">{{ __('Back') }}</a>
        @if(isset($faculty))
            {!! Form::model($faculty, ['route' => ['faculties.update', $faculty->id], 'method' => 'put']) !!}
        @else
            {!! Form::open(['route' => 'faculties.store', 'method' => 'post']) !!}
        @endif
        <div class="form-group mb-10">
            {!! Form::label('name', __('Tên phòng ban:'), ['class' => 'fw-bold']) !!}
            <span class="required-field">*</span>
            {!! Form::text('name', isset($faculty) ? $faculty->name : old('name'), ['class' => 'form-control', 'id' => 'name_department_edit']) !!}
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            {!! Form::label('description', __('Description:'), ['class' => 'fw-bold']) !!}
            <span class="required-field">*</span>
            {!! Form::text('description', isset($faculty) ? $faculty->description : old('description'), ['class' => 'form-control', 'id' => 'description_edit']) !!}
            @error('description')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="modal-footer">
            {!! Form::submit(__('Submit'), ['class' => 'btn btn-primary']) !!}
            <button type="button" class="btn btn-default">{{ __('Close') }}</button>
        </div>
        {!! Form::close() !!}
    </div>

@endsection
