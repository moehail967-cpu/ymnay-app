@extends('theme::frontend.user.dashboard.user-master')
@includeIf('loyalty-points::frontend.dashboard-widget')

@section('title') {{ __('Dashboard') }} @endsection

@section('section')

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([
        ['mdi-package-variant-closed', __('Total Orders'),    $package_orders,                              theme_user_package_orders_url()],
        ['mdi-currency-usd',           __('Total Purchase'),  amount_with_currency_symbol($order_purchase), null],
        ['mdi-undo-variant',           __('Refunds'),         $product_refunds,                             null],
        ['mdi-ticket-outline',         __('Support Tickets'), $support_tickets,                             theme_user_tickets_url()],
    ] as [$icon, $label, $value, $route])
    <div class="col-md-6 col-xl-3">
        <div class="ar-stat-card">
            <div class="ar-stat-icon"><i class="mdi {{ $icon }}"></i></div>
            <div>
                <div class="ar-stat-value">{{ $value }}</div>
                <div class="ar-stat-label">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Activity --}}
@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div class="ar-dash-box">
    <div style="padding:20px 20px 0;">
        <div class="ar-dash-section-title">
            <i class="mdi mdi-history" style="color:var(--ar-red);"></i> {{ __('Recent Activity') }}
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="ar-dash-table">
            <thead>
                <tr>
                    <th>{{ __('Order') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_logs as $log)
                @php $s = $log->status ?? 'pending'; @endphp
                <tr>
                    <td style="font-weight:700;">#{{ $log->id }}</td>
                    <td style="color:var(--ar-muted);">{{ $log->created_at->format('d M Y') }}</td>
                    <td style="font-weight:700;color:var(--ar-red);">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td>
                        <span class="ar-status-badge" style="
                            background:{{ $s==='complete'||$s==='success' ? 'rgba(76,175,80,.12)' : ($s==='pending'?'rgba(255,193,7,.12)':'rgba(248,58,38,.12)') }};
                            color:{{ $s==='complete'||$s==='success' ? '#4CAF50' : ($s==='pending'?'#D4A017':'var(--ar-red)') }};">
                            {{ __($s) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@php do_action('nazmart:user_dashboard_home', auth('web')->user()) @endphp

@endsection
