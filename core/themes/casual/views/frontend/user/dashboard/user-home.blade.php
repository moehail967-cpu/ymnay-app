@extends('theme::frontend.user.dashboard.user-master')
@includeIf('loyalty-points::frontend.dashboard-widget')

@section('title') {{ __('Dashboard') }} @endsection

@section('section')

{{-- Stats --}}
<div class="row g-3 mb-4">
    @foreach([
        ['las la-shopping-bag',    __('Total Orders'),   $package_orders,                              theme_user_package_orders_url()],
        ['las la-coins',           __('Total Purchase'),  amount_with_currency_symbol($order_purchase), null],
        ['las la-undo-alt',        __('Refunds'),         $product_refunds,                             null],
        ['las la-ticket-alt',      __('Support Tickets'), $support_tickets,                             theme_user_tickets_url()],
    ] as [$icon, $label, $value, $url])
    <div class="col-md-6 col-xl-3">
        <div class="cs-stat-card">
            <div class="cs-stat-icon"><i class="{{ $icon }}"></i></div>
            <div>
                <div class="cs-stat-value">{{ $value }}</div>
                <div class="cs-stat-label">{{ $label }}</div>
            </div>
            @if($url)
            <a href="{{ $url }}" class="cs-stat-link"><i class="las la-angle-right"></i></a>
            @endif
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Activity --}}
@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div class="cs-dash-box">
    <div class="cs-dash-box-head">
        <i class="las la-history"></i> {{ __('Recent Orders') }}
    </div>
    <div class="cs-dash-table-wrap">
        <table class="cs-dash-table">
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
                    <td class="cs-dash-td-bold">#{{ $log->id }}</td>
                    <td class="cs-dash-td-muted">{{ $log->created_at->format('d M Y') }}</td>
                    <td class="cs-dash-td-price">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td>
                        <span class="cs-dash-badge cs-dash-badge-{{ in_array($s, ['complete','success']) ? 'success' : ($s === 'cancel' ? 'danger' : 'warning') }}">
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
