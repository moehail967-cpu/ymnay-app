@extends(include_theme_path('user.dashboard.user-master'))
@includeIf('loyalty-points::frontend.dashboard-widget')

@section('title') {{ __('Dashboard') }} @endsection

@section('dashboard_content')

<div style="font-family:var(--sz-font-head);font-size:11px;font-weight:400;letter-spacing:1.5px;text-transform:uppercase;color:var(--sz-muted);margin-bottom:20px;">
    {{ __('Welcome back,') }} <span style="color:var(--sz-red);">{{ auth('web')->user()?->name }}</span>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @php
        $sz_stats = [
            ['mdi-package-variant-closed', __('Total Orders'),    $package_orders   ?? 0,                                         'var(--sz-red)'],
            ['mdi-currency-usd',           __('Total Purchase'),  amount_with_currency_symbol($order_purchase ?? 0),               'var(--sz-navy)'],
            ['mdi-ticket-outline',         __('Support Tickets'), $support_tickets  ?? 0,                                         'var(--sz-muted)'],
            ['mdi-download-outline',       __('Downloads'),       $download_count   ?? 0,                                         'var(--sz-navy-mid)'],
        ];
    @endphp
    @foreach($sz_stats as [$icon, $label, $value, $color])
    <div class="col-sm-6 col-xl-3">
        <div class="sz-dash-card" style="display:flex;align-items:center;gap:16px;padding:22px;">
            <div style="width:52px;height:52px;background:var(--sz-navy);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="mdi {{ $icon }}" style="font-size:24px;color:{{ $color }};"></i>
            </div>
            <div>
                <div style="font-family:var(--sz-font-head);font-size:26px;font-weight:700;color:var(--sz-dark);line-height:1;">{{ $value }}</div>
                <div style="font-family:var(--sz-font-head);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-top:4px;">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Orders --}}
@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div class="sz-dash-card" style="padding:0;overflow:hidden;">
    <div style="background:var(--sz-navy);padding:14px 20px;">
        <div class="sz-dash-section-title" style="border-bottom-color:var(--sz-red);margin-bottom:0;padding-bottom:0;color:#fff;">
            <i class="mdi mdi-clock-outline" style="margin-right:8px;"></i>{{ __('Recent Orders') }}
        </div>
    </div>
    <div style="overflow-x:auto;">
        <table class="sz-dash-table">
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
                    $bc = match(true){
                        in_array($s, ['complete','success']) => 'sz-dash-badge-success',
                        $s === 'pending'                     => 'sz-dash-badge-warning',
                        $s === 'cancel'                      => 'sz-dash-badge-danger',
                        default                              => 'sz-dash-badge-muted',
                    };
                @endphp
                <tr>
                    <td><strong style="color:var(--sz-dark);">#{{ $log->id }}</strong></td>
                    <td style="color:var(--sz-muted);">{{ $log->created_at->format('d M Y') }}</td>
                    <td><strong>{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</strong></td>
                    <td><span class="sz-dash-badge {{ $bc }}">{{ __($s) }}</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@php do_action('nazmart:user_dashboard_home', auth('web')->user()) @endphp

@endsection
