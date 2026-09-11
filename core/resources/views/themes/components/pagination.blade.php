<div class="pagination mt-60 mt-5">
    <ul class="pagination-list">
        @if(count($links) > 1)
            @foreach(generateDynamicPagination($current_page, count($links)) as $index => $link)
                @if($link === '...')
                    <li>
                        <a data-page="{{ $current_page }}" href="{{ $current_page }}" class="page-number"
                           style="align-items: end">{{ $link }}</a>
                    </li>
                @else
                    <li>
                        <a data-page="{{ $link }}" href="{{ $links[$link] }}"
                           class="page-number {{ $link === $current_page ? "current" : ""}}">{{ $link }}</a>
                    </li>
                @endif
            @endforeach
        @endif
    </ul>
</div>
