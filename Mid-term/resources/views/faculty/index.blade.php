@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('./css/admin_student.css') }}">
    <div class="card">
        <div class="card-body py-3">
            <a href="{{route('faculties.create')}}"   style="margin-bottom: 25px" class="btn btn-success" >{{__('Add')}} {{__('Faculty')}}</a>
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>{{__('Ordinal number')}}</th>
                        <th>{{__('Name')}}  {{__('Faculty')}}</th>
                        <th>{{__('Description')}}</th>
                        <th>{{__('Action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($faculties))
                        @foreach ($faculties as $faculty)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $faculty->name }}</td>
                                <td>{{ $faculty->description}}</td>
                                <td>
                                    <a href="{{ route('faculties.edit', ['faculty' => $faculty->id]) }}" style="background: #ffc700; color: #fff;" class="btn">{{ __('Update') }}</a>
                                    <button type="button" class="btn btn-danger delete" value="{{ $faculty->id }}" onclick="showConfirmationModal('{{ $faculty->id }}')">{{ __('Delete') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">Không có dữ liệu</td>
                        </tr>
                    @endif

                </tbody>
            </table>
            <div class="pagination">
                @include('layouts.pagination', ['data' => $faculties])
            </div>
        </div>
    </div>

    <div id="modalWrapper" class="modal-wrapper" onclick="hideConfirmationModal()">
        <div id="modal" class="modal1" onclick="event.stopPropagation()">
            <h4 style="margin-top: 82px;text-align: center">Bạn có chắc chắn muốn xóa?</h4>
            <div class="d-flex" style="justify-content: space-evenly;margin-top: 10px;">
                <form id="delete-form" action="{{ route('faculties.destroy', '__faculty_id') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">{{__('Delete')}}</button>
                </form>
                <button type="button" class="btn btn-secondary" onclick="hideConfirmationModal()">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/admin_faculty.js') }}"></script>
@endsection
