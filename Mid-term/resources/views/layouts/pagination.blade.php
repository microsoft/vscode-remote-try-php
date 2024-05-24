@if ($data->lastPage() > 1)
    <ul class="pagination">
        <li class="{{ ($data->currentPage() == 1)  ? 'active' : ''  }}">
            <a href="{{ $data->url(1) }}">1</a>
        </li>
        @for ($i = 2; $i <= $data->lastPage(); $i++)
            <li class="{{ ($data->currentPage() == $i) ? 'active' : '' }}">
                <a href="{{ $data->url($i) }}">{{ $i }}</a>
            </li>
        @endfor
    </ul>
@endif
