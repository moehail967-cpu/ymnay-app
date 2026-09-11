@if(isset($loyalty_enabled) && $loyalty_enabled && isset($loyalty_balance))
<div class="lp-dash-widget">
    <div class="lp-dash-header">
        <i class="mdi mdi-star-circle-outline lp-dash-icon"></i>
        <span class="lp-dash-title">{{ __('Loyalty Points') }}</span>
    </div>

    <div class="lp-dash-balance-row">
        <div class="lp-dash-balance">
            <span class="lp-dash-pts">{{ number_format($loyalty_balance) }}</span>
            <span class="lp-dash-pts-label">{{ __('pts') }}</span>
        </div>
        @if(isset($loyalty_redeem_value) && $loyalty_redeem_value > 0)
        <div class="lp-dash-worth">
            ≈ {{ amount_with_currency_symbol($loyalty_redeem_value) }} {{ __('discount value') }}
        </div>
        @endif
    </div>

    @if(isset($loyalty_recent_txns) && count($loyalty_recent_txns) > 0)
    <div class="lp-dash-history">
        <div class="lp-dash-hist-title">{{ __('Recent Activity') }}</div>
        @foreach($loyalty_recent_txns as $txn)
        <div class="lp-dash-hist-row">
            <div class="lp-dash-hist-left">
                @php
                    $icon  = match($txn->type) { 'earn' => 'mdi-arrow-up-circle', 'redeem' => 'mdi-arrow-down-circle', 'expire' => 'mdi-clock-remove-outline', 'refund' => 'mdi-restore', default => 'mdi-circle-small' };
                    $color = match($txn->type) { 'earn' => '#16a34a', 'redeem' => '#dc2626', 'expire' => '#9ca3af', 'refund' => '#f59e0b', default => '#6b7280' };
                @endphp
                <i class="mdi {{ $icon }}" style="color:{{ $color }};font-size:16px;"></i>
                <span class="lp-dash-hist-note">{{ $txn->note ?? ucfirst($txn->type) }}</span>
            </div>
            <span class="lp-dash-hist-pts" style="color:{{ $color }};">
                {{ $txn->type === 'earn' || $txn->type === 'refund' ? '+' : '-' }}{{ number_format(abs($txn->points)) }} {{ __('pts') }}
            </span>
        </div>
        @endforeach
    </div>
    @endif
</div>

<style>
.lp-dash-widget{background:#fefce8;border:1.5px solid #fde68a;border-radius:12px;padding:16px;margin-bottom:20px;font-family:inherit}
.lp-dash-header{display:flex;align-items:center;gap:8px;margin-bottom:12px}
.lp-dash-icon{font-size:20px;color:#f59e0b}
.lp-dash-title{font-size:14px;font-weight:700;color:#78350f}
.lp-dash-balance-row{display:flex;align-items:center;gap:12px;margin-bottom:12px;padding-bottom:12px;border-bottom:1px solid #fde68a}
.lp-dash-balance{display:flex;align-items:baseline;gap:4px}
.lp-dash-pts{font-size:28px;font-weight:800;color:#d97706}
.lp-dash-pts-label{font-size:13px;color:#b45309;font-weight:600}
.lp-dash-worth{font-size:12px;color:#92400e;background:#fef3c7;padding:4px 10px;border-radius:20px}
.lp-dash-hist-title{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#92400e;margin-bottom:8px}
.lp-dash-hist-row{display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid #fef3c7}
.lp-dash-hist-row:last-child{border-bottom:none}
.lp-dash-hist-left{display:flex;align-items:center;gap:6px}
.lp-dash-hist-note{font-size:12px;color:#374151}
.lp-dash-hist-pts{font-size:12px;font-weight:700}
</style>
@endif
