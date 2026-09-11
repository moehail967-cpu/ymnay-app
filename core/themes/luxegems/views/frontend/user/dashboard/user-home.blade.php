@extends(include_theme_path('user.user-master'))
@section('dash-title') {{ __('Dashboard') }} @endsection

@section('dash-content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="lg-dash-card" style="text-align:center;">
            <i class="las la-box" style="font-size:28px;color:var(--lx-gold);display:block;margin-bottom:8px;"></i>
            <div style="font-size:24px;font-weight:200;color:var(--lx-white);">{{ $order_count ?? 0 }}</div>
            <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--lx-muted);margin-top:4px;">{{ __('Total Orders') }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="lg-dash-card" style="text-align:center;">
            <i class="las la-heart" style="font-size:28px;color:var(--lx-gold);display:block;margin-bottom:8px;"></i>
            <div style="font-size:24px;font-weight:200;color:var(--lx-white);">{{ $wishlist_count ?? 0 }}</div>
            <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--lx-muted);margin-top:4px;">{{ __('Wishlist') }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="lg-dash-card" style="text-align:center;">
            <i class="las la-download" style="font-size:28px;color:var(--lx-gold);display:block;margin-bottom:8px;"></i>
            <div style="font-size:24px;font-weight:200;color:var(--lx-white);">{{ $download_count ?? 0 }}</div>
            <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--lx-muted);margin-top:4px;">{{ __('Downloads') }}</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="lg-dash-card" style="text-align:center;">
            <i class="las la-headset" style="font-size:28px;color:var(--lx-gold);display:block;margin-bottom:8px;"></i>
            <div style="font-size:24px;font-weight:200;color:var(--lx-white);">{{ $ticket_count ?? 0 }}</div>
            <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--lx-muted);margin-top:4px;">{{ __('Tickets') }}</div>
        </div>
    </div>
</div>

<div class="lg-dash-card">
    <div class="lg-dash-card-title">{{ __('Recent Orders') }}</div>
    <div style="overflow-x:auto;">
        <table class="lg-dash-table">
            <thead>
                <tr>
                    <th>{{ __('Order #') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_orders ?? [] as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->created_at?->format('d M Y') }}</td>
                    <td class="lx-price" style="font-size:13px;">{{ amount_with_currency_symbol($order->total) }}</td>
                    <td>
                        <span class="lg-dash-badge lg-dash-badge-{{ strtolower($order->status ?? 'pending') }}">
                            {{ ucfirst($order->status ?? 'pending') }}
                        </span>
                    </td>
                    <td><a href="{{ theme_user_order_detail_url($order->id) }}" class="lg-dash-btn lg-dash-btn-outline" style="font-size:9px;padding:6px 12px;">{{ __('View') }}</a></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--lx-muted);">{{ __('No orders yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{!! do_action('nazmart:user_dashboard_home', auth()->user()) !!}
@endsection
