@extends(include_theme_path('user.user-master'))

@section('title') {{ __('My Orders') }} @endsection
@section('dash-title') {{ __('My Orders') }} @endsection

@section('dashboard-content')
<div class="ph-dash-card">
    <div class="ph-dash-card-title"><i class="las la-shopping-bag"></i> {{ __('My Orders') }}</div>
    @if(($orders ?? collect())->isNotEmpty())
    <div class="table-responsive">
        <table class="ph-dash-table">
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
                <td><span class="ph-badge-pill ph-badge-{{ $order->status === 'complete' ? 'success' : ($order->status === 'pending' ? 'warning' : 'muted') }}">{{ ucfirst($order->status) }}</span></td>
                <td><strong>{{ amount_with_currency_symbol($order->total) }}</strong></td>
                <td><a href="{{ theme_user_order_detail_url($order->id) }}" class="ph-btn ph-btn-terra ph-btn-sm"><i class="las la-eye"></i> {{ __('View') }}</a></td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if(method_exists($orders, 'links'))
    <div class="ph-pagination mt-4">{{ $orders->links() }}</div>
    @endif
    @else
    <div style="text-align:center;padding:48px;">
        <i class="las la-shopping-bag" style="font-size:48px;color:var(--ph-border);display:block;margin-bottom:12px;"></i>
        <p style="color:var(--ph-muted);font-size:14px;margin-bottom:16px;">{{ __('You haven\'t placed any orders yet.') }}</p>
        <a href="{{ theme_shop_url() }}" class="ph-btn ph-btn-terra"><i class="las la-store"></i> {{ __('Shop Now') }}</a>
    </div>
    @endif
</div>
@endsection
