<div class="cm-detail-grid">
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Commission ID')}}</span>
        <span class="cm-detail-value">#{{ $commission->id }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Tenant ID')}}</span>
        <span class="cm-detail-value">{{ $commission->tenant_id }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Order UID')}}</span>
        <span class="cm-detail-value" style="color: #6366f1;">#{{ $commission->uid }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('User ID')}}</span>
        <span class="cm-detail-value">{{ $commission->user_id }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Order Total')}}</span>
        <span class="cm-detail-value">{{ site_currency_symbol() }}{{ number_format($commission->order_total, 2) }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Commission Rate')}}</span>
        <span class="cm-detail-value">{{ $commission->commission_rate }}%</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Commission Amount')}}</span>
        <span class="cm-detail-value" style="color: var(--color-success); font-size: 1.125rem;">{{ site_currency_symbol() }}{{ number_format($commission->commission_amount, 2) }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Payment Status')}}</span>
        <span class="cm-detail-value">
            @if($commission->payment_status === 'success')
                <span class="tw-pill tw-pill-success">{{__('Success')}}</span>
            @elseif($commission->payment_status === 'pending')
                <span class="tw-pill tw-pill-warning">{{__('Pending')}}</span>
            @else
                <span class="tw-pill tw-pill-danger">{{__('Cancel')}}</span>
            @endif
        </span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Status')}}</span>
        <span class="cm-detail-value">
            @if($commission->status === 'complete')
                <span class="tw-pill tw-pill-solid-success">{{__('Complete')}}</span>
            @else
                <span class="tw-pill tw-pill-gray">{{__('Incomplete')}}</span>
            @endif
        </span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Created At')}}</span>
        <span class="cm-detail-value">{{ $commission->created_at->format('d M Y, h:i A') }}</span>
    </div>
    @if($commission->updated_at)
        <div class="cm-detail-row cm-detail-full">
            <span class="cm-detail-label">{{__('Paid At')}}</span>
            <span class="cm-detail-value">{{ $commission->updated_at->format('d M Y, h:i A') }}</span>
        </div>
    @endif
</div>
