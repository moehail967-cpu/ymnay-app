@extends('theme::frontend.user.dashboard.user-master')
@includeIf('loyalty-points::frontend.dashboard-widget')


@section('title') {{ __('Dashboard') }} @endsection

@section('section')

@php
    $stat_cards = [
        ['las la-box',        __('Total Orders'),    $package_orders,                              theme_user_package_orders_url(), 'var(--kv-blue)'],
        ['las la-tag',        __('Total Purchase'),  amount_with_currency_symbol($order_purchase), null,                                         'var(--kv-green)'],
        ['las la-undo-alt',   __('Refunds'),         $product_refunds,                             null,                                         'var(--kv-orange)'],
        ['las la-ticket-alt', __('Support Tickets'), $support_tickets,                             theme_user_tickets_url(),    'var(--kv-red)'],
    ];
@endphp

<div class="row g-3 mb-4">
    @foreach($stat_cards as [$icon, $label, $value, $url, $color])
    <div class="col-md-6 col-xl-3">
        @if($url)
        <a href="{{ $url }}" style="display:flex;align-items:center;gap:14px;background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:20px;box-shadow:var(--kv-shadow);text-decoration:none;height:100%;transition:box-shadow .2s;border-top:4px solid {{ $color }};"
           onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(0,0,0,.12)'" onmouseout="this.style.boxShadow='var(--kv-shadow)'">
        @else
        <div style="display:flex;align-items:center;gap:14px;background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:20px;box-shadow:var(--kv-shadow);height:100%;border-top:4px solid {{ $color }};">
        @endif
            <div style="width:52px;height:52px;border-radius:var(--kv-radius);background:var(--kv-light);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="{{ $icon }}" style="font-size:24px;color:{{ $color }};"></i>
            </div>
            <div>
                <div style="font-size:24px;font-weight:900;color:var(--kv-dark);line-height:1;">{{ $value }}</div>
                <div style="font-size:11px;color:var(--kv-muted);margin-top:4px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</div>
            </div>
        @if($url) </a> @else </div> @endif
    </div>
    @endforeach
</div>

@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div style="background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);padding:20px;box-shadow:var(--kv-shadow);">
    <div style="font-size:13px;font-weight:800;color:var(--kv-red);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <i class="las la-history"></i> {{ __('Recent Activity') }}
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:2px solid var(--kv-border);">
                    <th style="padding:8px 12px;text-align:left;color:var(--kv-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Order') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--kv-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Date') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--kv-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Amount') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--kv-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_logs as $log)
                @php $s = $log->status ?? 'pending'; @endphp
                <tr style="border-bottom:1px solid var(--kv-border);">
                    <td style="padding:10px 12px;color:var(--kv-dark);font-weight:700;">#{{ $log->id }}</td>
                    <td style="padding:10px 12px;color:var(--kv-muted);">{{ $log->created_at->format('d M Y') }}</td>
                    <td style="padding:10px 12px;color:var(--kv-red);font-weight:700;">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td style="padding:10px 12px;">
                        <span style="padding:3px 12px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;
                            background:{{ $s==='complete'||$s==='success' ? 'rgba(67,160,71,.12)' : ($s==='pending'?'rgba(251,140,0,.12)':'rgba(244,67,54,.12)') }};
                            color:{{ $s==='complete'||$s==='success' ? 'var(--kv-green)' : ($s==='pending'?'var(--kv-orange)':'var(--kv-red)') }};">
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
