@extends(theme_path('frontend.user.dashboard.user-master'))

@section('title') {{ __('Order Details') }} @endsection
@section('page-title') {{ __('Order Details') }} @endsection

@section('dashboard-content')

<div class="vl-dash-card" style="margin-bottom:20px;">
    <div class="vl-dash-card-header">
        {{ __('Order') }} #{{ $order->id }}
        <span style="font-size:11px;color:var(--vl-muted);font-family:'Inter',sans-serif;letter-spacing:0;">{{ $order->created_at->format('d M Y, H:i') }}</span>
    </div>
    <div class="vl-dash-card-body">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div class="vl-dash-label">{{ __('Payment') }}</div>
                @php $pStyle = match($order->payment_status) { 'complete' => 'color:#48BB78;', 'pending' => 'color:#D69E2E;', default => 'color:#E53E3E;' }; @endphp
                <span class="vl-dash-badge" style="{{ $pStyle }}border:1px solid currentColor;background:rgba(0,0,0,.1);">{{ ucfirst($order->payment_status) }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div class="vl-dash-label">{{ __('Order Status') }}</div>
                <span class="vl-dash-badge" style="color:var(--vl-champagne);border:1px solid var(--vl-champagne);background:rgba(212,184,150,.06);">{{ ucfirst($order->order_status ?? 'pending') }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div class="vl-dash-label">{{ __('Payment Via') }}</div>
                <span style="font-size:13px;color:var(--vl-ivory);">{{ ucfirst($order->payment_gateway ?? 'N/A') }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div class="vl-dash-label">{{ __('Total') }}</div>
                <span style="font-size:18px;color:var(--vl-champagne);font-family:'Cormorant Garamond',serif;">{{ amount_with_currency_symbol($order->total) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="vl-dash-card" style="margin-bottom:20px;">
    <div class="vl-dash-card-header">{{ __('Items') }}</div>
    <div class="vl-dash-card-body" style="padding:0;">
        <table class="vl-dash-table">
            <thead><tr><th>{{ __('Product') }}</th><th>{{ __('Options') }}</th><th>{{ __('Qty') }}</th><th>{{ __('Price') }}</th></tr></thead>
            <tbody>
            @php $order_items = json_decode($order->order_details ?? '[]') ?? []; @endphp
            @foreach($order_items as $item)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:48px;height:60px;border:1px solid var(--vl-border);overflow:hidden;flex-shrink:0;background:var(--vl-surface);display:flex;align-items:center;justify-content:center;">
                            {!! render_image_markup_by_attachment_id($item->options->image ?? null, '', 'grid') !!}
                        </div>
                        <span style="font-size:13px;color:var(--vl-ivory);">{{ $item->name ?? '' }}</span>
                    </div>
                </td>
                <td style="font-size:12px;color:var(--vl-muted);">
                    @if($item->options->color_name ?? null) {{ __('Color:') }} {{ $item->options->color_name }}<br>@endif
                    @if($item->options->size_name ?? null) {{ __('Size:') }} {{ $item->options->size_name }}<br>@endif
                </td>
                <td style="color:var(--vl-ivory);">{{ $item->qty ?? 1 }}</td>
                <td style="color:var(--vl-champagne);">{{ amount_with_currency_symbol($item->price ?? 0) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="vl-dash-card" style="height:100%;">
            <div class="vl-dash-card-header">{{ __('Billing Info') }}</div>
            <div class="vl-dash-card-body" style="font-size:13px;color:var(--vl-muted);line-height:2;">
                <div style="font-family:'Cormorant Garamond',serif;font-size:16px;color:var(--vl-ivory);margin-bottom:4px;">{{ $order->billing_name ?? $order->name ?? '' }}</div>
                {{ $order->billing_email ?? $order->email ?? '' }}<br>
                {{ $order->billing_phone ?? $order->phone ?? '' }}<br>
                {{ implode(', ', array_filter([$order->billing_city ?? $order->city ?? null, $order->billing_state ?? $order->state ?? null, $order->billing_country ?? $order->country ?? null])) }}
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="vl-dash-card" style="height:100%;">
            <div class="vl-dash-card-header">{{ __('Summary') }}</div>
            <div class="vl-dash-card-body">
                @php $order_meta = json_decode($order->payment_meta ?? '{}'); @endphp
                @foreach([['Subtotal',$order_meta->subtotal??0],['Shipping',$order_meta->shipping_cost??0],['Tax',$order_meta->product_tax??0],['Discount',$order->coupon_discounted??0]] as [$label,$val])
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-bottom:1px solid rgba(58,36,68,.4);">
                    <span style="color:var(--vl-muted);">{{ __($label) }}</span>
                    <span style="color:var(--vl-ivory);">{{ amount_with_currency_symbol($val) }}</span>
                </div>
                @endforeach
                <div style="display:flex;justify-content:space-between;padding:12px 0 0;">
                    <span style="font-size:13px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);font-family:'Inter',sans-serif;">{{ __('Total') }}</span>
                    <span style="font-size:18px;color:var(--vl-champagne);font-family:'Cormorant Garamond',serif;">{{ amount_with_currency_symbol($order->total) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="margin-top:20px;">
    <a href="{{ route('tenant.user.dashboard.package.order') }}" class="vl-btn vl-btn-outline" style="font-size:10px;letter-spacing:2px;">
        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>
@endsection
