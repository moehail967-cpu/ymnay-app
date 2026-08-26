@extends(include_theme_path('user.dashboard.user-master'))
@includeIf('loyalty-points::frontend.dashboard-widget')

@section('title') {{ __('Dashboard') }} @endsection

@section('dashboard_content')

<div style="font-size:13px;color:var(--tz-muted);margin-bottom:20px;">
    {{ __('Welcome back,') }} <span style="color:var(--tz-blue);font-weight:700;">{{ auth('web')->user()?->name }}</span>
</div>

<div class="row g-3 mb-4">
    @php
        $tz_stats = [
            ['mdi-package-variant-closed', __('Total Orders'),    $package_orders  ?? 0,                                 'var(--tz-blue)'],
            ['mdi-currency-usd',           __('Total Purchase'),  amount_with_currency_symbol($order_purchase ?? 0),     'var(--tz-blue)'],
            ['mdi-ticket-outline',         __('Support Tickets'), $support_tickets ?? 0,                                 'var(--tz-blue)'],
            ['mdi-download-outline',       __('Downloads'),       $download_count  ?? 0,                                 'var(--tz-blue)'],
        ];
    @endphp
    @foreach($tz_stats as [$icon, $label, $value, $color])
    <div class="col-sm-6 col-xl-3">
        <div class="tz-dash-stat">
            <div class="tz-dash-stat-icon"><i class="mdi {{ $icon }}"></i></div>
            <div>
                <div class="tz-dash-stat-value">{{ $value }}</div>
                <div class="tz-dash-stat-label">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div class="tz-dash-card">
    <div class="tz-dash-card-header">
        <span><i class="mdi mdi-clock-outline" style="color:var(--tz-blue);margin-right:8px;"></i>{{ __('Recent Orders') }}</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="tz-dash-table">
            <thead>
                <tr>
                    <th>{{ __('Order #') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Amount') }}</th>
                    <th>{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_logs as $log)
                @php
                    $s = $log->status ?? 'pending';
                    $bc = match(true){ in_array($s,['complete','success'])=>'tz-dash-badge-success', $s==='cancel'=>'tz-dash-badge-danger', default=>'tz-dash-badge-warning' };
                @endphp
                <tr>
                    <td><strong style="color:#fff;">#{{ $log->id }}</strong></td>
                    <td>{{ $log->created_at->format('d M Y') }}</td>
                    <td><strong style="color:var(--tz-blue);">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</strong></td>
                    <td><span class="tz-dash-badge {{ $bc }}">{{ __($s) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@php do_action('nazmart:user_dashboard_home', auth('web')->user()) @endphp
@endsection
