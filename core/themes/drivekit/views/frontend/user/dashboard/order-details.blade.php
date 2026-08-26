@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@php $order_meta = json_decode($order->payment_meta); @endphp

@section('section')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-weight:800;color:var(--dk-white);font-size:15px;text-transform:uppercase;letter-spacing:.5px;">
        <i class="mdi mdi-receipt-outline" style="color:var(--dk-red);"></i> {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ theme_user_package_orders_url() }}" class="dk-btn dk-btn-ghost dk-btn-sm">
        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:20px;margin-bottom:20px;">
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Date') }}</div>
            <div style="color:var(--dk-white);font-weight:700;">{{ $order->created_at?->format('d M Y') }}</div>
            <div style="color:var(--dk-silver);font-size:12px;">{{ $order->created_at?->format('h:i A') }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Total') }}</div>
            <div style="color:var(--dk-white);font-weight:800;font-size:20px;">{{ amount_with_currency_symbol($order->total_amount) }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Order Status') }}</div>
            @php $os = $order->status ?? 'pending'; @endphp
            <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;
                background:{{ $os==='complete'?'rgba(76,175,80,.15)':($os==='cancel'?'rgba(229,48,48,.15)':'rgba(255,193,7,.15)') }};
                color:{{ $os==='complete'?'#4CAF50':($os==='cancel'?'var(--dk-red)':'#FFC107') }};">
                {{ __($os) }}
            </span>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;color:var(--dk-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ __('Payment Status') }}</div>
            @php $ps = $order->payment_status ?? 'pending'; @endphp
            <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;text-transform:uppercase;
                background:{{ $ps==='complete'||$ps==='success'?'rgba(76,175,80,.15)':($ps==='cancel'?'rgba(229,48,48,.15)':'rgba(255,193,7,.15)') }};
                color:{{ $ps==='complete'||$ps==='success'?'#4CAF50':($ps==='cancel'?'var(--dk-red)':'#FFC107') }};">
                {{ __($ps) }}
            </span>
        </div>
    </div>
    @if($order->transaction_id)
    <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--dk-border);font-size:12px;color:var(--dk-silver);">
        {{ __('Transaction ID:') }} <span style="color:var(--dk-white);font-weight:600;">{{ $order->transaction_id }}</span>
    </div>
    @endif
</div>

{{-- Order Items --}}
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);margin-bottom:20px;overflow:hidden;">
    <div style="padding:16px 20px;border-bottom:1px solid var(--dk-border);font-weight:700;color:var(--dk-white);font-size:13px;text-transform:uppercase;letter-spacing:.5px;">
        {{ __('Order Items') }}
    </div>
    @foreach(json_decode($order->order_details) ?? [] as $item)
    <div style="padding:16px 20px;border-bottom:1px solid var(--dk-border);display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;color:var(--dk-white);font-size:14px;">{{ $item->name }}</div>
            @if(!empty($item->options?->color_name))
                <div style="font-size:12px;color:var(--dk-silver);">{{ __('Size') }}: {{ $item->options->color_name }}</div>
            @endif
            @if(!empty($item->options?->size_name))
                <div style="font-size:12px;color:var(--dk-silver);">{{ __('Color') }}: {{ $item->options->size_name }}</div>
            @endif
            @foreach($item->options?->attributes ?? [] as $key => $val)
                <div style="font-size:12px;color:var(--dk-silver);">{{ $key }}: {{ $val }}</div>
            @endforeach
            <div style="font-size:12px;color:var(--dk-muted);margin-top:4px;">{{ __('Qty') }}: {{ $item->qty }}</div>
        </div>
        <div style="font-weight:800;color:var(--dk-white);font-size:16px;white-space:nowrap;">
            {{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}
        </div>
    </div>
    @endforeach

    {{-- Totals --}}
    <div style="padding:16px 20px;">
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--dk-border);color:var(--dk-silver);font-size:13px;">
            <span>{{ __('Subtotal') }}</span>
            <span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span>
        </div>
        @if($order_meta?->shipping_cost ?? false)
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--dk-border);color:var(--dk-silver);font-size:13px;">
            <span>{{ __('Shipping') }}</span>
            <span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span>
        </div>
        @endif
        @if($order_meta?->discount ?? false)
        <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--dk-border);color:var(--dk-silver);font-size:13px;">
            <span>{{ __('Discount') }}</span>
            <span style="color:var(--dk-red);">-{{ amount_with_currency_symbol($order_meta->discount) }}</span>
        </div>
        @endif
        <div style="display:flex;justify-content:space-between;padding:12px 0;font-weight:800;color:var(--dk-white);font-size:16px;">
            <span>{{ __('Total') }}</span>
            <span>{{ amount_with_currency_symbol($order->total_amount) }}</span>
        </div>
    </div>
</div>

{{-- Billing Info --}}
@if($order->address || $order->name)
<div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:20px;margin-bottom:20px;">
    <div style="font-weight:700;color:var(--dk-white);margin-bottom:14px;font-size:13px;text-transform:uppercase;letter-spacing:.5px;">{{ __('Billing Information') }}</div>
    <div class="row g-2" style="font-size:13px;color:var(--dk-silver);">
        @if($order->name)<div class="col-md-6"><strong style="color:var(--dk-white);">{{ __('Name') }}:</strong> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><strong style="color:var(--dk-white);">{{ __('Email') }}:</strong> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><strong style="color:var(--dk-white);">{{ __('Phone') }}:</strong> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><strong style="color:var(--dk-white);">{{ __('Address') }}:</strong> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
