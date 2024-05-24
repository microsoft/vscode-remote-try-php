@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('./css/admin_student.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/search.css') }}">
    <style>
        .select2-container {
        }
        .select2-selection__rendered{
            margin-top: 12px;
        }

    </style>
    <div class="card">
        <div class="card-body py-3">
            <div style="display: flex;">
                <a  href="{{route('students.create')}}" style="margin-bottom: 25px" class="btn btn-success ">{{__('Add Student')}}</a>
                <button style="margin-bottom: 25px;margin-left: 10px;" type="button" class="btn btn-info" data-toggle="modal" data-target="#myModal1">{{ __('Thêm nhanh') }}</button>

                {{ Form::open(['method' => 'get']) }}

                <label for="min_age">{{__('Min Age')}}:</label>
                {{ Form::number('min_age', request('min_age'), ['min' => 0, 'id' => 'min_age', 'style' => 'margin: 0 10px;']) }}

                <label for="max_age">{{__('Max Age')}}:</label>
                {{ Form::number('max_age', request('max_age'), ['min' => 0, 'id' => 'max_age', 'style' => 'margin: 0 10px;']) }}

                <label for="avg_from">{{__('Min average')}}:</label>
                {{ Form::number('avg_from', request('avg_from'), ['step' => '0.01', 'min' => 0, 'id' => 'avg_from', 'style' => 'margin: 0 10px;']) }}

                <label for="avg_to">{{__('Max average')}}:</label>
                {{ Form::number('avg_to', request('avg_to'), ['step' => '0.01', 'min' => 0, 'id' => 'avg_to', 'style' => 'margin: 0 10px;']) }}

                <button style="margin-left: 10px;" type="submit">{{__('Filter')}}</button>
                {{ Form::close() }}
            </div>
            <table class="custom-table">
                <thead>
                <tr>
                    <th>{{__('Ordinal number')}}</th>
                    <th>{{__('Full Name')}}</th>
                    <th>{{__('Age')}}</th>
                    <th>Email</th>
                    <th>{{__('Faculty')}}</th>
                    <th>{{__('Phone')}}</th>
                    <th>{{__('Address')}}</th>
                    <th>{{__('Gender')}}</th>
                    <th>{{__('Avatar')}}</th>
                    <th>{{__('Point')}}</th>
                    <th>{{__('Action')}}</th>
                </tr>
                </thead>
                <tbody>
                @if(count($students) >0)
                    @foreach ($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->age }}</td>
                            <td>{{ $student->user->email }}</td>
                            <td>{{ $student->faculty->name }}</td>
                            <td>{{ $student->phone }}</td>
                            <td>{{ $student->address }}</td>
                            <td>{{ __(App\Enums\Base::toSelectArray()[$student->gender])}}</td>
                            <td>
                                @if($student->avatar != null)
                                    <img style="width: 60px" src="{{ asset('storage/images/students/' . $student->avatar) }}" alt="">
                                @else
                                    <img style="height: 60px;width: 60px" src="{{asset('images/logo_user.png')}}" alt="">
                                @endif
                            </td>
                                <td>
                                    @if(isset($student->subjects[0]->average_point))
                                        {{$student->subjects[0]->average_point}}
                                    @else
                                        {{__('Unfinished')}}
                                    @endif
                                </td>
                            <td>
                                <a href="{{ route('students.edit',$student->id) }}" style="background: #ffc700; color: #fff;" class="btn">{{ __('Update') }}</a>
                                <button type="button" class="btn btn-danger delete" onclick="showConfirmationModal('{{ $student->id }}')">{{ __('Delete') }}</button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td style="text-align: center" colspan="11">Không có kết quả</td>
                    </tr>
                @endif
                </tbody>
            </table>
            <div class="pagination">
                @include('layouts.pagination', ['data' => $students])
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style="justify-content: flex-end">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    {!! Form::open([ 'id' => 'form_create']) !!}
                    <div class="row mb-5">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('name', __('User Name'), ['class' => 'fw-bold']) !!}
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                <span class="text-danger username_error"></span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('full_name', __('Full Name'), ['class' => 'fw-bold']) !!}
                                {!! Form::text('full_name', null, ['class' => 'form-control']) !!}
                                <span class="text-danger full_name_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('name', __('Email'), ['class' => 'fw-bold']) !!}
                                {!! Form::email('email', null, ['class' => 'form-control']) !!}
                                <span class="text-danger email_error"></span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('gender', __('Gender'), ['class' => 'fw-bold']) !!}
                                {!! Form::select('gender', ['' => __('Choose Gender')] + collect(App\Enums\Base::toSelectArray())->map(fn($label) => __($label))->toArray(), old('gender'), ['class' => 'form-control']) !!}
                                <span class="text-danger gender_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('name', __('Faculty'), ['class' => 'fw-bold']) !!}
                                {!! Form::select('faculty', ['' => __('Choose Faculty')] + $faculties->pluck('name', 'id')->toArray(), old('faculty'), ['class' => 'form-control']) !!}
                                <span class="text-danger faculty_error"></span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('', __('Birthday'), ['class' => 'fw-bold']) !!}
                                {!! Form::date('birthday', null, ['class' => 'form-control']) !!}
                                <span class="text-danger birthday_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('', __('Address'), ['class' => 'fw-bold']) !!}
                                {!! Form::text('address', null, ['class' => 'form-control']) !!}
                                <span class="text-danger address_error"></span>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('', __('Phone'), ['class' => 'fw-bold']) !!}
                                {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                                <span class="text-danger phone_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::label('', __('Avatar'), ['class' => 'fw-bold']) !!}
                                {!! Form::file('avatar', ['class' => 'form-control']) !!}
                                <span class="text-danger avavar_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                {!! Form::submit(__('Submit'), ['class' => 'btn btn-primary']) !!}
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <div id="modalWrapper" class="modal-wrapper" onclick="hideConfirmationModal()">
        <div id="modal" class="modal1" onclick="event.stopPropagation()">
            <h4 style="margin-top: 82px;text-align: center">Bạn có chắc chắn muốn xóa?</h4>
            <div class="d-flex" style="justify-content: space-evenly;margin-top: 10px;">
                <form id="delete-form" action="{{ route('students.destroy', '__student_id') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">{{__('Delete')}}</button>
                </form>
                <button type="button" class="btn btn-secondary" onclick="hideConfirmationModal()">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/admin_student.js') }}"></script>
    <script>
        $(document).ready(function () {

            function displayAvatar(input) {
                var avatarPreview = document.getElementById('avatar-preview');
                var avatarImage = document.getElementById('avatar-image');

                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        avatarImage.src = e.target.result;
                        avatarPreview.style.display = 'block';
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
            $('#form_create').on('submit', function(event){
                event.preventDefault();
                $.ajax({
                    url: `{{route('students.store')}}`,
                    method: 'POST',
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success:function(response)
                    {
                        if(response.status){
                            location.reload();
                        }else{
                            toastr.error("Lỗi hãy kiểm tra lại!", "Lỗi", {
                                timeOut: 2000,
                                closeButton: true,
                            });
                        }
                    },
                    error: function(response) {
                        console.log(response.responseJSON.errors);
                        $('.full_name_error').text(response.responseJSON.errors.full_name || '');
                        $('.username_error').text(response.responseJSON.errors.name || '');
                        $('.email_error').text(response.responseJSON.errors.email || '');
                        $('.avavar_error').text(response.responseJSON.errors.avatar || '');
                        $('.phone_error').text(response.responseJSON.errors.phone || '');
                        $('.gender_error').text(response.responseJSON.errors.gender || '');
                        $('.birthday_error').text(response.responseJSON.errors.birthday || '');
                        $('.faculty_error').text(response.responseJSON.errors.faculty || '');
                        $('.address_error').text(response.responseJSON.errors.address || '');
                    }
                });
            });
        });
    </script>


@endsection
