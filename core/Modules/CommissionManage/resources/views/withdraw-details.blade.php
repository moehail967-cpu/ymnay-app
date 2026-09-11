<div class="cm-detail-grid">
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Tenant Name')}}</span>
        <span class="cm-detail-value">{{ $tenant->name ?? 'N/A' }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Tenant Email')}}</span>
        <span class="cm-detail-value">{{ $tenant->email ?? 'N/A' }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Amount')}}</span>
        <span class="cm-detail-value" style="color: var(--color-success); font-size: 1.125rem;">{{ float_amount_with_currency_symbol($withdraw->amount) }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Payment Method')}}</span>
        <span class="cm-detail-value">{{ str_replace('_', ' ', ucfirst($withdraw->payment_method)) }}</span>
    </div>
    <div class="cm-detail-row cm-detail-full">
        <span class="cm-detail-label">{{__('Account Details')}}</span>
        <span class="cm-detail-value">
            @php
                $accountDetails = is_string($withdraw->account_details)
                    ? json_decode($withdraw->account_details, true)
                    : $withdraw->account_details;
            @endphp

            @if(is_array($accountDetails))
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @foreach($accountDetails as $key => $value)
                        <li style="padding: 0.2rem 0; font-size: 0.875rem;">
                            <strong style="color: var(--color-text-muted);">{{ ucwords(str_replace('_', ' ', $key)) }}:</strong>
                            {{ is_array($value) ? json_encode($value) : $value }}
                        </li>
                    @endforeach
                </ul>
            @else
                {{ $withdraw->account_details }}
            @endif
        </span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Status')}}</span>
        <span class="cm-detail-value">
            @if($withdraw->status == 'pending')
                <span class="tw-pill tw-pill-warning">{{__('Pending')}}</span>
            @elseif($withdraw->status == 'complete')
                <span class="tw-pill tw-pill-success">{{__('Approved')}}</span>
            @else
                <span class="tw-pill tw-pill-danger">{{__('Rejected')}}</span>
            @endif
        </span>
    </div>
    @if($withdraw->transaction_id)
        <div class="cm-detail-row">
            <span class="cm-detail-label">{{__('Transaction ID')}}</span>
            <span class="cm-detail-value">{{ $withdraw->transaction_id }}</span>
        </div>
    @endif
    @if($withdraw->note)
        <div class="cm-detail-row cm-detail-full">
            <span class="cm-detail-label">{{__('Note')}}</span>
            <span class="cm-detail-value">{{ $withdraw->note }}</span>
        </div>
    @endif
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Request Date')}}</span>
        <span class="cm-detail-value">{{ $withdraw->created_at->format('d M Y, h:i A') }}</span>
    </div>
    @if($withdraw->approved_at)
        <div class="cm-detail-row">
            <span class="cm-detail-label">{{__('Processed Date')}}</span>
            <span class="cm-detail-value">{{ $withdraw->approved_at->format('d M Y, h:i A') }}</span>
        </div>
    @endif
    @if($withdraw->approved_by)
        <div class="cm-detail-row">
            <span class="cm-detail-label">{{__('Processed By')}}</span>
            <span class="cm-detail-value">{{__('Admin')}}</span>
        </div>
    @endif
</div>
