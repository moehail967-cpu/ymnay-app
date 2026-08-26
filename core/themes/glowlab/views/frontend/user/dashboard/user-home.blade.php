@extends('theme::frontend.user.dashboard.user-master')
@includeIf('loyalty-points::frontend.dashboard-widget')


@section('title') {{ __('Dashboard') }} @endsection

@section('section')

@php $card_style = 'background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;display:flex;align-items:center;gap:14px;height:100%;box-shadow:var(--gl-shadow);'; @endphp

<div class="row g-3 mb-4">
    @foreach([
        ['mdi-package-variant-closed', __('Total Orders'),    $package_orders,                              theme_user_package_orders_url()],
        ['mdi-currency-usd',           __('Total Purchase'),  amount_with_currency_symbol($order_purchase), null],
        ['mdi-undo-variant',           __('Refunds'),         $product_refunds,                             null],
        ['mdi-ticket-outline',         __('Support Tickets'), $support_tickets,                             theme_user_tickets_url()],
    ] as [$icon, $label, $value, $url])
    <div class="col-md-6 col-xl-3">
        @if($url)
        <a href="{{ $url }}" style="{{ $card_style }}text-decoration:none;">
        @else
        <div style="{{ $card_style }}">
        @endif
            <div style="width:48px;height:48px;border-radius:var(--gl-radius);background:var(--gl-gold-pale);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="mdi {{ $icon }}" style="font-size:22px;color:var(--gl-gold);"></i>
            </div>
            <div>
                <div style="font-size:22px;font-weight:700;color:var(--gl-dark);line-height:1;">{{ $value }}</div>
                <div style="font-size:11px;color:var(--gl-muted);margin-top:4px;text-transform:uppercase;letter-spacing:.5px;">{{ $label }}</div>
            </div>
        @if($url) </a> @else </div> @endif
    </div>
    @endforeach
</div>

@if(isset($recent_logs) && $recent_logs->isNotEmpty())
<div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:20px;box-shadow:var(--gl-shadow);">
    <div style="font-weight:700;color:var(--gl-dark);margin-bottom:16px;font-size:13px;text-transform:uppercase;letter-spacing:1px;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-history" style="color:var(--gl-gold);"></i> {{ __('Recent Activity') }}
    </div>
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="border-bottom:1px solid var(--gl-border);">
                    <th style="padding:8px 12px;text-align:left;color:var(--gl-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Order') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--gl-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Date') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--gl-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Amount') }}</th>
                    <th style="padding:8px 12px;text-align:left;color:var(--gl-muted);font-weight:700;text-transform:uppercase;letter-spacing:.5px;font-size:11px;">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_logs as $log)
                @php $s = $log->status ?? 'pending'; @endphp
                <tr style="border-bottom:1px solid var(--gl-border);">
                    <td style="padding:10px 12px;color:var(--gl-dark);font-weight:600;">#{{ $log->id }}</td>
                    <td style="padding:10px 12px;color:var(--gl-muted);">{{ $log->created_at->format('d M Y') }}</td>
                    <td style="padding:10px 12px;color:var(--gl-dark);font-weight:700;">{{ amount_with_currency_symbol($log->total_amount ?? 0) }}</td>
                    <td style="padding:10px 12px;">
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;text-transform:uppercase;
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
