@php
    $currentStatus = $statuses?->where("id", $statusId ?? 2)?->first();
    $statusName = $currentStatus?->name ?? 'Draft';
    $colorClass = $statusName === 'Publish' ? 'bg-success-soft text-success' : ($statusName === 'Draft' ? 'bg-warning-soft text-warning' : 'bg-info-soft text-info');
@endphp
<div class="product-status-dropdown">
    <button type="button" class="status-trigger {{ $colorClass }}" onclick="toggleStatusMenu(this)">
        {{ $statusName }}
        <i class="mdi mdi-chevron-down text-[10px]"></i>
    </button>
    <div class="status-menu">
        @foreach($statuses as $status)
            <button type="button" class="status-option" data-id="{{ $id }}" data-status-id="{{ $status->id }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $status->name === 'Publish' ? 'bg-success' : 'bg-warning' }}"></span>
                {{ $status->name }}
            </button>
        @endforeach
    </div>
</div>
