@extends(theme_view('frontend.user.user-master'))
@section('dash-title') {{ __('Order Details') }} @endsection

@section('dash-content')
<div class="lg-dash-card mb-4">
    <div class="lg-dash-card-title">{{ __('Order') }} #{{ $order->id }}</div>
    <div class="row g-3 mb-4" style="font-size:13px;color:var(--lx-muted);">
        <div class="col-sm-4">
            <div style="color:var(--lx-gold);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:4px;">{{ __('Date') }}</div>
            {{ $order->created_at?->format('d M Y, H:i') }}
        </div>
        <div class="col-sm-4">
            <div style="color:var(--lx-gold);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:4px;">{{ __('Status') }}</div>
            <span class="lg-dash-badge lg-dash-badge-{{ strtolower($order->status ?? 'pending') }}">{{ ucfirst($order->status ?? 'pending') }}</span>
        </div>
        <div class="col-sm-4">
            <div style="color:var(--lx-gold);font-size:10px;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:4px;">{{ __('Payment') }}</div>
            {{ ucfirst($order->payment_gateway ?? '—') }}
        </div>
    </div>

    <table class="lg-dash-table">
        <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th>{{ __('Qty') }}</th>
                <th>{{ __('Unit Price') }}</th>
                <th>{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @php $order_items = json_decode($order->order_details ?? '[]') ?? []; @endphp
            @foreach($order_items as $item)
            <tr>
                <td>{{ $item->name ?? '' }}</td>
                <td>{{ $item->qty ?? 1 }}</td>
                <td>{{ amount_with_currency_symbol($item->price ?? 0) }}</td>
                <td class="lx-price" style="font-size:13px;">{{ amount_with_currency_symbol(($item->price ?? 0) * ($item->qty ?? 1)) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;color:var(--lx-muted);font-size:11px;letter-spacing:1px;">{{ __('Total') }}</td>
                <td class="lx-price" style="font-size:15px;">{{ amount_with_currency_symbol($order->total) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="lg-dash-card">
            <div class="lg-dash-card-title">{{ __('Shipping Address') }}</div>
            <div style="font-size:13px;color:var(--lx-muted);line-height:1.8;">
                <div>{{ $order->name }}</div>
                <div>{{ $order->address }}</div>
                <div>{{ $order->city }}{{ $order->state ? ', '.$order->state : '' }}</div>
                <div>{{ $order->country ?? '' }} {{ $order->postal_code ?? '' }}</div>
                <div>{{ $order->phone }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="lg-dash-card">
            <div class="lg-dash-card-title">{{ __('Summary') }}</div>
            @php $order_meta = json_decode($order->payment_meta ?? '{}'); @endphp
            <div style="font-size:13px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--lx-border);color:var(--lx-muted);">
                    <span>{{ __('Subtotal') }}</span>
                    <span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--lx-border);color:var(--lx-muted);">
                    <span>{{ __('Shipping') }}</span>
                    <span>{{ amount_with_currency_symbol($order_meta->shipping_cost ?? 0) }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:10px 0;">
                    <span class="lx-price" style="font-size:14px;">{{ __('Total') }}</span>
                    <span class="lx-price" style="font-size:14px;">{{ amount_with_currency_symbol($order->total) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('tenant.user.dashboard.package.order') }}" class="lg-dash-btn lg-dash-btn-outline">
        <i class="las la-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>
@endsection
