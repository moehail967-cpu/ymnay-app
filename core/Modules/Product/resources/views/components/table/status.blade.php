@props(['statuses', 'statusId', 'id'])

<div class="product-status-dropdown" data-id="{{$id}}">
    <button type="button" class="status-trigger
        @if($statusId == 1) bg-success-soft text-success
        @elseif($statusId == 2) bg-warning-soft text-warning
        @else bg-info-soft text-info @endif"
        onclick="toggleStatusMenu(this)">
        <i class="mdi @if($statusId == 1) mdi-check-circle @elseif($statusId == 2) mdi-pencil-outline @else mdi-clock-outline @endif text-[10px]"></i>
        <span class="status-text">
            @foreach($statuses as $status)
                @if($status->id == $statusId) {{ $status->name }} @endif
            @endforeach
        </span>
        <i class="mdi mdi-chevron-down text-[10px]"></i>
    </button>
    <div class="status-menu">
        @foreach($statuses as $status)
            <button type="button" class="status-option" data-id="{{$id}}" data-status-id="{{$status->id}}">
                <span class="w-1.5 h-1.5 rounded-full
                    @if($status->id == 1) bg-success
                    @elseif($status->id == 2) bg-warning
                    @else bg-info @endif"></span>
                {{ $status->name }}
            </button>
        @endforeach
    </div>
</div>
