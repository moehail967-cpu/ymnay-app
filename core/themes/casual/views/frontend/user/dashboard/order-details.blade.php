@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@section('section')

@php $order_meta = json_decode($order->payment_meta); @endphp

<div class="cs-dash-section-head">
    <div class="cs-dash-section-title">
        <i class="las la-receipt"></i> {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ theme_user_package_orders_url() }}" class="cs-dash-action-btn">
        <i class="las la-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div class="cs-dash-box cs-dash-status-bar mb-3">
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div class="cs-dash-meta-label">{{ __('Date') }}</div>
            <div class="cs-dash-meta-value">{{ $order->created_at?->format('d M Y') }}</div>
            <div class="cs-dash-meta-sub">{{ $order->created_at?->format('h:i A') }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div class="cs-dash-meta-label">{{ __('Total') }}</div>
            <div class="cs-dash-total-value">{{ amount_with_currency_symbol($order->total_amount) }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div class="cs-dash-meta-label">{{ __('Order Status') }}</div>
            @php $os = $order->status ?? 'pending'; @endphp
            <span class="cs-dash-badge cs-dash-badge-{{ in_array($os, ['complete','success']) ? 'success' : ($os === 'cancel' ? 'danger' : 'warning') }}">
                {{ __($os) }}
            </span>
        </div>
        <div class="col-md-3 col-6">
            <div class="cs-dash-meta-label">{{ __('Payment Status') }}</div>
            @php $ps = $order->payment_status ?? 'pending'; $ps_ok = in_array($ps, ['complete','success']); @endphp
            <span class="cs-dash-badge cs-dash-badge-{{ $ps_ok ? 'success' : ($ps === 'cancel' ? 'danger' : 'warning') }}">
                {{ __($ps) }}
            </span>
        </div>
    </div>
    @if($order->transaction_id)
    <div class="cs-dash-txn-id">
        {{ __('Transaction ID:') }} <strong>{{ $order->transaction_id }}</strong>
    </div>
    @endif
</div>

{{-- Order Items --}}
<div class="cs-dash-box mb-3">
    <div class="cs-dash-box-head">
        <i class="las la-list-ul"></i> {{ __('Order Items') }}
    </div>
    @foreach(json_decode($order->order_details) ?? [] as $item)
    <div class="cs-dash-order-item">
        <div class="cs-dash-order-item-info">
            <div class="cs-dash-order-item-name">{{ $item->name }}</div>
            @if(!empty($item->options?->color_name))
                <div class="cs-dash-order-item-meta">{{ __('Color') }}: {{ $item->options->color_name }}</div>
            @endif
            @if(!empty($item->options?->size_name))
                <div class="cs-dash-order-item-meta">{{ __('Size') }}: {{ $item->options->size_name }}</div>
            @endif
            <div class="cs-dash-order-item-meta">{{ __('Qty') }}: {{ $item->qty }}</div>
        </div>
        <div class="cs-dash-order-item-price">
            {{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}
        </div>
    </div>
    @endforeach

    <div class="cs-dash-summary-rows">
        <div class="cs-dash-summary-row"><span>{{ __('Subtotal') }}</span><span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span></div>
        @if($order_meta?->shipping_cost ?? false)
        <div class="cs-dash-summary-row"><span>{{ __('Shipping') }}</span><span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span></div>
        @endif
        @if($order_meta?->discount ?? false)
        <div class="cs-dash-summary-row"><span>{{ __('Discount') }}</span><span class="cs-dash-td-price">-{{ amount_with_currency_symbol($order_meta->discount) }}</span></div>
        @endif
        <div class="cs-dash-summary-row cs-dash-summary-total">
            <span>{{ __('Total') }}</span>
            <span class="cs-dash-total-value">{{ amount_with_currency_symbol($order->total_amount) }}</span>
        </div>
    </div>
</div>

{{-- Billing Info --}}
@if($order->address || $order->name)
<div class="cs-dash-box cs-dash-billing-box">
    <div class="cs-dash-box-head">
        <i class="las la-map-marker-alt"></i> {{ __('Billing Information') }}
    </div>
    <div class="row g-2 cs-dash-billing-grid">
        @if($order->name)<div class="col-md-6"><span class="cs-dash-billing-key">{{ __('Name') }}:</span> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><span class="cs-dash-billing-key">{{ __('Email') }}:</span> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><span class="cs-dash-billing-key">{{ __('Phone') }}:</span> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><span class="cs-dash-billing-key">{{ __('Address') }}:</span> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
