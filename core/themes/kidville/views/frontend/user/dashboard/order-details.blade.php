@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@php $order_meta = json_decode($order->payment_meta); @endphp

@section('section')

@php
    $card = 'background:var(--kv-white);border:2px solid var(--kv-border);border-radius:var(--kv-radius);box-shadow:var(--kv-shadow);';
    $lbl  = 'font-size:10px;color:var(--kv-muted);text-transform:uppercase;letter-spacing:1px;font-weight:700;margin-bottom:4px;';
@endphp

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-size:13px;font-weight:800;color:var(--kv-red);display:flex;align-items:center;gap:8px;">
        <i class="las la-receipt"></i> {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ theme_user_package_orders_url() }}" class="kv-btn kv-btn-outline kv-btn-sm">
        <i class="las la-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div style="{{ $card }}padding:20px;margin-bottom:20px;">
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div style="{{ $lbl }}">{{ __('Date') }}</div>
            <div style="color:var(--kv-dark);font-weight:700;">{{ $order->created_at?->format('d M Y') }}</div>
            <div style="color:var(--kv-muted);font-size:12px;">{{ $order->created_at?->format('h:i A') }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="{{ $lbl }}">{{ __('Total') }}</div>
            <div style="color:var(--kv-red);font-size:20px;font-weight:900;">{{ amount_with_currency_symbol($order->total_amount) }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="{{ $lbl }}">{{ __('Order Status') }}</div>
            @php $os = $order->status ?? 'pending'; @endphp
            <span style="padding:4px 14px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;text-transform:uppercase;
                background:{{ $os==='complete'?'rgba(67,160,71,.12)':($os==='cancel'?'rgba(244,67,54,.12)':'rgba(251,140,0,.12)') }};
                color:{{ $os==='complete'?'var(--kv-green)':($os==='cancel'?'var(--kv-red)':'var(--kv-orange)') }};">
                {{ __($os) }}
            </span>
        </div>
        <div class="col-md-3 col-6">
            <div style="{{ $lbl }}">{{ __('Payment Status') }}</div>
            @php $ps = $order->payment_status ?? 'pending'; @endphp
            <span style="padding:4px 14px;border-radius:var(--kv-radius-sm);font-size:11px;font-weight:700;text-transform:uppercase;
                background:{{ $ps==='complete'||$ps==='success'?'rgba(67,160,71,.12)':($ps==='cancel'?'rgba(244,67,54,.12)':'rgba(251,140,0,.12)') }};
                color:{{ $ps==='complete'||$ps==='success'?'var(--kv-green)':($ps==='cancel'?'var(--kv-red)':'var(--kv-orange)') }};">
                {{ __($ps) }}
            </span>
        </div>
    </div>
    @if($order->transaction_id)
    <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--kv-border);font-size:12px;color:var(--kv-muted);">
        {{ __('Transaction ID:') }} <span style="color:var(--kv-dark);font-weight:700;">{{ $order->transaction_id }}</span>
    </div>
    @endif
</div>

{{-- Order Items --}}
<div style="{{ $card }}margin-bottom:20px;overflow:hidden;">
    <div style="padding:14px 20px;border-bottom:2px solid var(--kv-border);background:var(--kv-light);font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--kv-red);">
        {{ __('Order Items') }}
    </div>
    @foreach(json_decode($order->order_details) ?? [] as $item)
    <div style="padding:16px 20px;border-bottom:1px solid var(--kv-border);display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
        <div style="flex:1;min-width:0;">
            <div style="color:var(--kv-dark);font-weight:700;">{{ $item->name }}</div>
            @if(!empty($item->options?->color_name))
                <div style="font-size:12px;color:var(--kv-muted);">{{ __('Color') }}: {{ $item->options->color_name }}</div>
            @endif
            @if(!empty($item->options?->size_name))
                <div style="font-size:12px;color:var(--kv-muted);">{{ __('Size') }}: {{ $item->options->size_name }}</div>
            @endif
            @foreach($item->options?->attributes ?? [] as $key => $val)
                <div style="font-size:12px;color:var(--kv-muted);">{{ $key }}: {{ $val }}</div>
            @endforeach
            <div style="font-size:12px;color:var(--kv-muted);margin-top:4px;">{{ __('Qty') }}: {{ $item->qty }}</div>
        </div>
        <div style="color:var(--kv-red);font-size:15px;font-weight:800;white-space:nowrap;">
            {{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}
        </div>
    </div>
    @endforeach

    <div style="padding:16px 20px;">
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--kv-border);color:var(--kv-muted);font-size:13px;">
            <span>{{ __('Subtotal') }}</span>
            <span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span>
        </div>
        @if($order_meta?->shipping_cost ?? false)
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--kv-border);color:var(--kv-muted);font-size:13px;">
            <span>{{ __('Shipping') }}</span>
            <span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span>
        </div>
        @endif
        @if($order_meta?->discount ?? false)
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--kv-border);color:var(--kv-muted);font-size:13px;">
            <span>{{ __('Discount') }}</span>
            <span style="color:var(--kv-red);">-{{ amount_with_currency_symbol($order_meta->discount) }}</span>
        </div>
        @endif
        <div style="display:flex;justify-content:space-between;padding:12px 0;color:var(--kv-red);font-size:18px;font-weight:900;">
            <span>{{ __('Total') }}</span>
            <span>{{ amount_with_currency_symbol($order->total_amount) }}</span>
        </div>
    </div>
</div>

@if($order->address || $order->name)
<div style="{{ $card }}padding:20px;">
    <div style="font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--kv-red);margin-bottom:14px;">{{ __('Billing Information') }}</div>
    <div class="row g-2" style="font-size:13px;color:var(--kv-muted);">
        @if($order->name)<div class="col-md-6"><span style="color:var(--kv-dark);font-weight:700;">{{ __('Name') }}:</span> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><span style="color:var(--kv-dark);font-weight:700;">{{ __('Email') }}:</span> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><span style="color:var(--kv-dark);font-weight:700;">{{ __('Phone') }}:</span> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><span style="color:var(--kv-dark);font-weight:700;">{{ __('Address') }}:</span> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
