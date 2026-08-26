@extends('theme::frontend.user.dashboard.user-master')
@includeIf('loyalty-points::frontend.dashboard-widget')


@section('title') {{ __('Dashboard') }} @endsection

@section('section')

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    @foreach([
        ['mdi-package-variant-closed', __('Total Orders'),   $package_orders,                              route('tenant.user.dashboard.package.order')],
        ['mdi-currency-usd',           __('Total Purchase'),  amount_with_currency_symbol($order_purchase), 'null'],
        ['mdi-undo-variant',           __('Refunds'),         $product_refunds,                             'null'],
        ['mdi-ticket-outline',         __('Support Tickets'), $support_tickets,                             route("tenant.user.home.support.tickets")],
    ] as [$icon, $label, $value, $route])
    <div class="col-md-6 col-xl-3">
        <div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:20px;display:flex;align-items:center;gap:14px;height:100%;">
            <div style="width:48px;height:48px;border-radius:var(--dk-radius);background:rgba(229,48,48,.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="mdi {{ $icon }}" style="font-size:24px;color:var(--dk-red);"></i>
            </div>
            <div>
                <div style="font-size:22px;font-weight:900;color:var(--dk-white);line-height:1;">{{ $value }}</div>
                <div style="font-size:12px;color:var(--dk-silver);margin-top:4px;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- Recent Activity --}}
@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:20px;">
    <div style="font-weight:800;color:var(--dk-white);margin-bottom:16px;font-size:15px;text-transform:uppercase;letter-spacing:.5px;">
        <i class="mdi mdi-history" style="color:var(--dk-red);"></i> {{ __('Recent Activity') }}
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid var(--dk-border);">
                    <th style="padding:8px 12px;text-align:left;color:var(--dk-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Order') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--dk-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Date') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--dk-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Amount') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--dk-muted);font-weight:600;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_logs as $log)
                <tr style="border-bottom:1px solid var(--dk-border);">
                    <td style="padding:10px 12px;color:var(--dk-white);">#{{ $log->id }}</td>
                    <td style="padding:10px 12px;color:var(--dk-silver);">{{ $log->created_at->format('d M Y') }}</td>
                    <td style="padding:10px 12px;color:var(--dk-white);font-weight:700;">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td style="padding:10px 12px;">
                        @php $s = $log->status ?? 'pending'; @endphp
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;
                            background:{{ $s==='complete'||$s==='success' ? 'rgba(76,175,80,.15)' : ($s==='pending'?'rgba(255,193,7,.15)':'rgba(229,48,48,.15)') }};
                            color:{{ $s==='complete'||$s==='success' ? '#4CAF50' : ($s==='pending'?'#FFC107':'var(--dk-red)') }};">
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
