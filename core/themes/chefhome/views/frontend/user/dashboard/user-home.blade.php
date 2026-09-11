@extends('theme::frontend.user.dashboard.user-master')
@includeIf('loyalty-points::frontend.dashboard-widget')


@section('title') {{ __('Dashboard') }} @endsection

@section('section')

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    @foreach([
        ['las la-shopping-bag',  __('Total Orders'),    $package_orders,                              route('tenant.user.dashboard.package.order')],
        ['las la-money-bill',    __('Total Purchase'),   amount_with_currency_symbol($order_purchase), 'null'],
        ['las la-undo-alt',      __('Refunds'),          $product_refunds,                             'null'],
        ['las la-ticket-alt',    __('Support Tickets'),  $support_tickets,                             route("tenant.user.home.support.tickets")],
    ] as [$icon, $label, $value, $route])
    <div class="col-md-6 col-xl-3">
        <div class="ch-stat-card">
            <div class="ch-stat-icon"><i class="{{ $icon }}"></i></div>
            <div>
                <div class="ch-stat-value">{{ $value }}</div>
                <div class="ch-stat-label">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Activity --}}
@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div class="ch-dash-card">
    <div class="ch-dash-card-header">
        <span><i class="las la-history" style="color:var(--ch-red);margin-right:6px;"></i> {{ __('Recent Activity') }}</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="ch-dash-table">
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
                    <td style="color:var(--ch-muted);">{{ $log->created_at->format('d M Y') }}</td>
                    <td style="font-weight:700;">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td>
                        <span class="ch-dash-badge" style="
                            background:{{ $s==='complete'||$s==='success' ? 'rgba(39,174,96,.12)' : ($s==='pending'?'rgba(243,156,18,.12)':'rgba(192,57,43,.12)') }};
                            color:{{ $s==='complete'||$s==='success' ? 'var(--ch-green)' : ($s==='pending'?'var(--ch-amber)':'var(--ch-red)') }};">
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

{{-- Plugin dashboard widgets --}}
@php do_action('nazmart:user_dashboard_home', auth('web')->user()) @endphp

@endsection
