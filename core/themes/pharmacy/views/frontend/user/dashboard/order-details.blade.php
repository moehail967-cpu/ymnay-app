@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Order Details') }} @endsection
@section('dash-title') {{ __('Order Details') }} @endsection

@section('dashboard-content')

<div class="pf-dash-card">
    <div class="pf-dash-card-title"><i class="las la-receipt"></i> {{ __('Order') }} #{{ $order->id }}</div>

    <div class="row g-3 mb-4">
        <div class="col-sm-3">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--pf-muted);margin-bottom:4px;">{{ __('Date') }}</div>
            <div style="font-size:14px;font-weight:700;color:var(--pf-dark);">{{ $order->created_at?->format('d M Y') }}</div>
        </div>
        <div class="col-sm-3">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--pf-muted);margin-bottom:4px;">{{ __('Status') }}</div>
            <span class="pf-badge-pill pf-badge-{{ ($order->status === 'complete') ? 'success' : (($order->status === 'pending') ? 'warning' : 'muted') }}">{{ ucfirst($order->status ?? '') }}</span>
        </div>
        <div class="col-sm-3">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--pf-muted);margin-bottom:4px;">{{ __('Payment') }}</div>
            <div style="font-size:14px;font-weight:700;color:var(--pf-dark);">{{ ucfirst($order->payment_status ?? '') }}</div>
        </div>
        <div class="col-sm-3">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--pf-muted);margin-bottom:4px;">{{ __('Total') }}</div>
            <div style="font-size:16px;font-weight:800;color:var(--pf-teal);">{{ amount_with_currency_symbol($order->total ?? 0) }}</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="pf-dash-table">
            <thead><tr>
                <th>{{ __('Product') }}</th>
                <th>{{ __('Qty') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Subtotal') }}</th>
            </tr></thead>
            <tbody>
            @php $order_items = json_decode($order->order_details ?? '[]') ?? []; @endphp
            @foreach($order_items as $item)
            <tr>
                <td><strong>{{ $item->name ?? '' }}</strong></td>
                <td>{{ $item->qty ?? 1 }}</td>
                <td>{{ amount_with_currency_symbol($item->price ?? 0) }}</td>
                <td>{{ amount_with_currency_symbol(($item->price ?? 0) * ($item->qty ?? 1)) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    @php $order_meta = json_decode($order->payment_meta ?? '{}'); @endphp
    <div style="text-align:right;margin-top:16px;padding-top:16px;border-top:1.5px solid var(--pf-border);">
        <div style="display:inline-flex;flex-direction:column;gap:8px;min-width:200px;">
            <div class="d-flex justify-content-between gap-4" style="font-size:13px;color:var(--pf-muted);">
                <span>{{ __('Subtotal') }}</span><span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span>
            </div>
            @if(($order_meta->shipping_cost ?? 0) > 0)
            <div class="d-flex justify-content-between gap-4" style="font-size:13px;color:var(--pf-muted);">
                <span>{{ __('Shipping') }}</span><span>{{ amount_with_currency_symbol($order_meta->shipping_cost ?? 0) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between gap-4" style="font-size:16px;font-weight:800;color:var(--pf-dark);padding-top:8px;border-top:1px solid var(--pf-border);">
                <span>{{ __('Total') }}</span><span style="color:var(--pf-teal);">{{ amount_with_currency_symbol($order->total ?? 0) }}</span>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('tenant.user.dashboard.package.order') }}" class="pf-btn pf-btn-ghost">
    <i class="las la-arrow-left"></i> {{ __('Back to Orders') }}
</a>
@endsection
