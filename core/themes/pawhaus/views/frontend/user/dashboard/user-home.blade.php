@extends(include_theme_path('user.user-master'))

@section('title') {{ __('Dashboard') }} @endsection
@section('dash-title') {{ __('Dashboard') }} @endsection

@section('dashboard-content')

<div class="row g-3 mb-4">
    <div class="col-sm-4">
        <div style="background:var(--ph-terra-light);border:2px solid var(--ph-border);border-radius:var(--ph-radius);padding:20px;text-align:center;">
            <i class="las la-shopping-bag" style="font-size:36px;color:var(--ph-terra);display:block;margin-bottom:8px;"></i>
            <div style="font-size:26px;font-weight:800;color:var(--ph-dark);">{{ $total_order ?? 0 }}</div>
            <div style="font-size:12px;color:var(--ph-muted);font-weight:700;">{{ __('Total Orders') }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div style="background:var(--ph-sage-light);border:2px solid var(--ph-border);border-radius:var(--ph-radius);padding:20px;text-align:center;">
            <i class="las la-heart" style="font-size:36px;color:var(--ph-sage);display:block;margin-bottom:8px;"></i>
            <div style="font-size:26px;font-weight:800;color:var(--ph-dark);">{{ $total_wishlist ?? 0 }}</div>
            <div style="font-size:12px;color:var(--ph-muted);font-weight:700;">{{ __('Wishlist') }}</div>
        </div>
    </div>
    <div class="col-sm-4">
        <div style="background:var(--ph-cream);border:2px solid var(--ph-border);border-radius:var(--ph-radius);padding:20px;text-align:center;">
            <i class="las la-headset" style="font-size:36px;color:var(--ph-terra);display:block;margin-bottom:8px;"></i>
            <div style="font-size:26px;font-weight:800;color:var(--ph-dark);">{{ $total_support ?? 0 }}</div>
            <div style="font-size:12px;color:var(--ph-muted);font-weight:700;">{{ __('Support Tickets') }}</div>
        </div>
    </div>
</div>

<div class="ph-dash-card">
    <div class="ph-dash-card-title"><i class="las la-shopping-bag"></i> {{ __('Recent Orders') }}</div>
    @if(($recent_orders ?? collect())->isNotEmpty())
    <div class="table-responsive">
        <table class="ph-dash-table">
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
                <td><span class="ph-badge-pill ph-badge-{{ $order->status === 'complete' ? 'success' : ($order->status === 'pending' ? 'warning' : 'muted') }}">{{ ucfirst($order->status) }}</span></td>
                <td><strong>{{ amount_with_currency_symbol($order->total) }}</strong></td>
                <td><a href="{{ theme_user_order_detail_url($order->id) }}" class="ph-btn ph-btn-ghost ph-btn-sm"><i class="las la-eye"></i></a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align:center;padding:32px;"><i class="las la-shopping-bag" style="font-size:40px;color:var(--ph-border);display:block;margin-bottom:10px;"></i><p style="color:var(--ph-muted);">{{ __('No orders yet') }}</p></div>
    @endif
</div>

{!! do_action('nazmart:user_dashboard_home', auth()->user()) !!}
@endsection
