<div class="cm-detail-grid">
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Transaction ID')}}</span>
        <span class="cm-detail-value">{{ $paymentLog->transaction_id ?? 'N/A' }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('User')}}</span>
        <span class="cm-detail-value">
            {{ $paymentLog->user->name }}
            <span style="display:block; font-size: 0.75rem; color: var(--color-text-muted);">ID: {{ $paymentLog->user_id }}</span>
        </span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Amount')}}</span>
        <span class="cm-detail-value" style="color: var(--color-success); font-size: 1.125rem;">{{ site_currency_symbol() }}{{ number_format($paymentLog->amount, 2) }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Payment Gateway')}}</span>
        <span class="cm-detail-value">{{ $paymentLog->payment_gateway ?? 'N/A' }}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Payment Status')}}</span>
        <span class="cm-detail-value">{!! $paymentLog->statusBadge !!}</span>
    </div>
    <div class="cm-detail-row">
        <span class="cm-detail-label">{{__('Payment Date')}}</span>
        <span class="cm-detail-value">{{ $paymentLog->payment_date ? $paymentLog->payment_date->format('d-m-Y H:i:s') : 'N/A' }}</span>
    </div>
</div>

@if(!empty($commissions))
    <div style="margin-top: 1.5rem;">
        <h6 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--color-text-muted); margin-bottom: 0.75rem; padding: 0 1rem;">
            {{__('Associated Commissions')}}
        </h6>
        <div class="tw-table-wrap">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-main">
                        <th class="px-4 py-2 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('ID')}}</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Order UID')}}</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Amount')}}</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Rate')}}</th>
                        <th class="px-4 py-2 text-[10px] font-bold text-muted uppercase tracking-widest">{{__('Status')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commissions as $commission)
                        <tr class="border-b border-subtle">
                            <td class="px-4 py-2 text-sm font-semibold text-dark">#{{ $commission->id }}</td>
                            <td class="px-4 py-2 text-sm text-dark">{{ $commission->uid }}</td>
                            <td class="px-4 py-2 text-sm font-bold text-success">{{ site_currency_symbol() }}{{ number_format($commission->commission_amount, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-dark">{{ $commission->commission_rate }}%</td>
                            <td class="px-4 py-2">{!! $commission->statusBadge !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
