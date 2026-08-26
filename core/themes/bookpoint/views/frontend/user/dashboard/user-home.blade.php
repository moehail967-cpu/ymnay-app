@extends('theme::frontend.user.dashboard.user-master')
@includeIf('loyalty-points::frontend.dashboard-widget')

@section('title') {{ __('Dashboard') }} @endsection

@section('section')

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([
        ['la-shopping-bag',   __('Total Orders'),    $package_orders,                              theme_user_package_orders_url()],
        ['la-dollar-sign',    __('Total Purchase'),  amount_with_currency_symbol($order_purchase), null],
        ['la-undo-alt',       __('Refunds'),         $product_refunds,                             null],
        ['la-ticket-alt',     __('Support Tickets'), $support_tickets,                             theme_user_tickets_url()],
    ] as [$icon, $label, $value, $route])
    <div class="col-md-6 col-xl-3">
        <div class="bp-stat-card">
            <div class="bp-stat-icon"><i class="las {{ $icon }}"></i></div>
            <div>
                <div class="bp-stat-value">{{ $value }}</div>
                <div class="bp-stat-label">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Activity --}}
@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div class="bp-dash-box">
    <div class="bp-dash-box-header">
        <div class="bp-dash-section-title">
            <i class="las la-history"></i> {{ __('Recent Activity') }}
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="bp-dash-table">
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
                    <td class="bp-fw-bold">#{{ $log->id }}</td>
                    <td class="bp-muted">{{ $log->created_at->format('d M Y') }}</td>
                    <td class="bp-fw-bold bp-accent-text">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td>
                        <span class="bp-status-badge {{ $s==='complete'||$s==='success' ? 'bp-badge-success' : ($s==='pending' ? 'bp-badge-warn' : 'bp-badge-danger') }}">
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
