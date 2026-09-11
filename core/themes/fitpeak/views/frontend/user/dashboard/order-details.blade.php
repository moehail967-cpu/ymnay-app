@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Order Details') }} @endsection
@section('page-title') {{ __('Order Details') }} @endsection

@section('dashboard-content')

{{-- Status bar --}}
<div class="fp-dash-card" style="margin-bottom:20px;">
    <div class="fp-dash-card-header">
        {{ __('Order') }} #{{ $order->id }}
        <span style="font-size:12px;color:var(--fp-muted);">{{ $order->created_at->format('d M Y, H:i') }}</span>
    </div>
    <div class="fp-dash-card-body">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="fp-dash-label">{{ __('Payment') }}</div>
                @php $pStyle = match($order->payment_status) { 'complete' => 'color:var(--fp-green);', 'pending' => 'color:#ffc107;', default => 'color:#ff4d4d;' }; @endphp
                <span class="fp-dash-badge" style="{{ $pStyle }}background:rgba(255,255,255,.05);border:1px solid currentColor;">{{ ucfirst($order->payment_status) }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div class="fp-dash-label">{{ __('Order Status') }}</div>
                <span class="fp-dash-badge" style="background:rgba(0,255,65,.1);color:var(--fp-green);border:1px solid var(--fp-green);">{{ ucfirst($order->order_status ?? 'pending') }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div class="fp-dash-label">{{ __('Payment Via') }}</div>
                <span style="font-family:var(--fp-font-head);color:var(--fp-text);font-size:13px;">{{ ucfirst($order->payment_gateway ?? 'N/A') }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div class="fp-dash-label">{{ __('Total') }}</div>
                <span style="font-family:var(--fp-font-head);font-size:18px;font-weight:800;color:var(--fp-green);">{{ amount_with_currency_symbol($order->total) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Items --}}
<div class="fp-dash-card" style="margin-bottom:20px;">
    <div class="fp-dash-card-header">{{ __('Items') }}</div>
    <div class="fp-dash-card-body" style="padding:0;">
        <table class="fp-dash-table">
            <thead><tr><th>{{ __('Product') }}</th><th>{{ __('Options') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Price') }}</th></tr></thead>
            <tbody>
            @php $order_items = json_decode($order->order_details ?? '[]') ?? []; @endphp
            @foreach($order_items as $item)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:48px;height:48px;border:1px solid var(--fp-border);border-radius:var(--fp-radius);overflow:hidden;flex-shrink:0;">
                                {!! render_image_markup_by_attachment_id($item->options->image ?? null, '', 'grid') !!}
                            </div>
                            <span style="font-family:var(--fp-font-head);font-size:13px;color:var(--fp-text);">{{ $item->name ?? '' }}</span>
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--fp-muted);">
                        @if($item->options->color_name ?? null) {{ __('Color:') }} {{ $item->options->color_name }}<br>@endif
                        @if($item->options->size_name ?? null) {{ __('Size:') }} {{ $item->options->size_name }}<br>@endif
                    </td>
                    <td style="font-family:var(--fp-font-head);">{{ $item->qty ?? 1 }}</td>
                    <td style="font-family:var(--fp-font-head);font-weight:700;color:var(--fp-green);">{{ amount_with_currency_symbol($item->price ?? 0) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Totals + Billing --}}
<div class="row g-3">
    <div class="col-md-6">
        <div class="fp-dash-card" style="height:100%;">
            <div class="fp-dash-card-header">{{ __('Billing Info') }}</div>
            <div class="fp-dash-card-body" style="font-size:13px;color:var(--fp-text);line-height:2;">
                <strong style="color:var(--fp-green);font-family:var(--fp-font-head);">{{ $order->billing_name ?? $order->name ?? '' }}</strong><br>
                {{ $order->billing_email ?? $order->email ?? '' }}<br>
                {{ $order->billing_phone ?? $order->phone ?? '' }}<br>
                {{ implode(', ', array_filter([$order->billing_city ?? $order->city ?? null, $order->billing_state ?? $order->state ?? null, $order->billing_country ?? $order->country ?? null])) }}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="fp-dash-card" style="height:100%;">
            <div class="fp-dash-card-header">{{ __('Summary') }}</div>
            <div class="fp-dash-card-body">
                @php $order_meta = json_decode($order->payment_meta ?? '{}'); @endphp
                @foreach([['Subtotal', $order_meta->subtotal ?? 0],['Shipping', $order_meta->shipping_cost ?? 0],['Tax', $order_meta->product_tax ?? 0],['Discount', $order->coupon_discounted ?? 0]] as [$label, $val])
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:5px 0;border-bottom:1px solid var(--fp-border);">
                    <span style="color:var(--fp-muted);">{{ __($label) }}</span>
                    <span style="font-family:var(--fp-font-head);">{{ amount_with_currency_symbol($val) }}</span>
                </div>
                @endforeach
                <div style="display:flex;justify-content:space-between;padding:10px 0 0;font-family:var(--fp-font-head);font-weight:800;color:var(--fp-green);">
                    <span>{{ __('Total') }}</span>
                    <span style="font-size:20px;">{{ amount_with_currency_symbol($order->total) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="margin-top:20px;">
    <a href="{{ theme_user_package_orders_url() }}" class="fp-btn fp-btn-dark">
        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>
@endsection
