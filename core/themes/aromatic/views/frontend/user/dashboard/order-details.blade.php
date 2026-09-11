@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@section('section')

@php $order_meta = json_decode($order->payment_meta); @endphp

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="ar-dash-section-title mb-0">
        <i class="mdi mdi-receipt-outline" style="color:var(--ar-red);"></i>
        {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ theme_user_package_orders_url() }}" class="ar-btn ar-btn-outline ar-btn-sm">
        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div class="ar-dash-box mb-3">
    <div style="padding:20px;">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div style="font-size:11px;color:var(--ar-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Date') }}</div>
                <div style="font-weight:700;color:var(--ar-dark);">{{ $order->created_at?->format('d M Y') }}</div>
                <div style="font-size:12px;color:var(--ar-muted);">{{ $order->created_at?->format('h:i A') }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div style="font-size:11px;color:var(--ar-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Total') }}</div>
                <div style="font-size:20px;font-weight:900;color:var(--ar-red);font-family:var(--ar-font-head);">{{ amount_with_currency_symbol($order->total_amount) }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div style="font-size:11px;color:var(--ar-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Order Status') }}</div>
                @php $os = $order->status ?? 'pending'; @endphp
                <span class="ar-status-badge" style="
                    background:{{ $os==='complete'?'rgba(76,175,80,.12)':($os==='cancel'?'rgba(248,58,38,.12)':'rgba(255,193,7,.12)') }};
                    color:{{ $os==='complete'?'#4CAF50':($os==='cancel'?'var(--ar-red)':'#D4A017') }};">
                    {{ __($os) }}
                </span>
            </div>
            <div class="col-md-3 col-6">
                <div style="font-size:11px;color:var(--ar-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Payment Status') }}</div>
                @php $ps = $order->payment_status ?? 'pending'; $ps_ok = in_array($ps, ['complete','success']); @endphp
                <span class="ar-status-badge" style="
                    background:{{ $ps_ok?'rgba(76,175,80,.12)':($ps==='cancel'?'rgba(248,58,38,.12)':'rgba(255,193,7,.12)') }};
                    color:{{ $ps_ok?'#4CAF50':($ps==='cancel'?'var(--ar-red)':'#D4A017') }};">
                    {{ __($ps) }}
                </span>
            </div>
        </div>
        @if($order->transaction_id)
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--ar-border);font-size:12px;color:var(--ar-muted);">
            {{ __('Transaction ID:') }} <strong style="color:var(--ar-dark);">{{ $order->transaction_id }}</strong>
        </div>
        @endif
    </div>
</div>

{{-- Order Items --}}
<div class="ar-dash-box mb-3">
    <div style="padding:16px 20px;border-bottom:1.5px solid var(--ar-border);">
        <strong style="font-size:13px;text-transform:uppercase;letter-spacing:.5px;color:var(--ar-dark);">{{ __('Order Items') }}</strong>
    </div>
    @foreach(json_decode($order->order_details) ?? [] as $item)
    <div style="padding:16px 20px;border-bottom:1px solid var(--ar-border);display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;color:var(--ar-dark);">{{ $item->name }}</div>
            @if(!empty($item->options?->color_name))
                <div style="font-size:12px;color:var(--ar-muted);">{{ __('Color') }}: {{ $item->options->color_name }}</div>
            @endif
            @if(!empty($item->options?->size_name))
                <div style="font-size:12px;color:var(--ar-muted);">{{ __('Size') }}: {{ $item->options->size_name }}</div>
            @endif
            <div style="font-size:12px;color:var(--ar-muted);margin-top:4px;">{{ __('Qty') }}: {{ $item->qty }}</div>
        </div>
        <div style="font-weight:800;color:var(--ar-red);font-size:15px;">
            {{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}
        </div>
    </div>
    @endforeach
    <div style="padding:16px 20px;">
        <div class="ar-summary-row"><span>{{ __('Subtotal') }}</span><span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span></div>
        @if($order_meta?->shipping_cost ?? false)
        <div class="ar-summary-row"><span>{{ __('Shipping') }}</span><span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span></div>
        @endif
        @if($order_meta?->discount ?? false)
        <div class="ar-summary-row"><span>{{ __('Discount') }}</span><span style="color:var(--ar-red);">-{{ amount_with_currency_symbol($order_meta->discount) }}</span></div>
        @endif
        <div class="ar-summary-row total"><span>{{ __('Total') }}</span><span class="ar-summary-price">{{ amount_with_currency_symbol($order->total_amount) }}</span></div>
    </div>
</div>

{{-- Billing Info --}}
@if($order->address || $order->name)
<div class="ar-dash-box" style="padding:20px;">
    <strong style="font-size:12px;text-transform:uppercase;letter-spacing:.5px;color:var(--ar-dark);display:block;margin-bottom:14px;">{{ __('Billing Information') }}</strong>
    <div class="row g-2" style="font-size:13px;color:var(--ar-muted);">
        @if($order->name)<div class="col-md-6"><strong style="color:var(--ar-dark);">{{ __('Name') }}:</strong> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><strong style="color:var(--ar-dark);">{{ __('Email') }}:</strong> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><strong style="color:var(--ar-dark);">{{ __('Phone') }}:</strong> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><strong style="color:var(--ar-dark);">{{ __('Address') }}:</strong> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
