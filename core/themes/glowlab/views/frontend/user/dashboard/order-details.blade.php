@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@php $order_meta = json_decode($order->payment_meta); @endphp

@section('section')

@php
    $card = 'background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);box-shadow:var(--gl-shadow);';
    $lbl  = 'font-size:11px;color:var(--gl-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;';
@endphp

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-weight:700;color:var(--gl-dark);font-size:15px;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-receipt" style="color:var(--gl-gold);"></i> {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ theme_user_package_orders_url() }}"
       style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-weight:600;color:var(--gl-dark);text-decoration:none;transition:border-color .2s;"
       onmouseover="this.style.borderColor='var(--gl-gold)'" onmouseout="this.style.borderColor='var(--gl-border)'">
        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div style="{{ $card }}padding:20px;margin-bottom:20px;">
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div style="{{ $lbl }}">{{ __('Date') }}</div>
            <div style="color:var(--gl-dark);font-weight:700;">{{ $order->created_at?->format('d M Y') }}</div>
            <div style="color:var(--gl-muted);font-size:12px;">{{ $order->created_at?->format('h:i A') }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="{{ $lbl }}">{{ __('Total') }}</div>
            <div style="color:var(--gl-dark);font-weight:700;font-size:18px;">{{ amount_with_currency_symbol($order->total_amount) }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="{{ $lbl }}">{{ __('Order Status') }}</div>
            @php $os = $order->status ?? 'pending'; @endphp
            <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;
                background:{{ $os==='complete'?'rgba(72,187,120,.12)':($os==='cancel'?'rgba(229,62,62,.12)':'rgba(245,158,11,.12)') }};
                color:{{ $os==='complete'?'#38a169':($os==='cancel'?'#c53030':'#d97706') }};">
                {{ __($os) }}
            </span>
        </div>
        <div class="col-md-3 col-6">
            <div style="{{ $lbl }}">{{ __('Payment Status') }}</div>
            @php $ps = $order->payment_status ?? 'pending'; @endphp
            <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;
                background:{{ $ps==='complete'||$ps==='success'?'rgba(72,187,120,.12)':($ps==='cancel'?'rgba(229,62,62,.12)':'rgba(245,158,11,.12)') }};
                color:{{ $ps==='complete'||$ps==='success'?'#38a169':($ps==='cancel'?'#c53030':'#d97706') }};">
                {{ __($ps) }}
            </span>
        </div>
    </div>
    @if($order->transaction_id)
    <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--gl-border);font-size:12px;color:var(--gl-muted);">
        {{ __('Transaction ID:') }} <span style="color:var(--gl-dark);font-weight:600;">{{ $order->transaction_id }}</span>
    </div>
    @endif
</div>

{{-- Order Items --}}
<div style="{{ $card }}margin-bottom:20px;overflow:hidden;">
    <div style="padding:14px 20px;border-bottom:1px solid var(--gl-border);background:var(--gl-gold-pale);font-weight:700;color:var(--gl-dark);font-size:11px;text-transform:uppercase;letter-spacing:1px;">
        {{ __('Order Items') }}
    </div>
    @foreach(json_decode($order->order_details) ?? [] as $item)
    <div style="padding:16px 20px;border-bottom:1px solid var(--gl-border);display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap;">
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;color:var(--gl-dark);">{{ $item->name }}</div>
            @if(!empty($item->options?->color_name))
                <div style="font-size:12px;color:var(--gl-muted);">{{ __('Color') }}: {{ $item->options->color_name }}</div>
            @endif
            @if(!empty($item->options?->size_name))
                <div style="font-size:12px;color:var(--gl-muted);">{{ __('Size') }}: {{ $item->options->size_name }}</div>
            @endif
            @foreach($item->options?->attributes ?? [] as $key => $val)
                <div style="font-size:12px;color:var(--gl-muted);">{{ $key }}: {{ $val }}</div>
            @endforeach
            <div style="font-size:12px;color:var(--gl-muted);margin-top:4px;">{{ __('Qty') }}: {{ $item->qty }}</div>
        </div>
        <div style="font-weight:700;color:var(--gl-dark);font-size:15px;white-space:nowrap;">
            {{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}
        </div>
    </div>
    @endforeach

    <div style="padding:16px 20px;">
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gl-border);color:var(--gl-muted);font-size:13px;">
            <span>{{ __('Subtotal') }}</span>
            <span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span>
        </div>
        @if($order_meta?->shipping_cost ?? false)
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gl-border);color:var(--gl-muted);font-size:13px;">
            <span>{{ __('Shipping') }}</span>
            <span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span>
        </div>
        @endif
        @if($order_meta?->discount ?? false)
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gl-border);color:var(--gl-muted);font-size:13px;">
            <span>{{ __('Discount') }}</span>
            <span style="color:#c53030;">-{{ amount_with_currency_symbol($order_meta->discount) }}</span>
        </div>
        @endif
        <div style="display:flex;justify-content:space-between;padding:12px 0;font-weight:700;color:var(--gl-dark);font-size:16px;">
            <span>{{ __('Total') }}</span>
            <span>{{ amount_with_currency_symbol($order->total_amount) }}</span>
        </div>
    </div>
</div>

@if($order->address || $order->name)
<div style="{{ $card }}padding:20px;">
    <div style="font-weight:700;color:var(--gl-dark);margin-bottom:14px;font-size:11px;text-transform:uppercase;letter-spacing:1px;">{{ __('Billing Information') }}</div>
    <div class="row g-2" style="font-size:13px;color:var(--gl-muted);">
        @if($order->name)<div class="col-md-6"><strong style="color:var(--gl-dark);">{{ __('Name') }}:</strong> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><strong style="color:var(--gl-dark);">{{ __('Email') }}:</strong> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><strong style="color:var(--gl-dark);">{{ __('Phone') }}:</strong> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><strong style="color:var(--gl-dark);">{{ __('Address') }}:</strong> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
