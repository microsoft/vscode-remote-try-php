@extends('layouts.app')
@section('content')
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
          xmlns="http://www.w3.org/1999/html">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <link rel="stylesheet" href="{{ asset('./css/admin_student.css') }}">
    <div class="card">
        <div class="card-body py-3">
            <a href="{{route('subjects.create')}}" type="button" style="margin-bottom: 25px" class="btn btn-success">{{__('Add')}} {{__('Subject')}}</a>
            <table class="custom-table">
                <thead>
                <tr>
                    <th>{{__('Ordinal number')}}</th>
                    <th>{{__('Name')}} {{__('Faculty')}}</th>
                    <th>{{__('Name')}} {{__('Subject')}}</th>
                    <th>{{__('Description')}}</th>
                    <th>{{__('Number of students enrolled')}}</th>
                    <th>{{__('Action')}}</th>
                </tr>
                </thead>
                <tbody>
                @if(count($subjects)> 0)
                    @foreach ($subjects as $subject)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $subject->faculty->name }}</td>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->description}}</td>
                            <td>{{$subject->count}}</td>
                            <td>
                                <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-warning">{{ __('Update') }}</a>
                                <button type="button" class="btn btn-danger delete" onclick="showConfirmationModal('{{ $subject->id }}')">{{ __('Delete') }}</button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <td style="text-align: center" colspan="5">Môn học không tồn tại</td>
                @endif
                </tbody>
            </table>
            <div class="pagination">
                @include('layouts.pagination', ['data' => $subjects])
            </div>
        </div>
    </div>

    <div id="modalWrapper" class="modal-wrapper" onclick="hideConfirmationModal()">
        <div id="modal" class="modal1" onclick="event.stopPropagation()">
            <h4 style="margin-top: 82px;text-align: center">Bạn có chắc chắn muốn xóa?</h4>
            <div class="d-flex" style="justify-content: space-evenly;margin-top: 10px;">
                <form id="delete-form" action="{{ route('subjects.destroy', '__subject_id') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger" type="submit">{{__('Delete')}}</button>
                </form>
                <button type="button" class="btn btn-secondary" onclick="hideConfirmationModal()">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>





    <script src="{{ asset('js/admin_subject.js') }}"></script>
@endsection
