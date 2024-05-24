@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body py-3">
            <a href="{{route('students.index') }}" style="margin-bottom: 25px;width: 100px"
               class="btn btn-info">{{ __('Back') }}</a>
            @if(isset($student))
                {!! Form::model($student, ['route' => ['students.update', $student->id], 'method' => 'put', 'enctype' => 'multipart/form-data']) !!}
            @else
                {!! Form::open(['route' => 'students.store', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
            @endif
            <div class="row mb-5">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('name', __('User Name'), ['class' => 'fw-bold']) !!}
                        <span class="required-field">*</span>
                        {!! Form::text('name', isset($student) ? $student->user->name : old('name'), ['class' => 'form-control']) !!}
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('full_name', __('Full Name'), ['class' => 'fw-bold']) !!}
                        <span class="required-field">*</span>
                        {!! Form::text('full_name', isset($student) ? $student->full_name : old('full_name'), ['class' => 'form-control']) !!}
                        @error('full_name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('email', __('Email'), ['class' => 'fw-bold']) !!}
                        <span class="required-field">*</span>
                        {!! Form::email('email', isset($student) ? $student->user->email : old('email'), ['class' => 'form-control']) !!}
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('gender', __('Gender'), ['class' => 'fw-bold']) !!}
                        <span class="required-field">*</span>
                        {!! Form::select('gender', ['' => __('Choose Gender')] + collect(App\Enums\Base::toSelectArray())->map(fn($label) => __($label))->toArray(),
                        isset($student) ? $student->gender : old('gender'), ['class' => 'form-control']) !!}
                        @error('gender')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('faculty', __('Faculty'), ['class' => 'fw-bold']) !!}
                        <span class="required-field">*</span>
                        {!! Form::select('faculty', ['' => __('Choose Faculty')] + $faculties->pluck('name', 'id')->toArray(), isset($student) ?
                        $student->faculty_id : old('faculty'), ['class' => 'form-control']) !!}
                        @error('faculty')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('birthday', __('Birthday'), ['class' => 'fw-bold']) !!}
                        {!! Form::date('birthday', isset($student) ? $student->birthday : old('birthday'), ['class' => 'form-control']) !!}
                        @error('birthday')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('address', __('Address'), ['class' => 'fw-bold']) !!}
                        <span class="required-field">*</span>
                        {!! Form::text('address', isset($student) ? $student->address : old('address'), ['class' => 'form-control']) !!}
                        @error('address')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('phone', __('Phone'), ['class' => 'fw-bold']) !!}
                        <span class="required-field">*</span>
                        {!! Form::text('phone', isset($student) ? $student->phone : old('phone'), ['class' => 'form-control']) !!}
                        @error('phone')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row mb-5">
                <div class="col">
                    <div class="form-group">
                        {!! Form::label('avatar', __('Avatar'), ['class' => 'fw-bold']) !!}
                        {!! Form::file('avatar', ['class' => 'form-control','id'=>'image-input', 'onchange' => 'displayAvatar(this)']) !!}
                        <div id="avatar-preview"
                             @if(isset($student) && $student->avatar) style="display: block;" @endif>
                            @if(isset($student) && $student->avatar)
                                <img id="avatar-image" src="{{ asset('storage/images/students/' . $student->avatar) }}"
                                     alt="Avatar" style="max-width: 100px; max-height: 100px;">
                            @else
                                <img id="avatar-image" src="#" alt="Avatar"
                                     style="max-width: 100px; max-height: 100px; display: none;">
                            @endif
                        </div>
                        @error('avatar')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                @if(isset($student))
                    <div class="col">
                        <div class="form-group">
                            {!! Form::label('password', __('Password'), ['class' => 'fw-bold']) !!}
                            {!! Form::password('password', ['class' => 'form-control']) !!}
                            @error('password')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        {!! Form::submit(__('Submit'), ['class' => 'btn btn-primary', 'style' => 'float: right;']) !!}
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <script>
        function displayAvatar(input) {
            const img = document.getElementById("avatar-image");
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    img.src = event.target.result;
                    img.style.display = "block";
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                img.src = "#";
                img.style.display = "none";
            }
        }
    </script>

@endsection
