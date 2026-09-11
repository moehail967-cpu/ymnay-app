@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Dashboard') }} @endsection
@section('page-title') {{ __('Dashboard') }} @endsection

@section('dashboard-content')

{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="tr-dash-stat">
            <div class="tr-dash-stat-icon">
                <i class="mdi mdi-package-variant-closed"></i>
            </div>
            <div>
                <div class="tr-dash-stat-value">{{ $total_order ?? 0 }}</div>
                <div class="tr-dash-stat-label">{{ __('Total Orders') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="tr-dash-stat">
            <div class="tr-dash-stat-icon" style="background:rgba(194,91,42,.12);">
                <i class="mdi mdi-currency-usd" style="color:var(--tr-terra);"></i>
            </div>
            <div>
                <div class="tr-dash-stat-value" style="font-size:18px;">{{ amount_with_currency_symbol($total_amount ?? 0) }}</div>
                <div class="tr-dash-stat-label">{{ __('Total Purchase') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="tr-dash-stat">
            <div class="tr-dash-stat-icon" style="background:rgba(59,47,47,.1);">
                <i class="mdi mdi-headset" style="color:var(--tr-bark);"></i>
            </div>
            <div>
                <div class="tr-dash-stat-value">{{ $support_ticket ?? 0 }}</div>
                <div class="tr-dash-stat-label">{{ __('Support Tickets') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="tr-dash-stat">
            <div class="tr-dash-stat-icon" style="background:rgba(212,169,106,.2);">
                <i class="mdi mdi-download-outline" style="color:var(--tr-sand);"></i>
            </div>
            <div>
                <div class="tr-dash-stat-value">{{ $total_download ?? 0 }}</div>
                <div class="tr-dash-stat-label">{{ __('Downloads') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Orders --}}
<div class="tr-dash-card">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-clock-outline" style="margin-right:6px;"></i>{{ __('Recent Orders') }}</span>
        <a href="{{ route('tenant.user.dashboard.package.order') }}"
           style="font-size:12px;color:var(--tr-olive);font-weight:600;text-decoration:none;">{{ __('View All') }}</a>
    </div>
    <div class="tr-dash-card-body" style="padding:0;">
        @if(isset($recent_orders) && $recent_orders->count())
        <div class="table-responsive">
            <table class="tr-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($recent_orders as $order)
                    @php
                        $sc = match($order->payment_status) {
                            'complete' => 'background:rgba(92,122,62,.15);color:var(--tr-olive);',
                            'pending'  => 'background:rgba(212,169,106,.2);color:#9a7a30;',
                            default    => 'background:rgba(194,91,42,.15);color:var(--tr-terra);'
                        };
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ theme_user_order_detail_url($order->id) }}"
                               style="color:var(--tr-olive);font-weight:700;text-decoration:none;">#{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td><span class="tr-dash-badge" style="{{ $sc }}">{{ ucfirst($order->payment_status) }}</span></td>
                        <td style="font-weight:700;color:var(--tr-bark);">{{ amount_with_currency_symbol($order->total) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding:48px;text-align:center;color:var(--tr-stone);">
            <i class="mdi mdi-package-variant" style="font-size:48px;display:block;margin-bottom:12px;color:var(--tr-border);"></i>
            {{ __('No orders yet.') }}
        </div>
        @endif
    </div>
</div>

{!! do_action('nazmart:user_dashboard_home', auth()->user()) !!}
@endsection
