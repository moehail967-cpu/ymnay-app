@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Dashboard') }} @endsection
@section('dash-title') {{ __('Dashboard') }} @endsection

@section('dashboard-content')

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div class="mc-dash-stat">
            <i class="las la-shopping-bag"></i>
            <div class="mc-dash-stat-value">{{ $total_order ?? 0 }}</div>
            <div class="mc-dash-stat-label">{{ __('Total Orders') }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="mc-dash-stat" style="border-color:#fef3c7;">
            <i class="las la-heart" style="color:#326DE4;"></i>
            <div class="mc-dash-stat-value">{{ $total_wishlist ?? 0 }}</div>
            <div class="mc-dash-stat-label">{{ __('Wishlist') }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="mc-dash-stat" style="border-color:#dbeafe;">
            <i class="las la-headset" style="color:#3B82F6;"></i>
            <div class="mc-dash-stat-value">{{ $total_support ?? 0 }}</div>
            <div class="mc-dash-stat-label">{{ __('Support Tickets') }}</div>
        </div>
    </div>
</div>

<div class="mc-dash-card">
    <div class="mc-dash-card-title"><i class="las la-shopping-bag"></i> {{ __('Recent Orders') }}</div>
    @if(($recent_orders ?? collect())->isNotEmpty())
    <div class="table-responsive">
        <table class="mc-dash-table">
            <thead><tr>
                <th>{{ __('Order #') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Total') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($recent_orders as $order)
            <tr>
                <td><strong>#{{ $order->id }}</strong></td>
                <td>{{ $order->created_at?->format('d M Y') }}</td>
                <td>
                    <span class="mc-badge mc-badge-{{ $order->status === 'complete' ? 'success' : ($order->status === 'pending' ? 'warning' : 'muted') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td><strong>{{ amount_with_currency_symbol($order->total) }}</strong></td>
                <td>
                    <a href="{{ theme_user_order_detail_url($order->id) }}" class="mc-btn mc-btn-ghost mc-btn-sm">
                        <i class="las la-eye"></i>
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:40px;">
        <i class="las la-shopping-bag" style="font-size:48px;color:#e0e0e0;display:block;margin-bottom:12px;"></i>
        <p style="color:#888;font-size:14px;margin-bottom:16px;">{{ __('No orders yet') }}</p>
        <a href="{{ theme_shop_url() }}" class="mc-btn mc-btn-primary"><i class="las la-store"></i> {{ __('Shop Now') }}</a>
    </div>
    @endif
</div>

{!! do_action('nazmart:user_dashboard_home', auth()->user()) !!}
@endsection
