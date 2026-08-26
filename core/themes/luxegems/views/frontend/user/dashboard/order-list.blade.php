@extends(include_theme_path('user.user-master'))
@section('dash-title') {{ __('My Orders') }} @endsection

@section('dash-content')
<div class="lg-dash-card">
    <div class="lg-dash-card-title">{{ __('Order History') }}</div>
    <div style="overflow-x:auto;">
        <table class="lg-dash-table">
            <thead>
                <tr>
                    <th>{{ __('Order #') }}</th>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Items') }}</th>
                    <th>{{ __('Total') }}</th>
                    <th>{{ __('Payment') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($all_orders ?? [] as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->created_at?->format('d M Y') }}</td>
                    <td>{{ $order->orderItems?->count() ?? 0 }}</td>
                    <td class="lx-price" style="font-size:13px;">{{ amount_with_currency_symbol($order->total) }}</td>
                    <td><span style="font-size:11px;color:var(--lx-muted);">{{ ucfirst($order->payment_gateway ?? '—') }}</span></td>
                    <td>
                        <span class="lg-dash-badge lg-dash-badge-{{ strtolower($order->status ?? 'pending') }}">
                            {{ ucfirst($order->status ?? 'pending') }}
                        </span>
                    </td>
                    <td><a href="{{ theme_user_order_detail_url($order->id) }}" class="lg-dash-btn lg-dash-btn-outline" style="font-size:9px;padding:6px 12px;">{{ __('View') }}</a></td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--lx-muted);">{{ __('No orders placed yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($orders_paginator))
    <div style="margin-top:20px;">{{ $orders_paginator->links() }}</div>
    @endif
</div>
@endsection
