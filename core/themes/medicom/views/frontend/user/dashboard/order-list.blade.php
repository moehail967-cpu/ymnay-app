@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('My Orders') }} @endsection
@section('dash-title') {{ __('My Orders') }} @endsection

@section('dashboard-content')
<div class="mc-dash-card">
    <div class="mc-dash-card-title"><i class="las la-shopping-bag"></i> {{ __('My Orders') }}</div>
    @if(($orders ?? collect())->isNotEmpty())
    <div class="table-responsive">
        <table class="mc-dash-table">
            <thead><tr>
                <th>{{ __('Order #') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Items') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Total') }}</th>
                <th></th>
            </tr></thead>
            <tbody>
            @foreach($orders as $order)
            <tr>
                <td><strong>#{{ $order->id }}</strong></td>
                <td>{{ $order->created_at?->format('d M Y') }}</td>
                <td>{{ $order->order_items_count ?? $order->orderItems?->count() ?? '—' }}</td>
                <td>
                    <span class="mc-badge mc-badge-{{ $order->status === 'complete' ? 'success' : ($order->status === 'pending' ? 'warning' : 'muted') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td><strong>{{ amount_with_currency_symbol($order->total) }}</strong></td>
                <td>
                    <a href="{{ theme_user_order_detail_url($order->id) }}" class="mc-btn mc-btn-primary mc-btn-sm">
                        <i class="las la-eye"></i> {{ __('View') }}
                    </a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if(method_exists($orders, 'links'))
    <div style="margin-top:16px;">{{ $orders->links() }}</div>
    @endif
    @else
    <div style="text-align:center;padding:48px;">
        <i class="las la-shopping-bag" style="font-size:48px;color:#e0e0e0;display:block;margin-bottom:12px;"></i>
        <p style="color:#888;font-size:14px;margin-bottom:16px;">{{ __('You haven\'t placed any orders yet.') }}</p>
        <a href="{{ theme_shop_url() }}" class="mc-btn mc-btn-primary"><i class="las la-store"></i> {{ __('Shop Now') }}</a>
    </div>
    @endif
</div>
@endsection
