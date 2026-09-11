@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Dashboard') }} @endsection
@section('page-title') {{ __('Dashboard') }} @endsection

@section('dashboard-content')

{{-- Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="fp-stat-card">
            <div class="fp-stat-icon"><i class="mdi mdi-package-variant-closed"></i></div>
            <div>
                <div class="fp-stat-value">{{ $total_order ?? 0 }}</div>
                <div class="fp-stat-label">{{ __('Orders') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="fp-stat-card">
            <div class="fp-stat-icon"><i class="mdi mdi-heart-outline"></i></div>
            <div>
                <div class="fp-stat-value">{{ $total_wishlist ?? 0 }}</div>
                <div class="fp-stat-label">{{ __('Wishlist') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="fp-stat-card">
            <div class="fp-stat-icon"><i class="mdi mdi-download-outline"></i></div>
            <div>
                <div class="fp-stat-value">{{ $total_download ?? 0 }}</div>
                <div class="fp-stat-label">{{ __('Downloads') }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="fp-stat-card">
            <div class="fp-stat-icon"><i class="mdi mdi-headset"></i></div>
            <div>
                <div class="fp-stat-value">{{ $total_ticket ?? 0 }}</div>
                <div class="fp-stat-label">{{ __('Tickets') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Recent Orders --}}
<div class="fp-dash-card">
    <div class="fp-dash-card-header">
        {{ __('Recent Orders') }}
        <a href="{{ theme_user_package_orders_url() }}" style="font-size:12px;color:var(--fp-green);">{{ __('View All') }}</a>
    </div>
    <div class="fp-dash-card-body" style="padding:0;">
        @if(isset($recent_orders) && $recent_orders->count())
        <div class="table-responsive">
            <table class="fp-dash-table">
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
                    <tr>
                        <td><a href="{{ theme_user_order_detail_url($order->id) }}" style="color:var(--fp-green);font-family:var(--fp-font-head);font-weight:700;">#{{ $order->id }}</a></td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            @php
                                $sc = match($order->payment_status) { 'complete' => 'background:rgba(0,255,65,.15);color:var(--fp-green);border:1px solid var(--fp-green);', 'pending' => 'background:rgba(255,193,7,.1);color:#ffc107;border:1px solid #ffc107;', default => 'background:rgba(255,77,77,.1);color:#ff4d4d;border:1px solid #ff4d4d;' };
                            @endphp
                            <span class="fp-dash-badge" style="{{ $sc }}">{{ ucfirst($order->payment_status) }}</span>
                        </td>
                        <td style="color:var(--fp-green);font-family:var(--fp-font-head);font-weight:700;">{{ amount_with_currency_symbol($order->total) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @else
            <div style="padding:40px;text-align:center;color:var(--fp-muted);">
                <i class="mdi mdi-package-variant" style="font-size:48px;color:var(--fp-border);display:block;margin-bottom:12px;"></i>
                {{ __('No orders yet.') }}
            </div>
        @endif
    </div>
</div>

{{!! do_action('nazmart:user_dashboard_home', auth()->user()) !!}}
@endsection
