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
        <div class="fn-stat-card">
            <div class="fn-stat-icon"><i class="las {{ $icon }}"></i></div>
            <div>
                <div class="fn-stat-value">{{ $value }}</div>
                <div class="fn-stat-label">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Activity --}}
@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div class="fn-dash-box">
    <div class="fn-dash-box-header">
        <div class="fn-dash-section-title">
            <i class="las la-history"></i> {{ __('Recent Activity') }}
        </div>
    </div>
    <div class="table-responsive">
        <table class="fn-dash-table">
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
                    <td class="fn-fw-bold">#{{ $log->id }}</td>
                    <td class="fn-muted">{{ $log->created_at->format('d M Y') }}</td>
                    <td class="fn-fw-bold fn-accent-text">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td>
                        <span class="fn-status-badge {{ $s==='complete'||$s==='success' ? 'fn-badge-success' : ($s==='pending' ? 'fn-badge-warn' : 'fn-badge-danger') }}">
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
