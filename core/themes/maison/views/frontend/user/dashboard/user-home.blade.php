@extends(include_theme_path('user.user-master'))
@includeIf('loyalty-points::frontend.dashboard-widget')

@section('title') {{ __('Dashboard') }} @endsection

@section('dashboard_content')

<div style="font-size:10px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:20px;">
    {{ __('Welcome back,') }} <span style="color:var(--ms-dark);">{{ auth('web')->user()?->name }}</span>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    @php
        $stats = [
            ['mdi-package-variant-closed', __('Total Orders'),   $package_orders   ?? 0, 'var(--ms-olive)'],
            ['mdi-currency-usd',           __('Total Purchase'),  amount_with_currency_symbol($order_purchase ?? 0), 'var(--ms-linen-d)'],
            ['mdi-undo-variant',           __('Refunds'),         $product_refunds  ?? 0, 'var(--ms-muted)'],
            ['mdi-ticket-outline',         __('Support Tickets'), $support_tickets  ?? 0, 'var(--ms-charcoal)'],
        ];
    @endphp
    @foreach($stats as [$icon, $label, $value, $color])
    <div class="col-sm-6 col-xl-3">
        <div class="ms-dash-card" style="display:flex;align-items:center;gap:14px;padding:20px;">
            <div style="width:48px;height:48px;border-radius:var(--ms-radius);background:var(--ms-warm);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="mdi {{ $icon }}" style="font-size:22px;color:{{ $color }};"></i>
            </div>
            <div>
                <div style="font-size:22px;font-weight:600;color:var(--ms-dark);line-height:1;">{{ $value }}</div>
                <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-top:4px;">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Orders --}}
@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div class="ms-dash-card" style="padding:0;overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid var(--ms-border);">
        <div class="ms-dash-section-title" style="margin:0;">{{ __('Recent Orders') }}</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="ms-table" style="width:100%;">
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
                    $badge_class = match(true){
                        in_array($s, ['complete','success']) => 'ms-badge-success',
                        $s === 'pending'                     => 'ms-badge-warning',
                        default                              => 'ms-badge-muted',
                    };
                @endphp
                <tr>
                    <td><strong>#{{ $log->id }}</strong></td>
                    <td>{{ $log->created_at->format('d M Y') }}</td>
                    <td>{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td><span class="ms-badge {{ $badge_class }}">{{ __($s) }}</span></td>
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
