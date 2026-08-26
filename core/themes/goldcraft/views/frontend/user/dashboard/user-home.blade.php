@extends('theme::frontend.user.dashboard.user-master')
@includeIf('loyalty-points::frontend.dashboard-widget')


@section('title') {{ __('Dashboard') }} @endsection

@section('section')

@php
    $card_style = 'background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;display:flex;align-items:center;gap:14px;height:100%;box-shadow:var(--gc-shadow);text-decoration:none;';
@endphp

<div class="row g-3 mb-4">
    @foreach([
        ['las la-box',          __('Total Orders'),    $package_orders,                              theme_user_package_orders_url(), 'var(--gc-rose)'],
        ['las la-gem',          __('Total Purchase'),  amount_with_currency_symbol($order_purchase), null,                                         'var(--gc-gold)'],
        ['las la-undo-alt',     __('Refunds'),         $product_refunds,                             null,                                         'var(--gc-brown)'],
        ['las la-ticket-alt',   __('Support Tickets'), $support_tickets,                             theme_user_tickets_url(),    'var(--gc-rose)'],
    ] as [$icon, $label, $value, $url, $color])
    <div class="col-md-6 col-xl-3">
        @if($url)
        <a href="{{ $url }}" style="{{ $card_style }}">
        @else
        <div style="{{ $card_style }}">
        @endif
            <div style="width:48px;height:48px;border-radius:var(--gc-radius);background:var(--gc-warm);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="{{ $icon }}" style="font-size:22px;color:{{ $color }};"></i>
            </div>
            <div>
                <div style="font-size:22px;font-weight:400;color:var(--gc-dark);line-height:1;font-family:Georgia,serif;font-style:italic;">{{ $value }}</div>
                <div style="font-size:10px;color:var(--gc-muted);margin-top:4px;text-transform:uppercase;letter-spacing:1px;">{{ $label }}</div>
            </div>
        @if($url) </a> @else </div> @endif
    </div>
    @endforeach
</div>

@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:20px;box-shadow:var(--gc-shadow);">
    <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;display:flex;align-items:center;gap:8px;">
        <i class="las la-history"></i> {{ __('Recent Activity') }}
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;font-family:Georgia,serif;">
            <thead>
                <tr style="border-bottom:1px solid var(--gc-border);">
                    <th style="padding:8px 12px;text-align:left;color:var(--gc-muted);font-weight:400;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Order') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--gc-muted);font-weight:400;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Date') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--gc-muted);font-weight:400;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Amount') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--gc-muted);font-weight:400;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_logs as $log)
                @php $s = $log->status ?? 'pending'; @endphp
                <tr style="border-bottom:1px solid var(--gc-border);">
                    <td style="padding:10px 12px;color:var(--gc-dark);font-style:italic;">#{{ $log->id }}</td>
                    <td style="padding:10px 12px;color:var(--gc-muted);font-style:italic;">{{ $log->created_at->format('d M Y') }}</td>
                    <td style="padding:10px 12px;color:var(--gc-rose);font-style:italic;">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td style="padding:10px 12px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:400;text-transform:uppercase;letter-spacing:.5px;
                            background:{{ $s==='complete'||$s==='success' ? 'rgba(72,187,120,.12)' : ($s==='pending'?'rgba(245,158,11,.12)':'rgba(229,62,62,.12)') }};
                            color:{{ $s==='complete'||$s==='success' ? '#38a169' : ($s==='pending'?'#d97706':'#c53030') }};">
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
