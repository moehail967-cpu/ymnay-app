@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@section('section')

@php $order_meta = json_decode($order->payment_meta); @endphp

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="fn-dash-section-title mb-0">
        <i class="las la-receipt"></i>
        {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ theme_user_package_orders_url() }}" class="fn-btn fn-btn-outline fn-btn-sm">
        <i class="las la-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div class="fn-dash-box mb-3">
    <div class="fn-dash-box-pad">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="fn-meta-label">{{ __('Date') }}</div>
                <div class="fn-fw-bold fn-dark-text">{{ $order->created_at?->format('d M Y') }}</div>
                <div class="fn-muted fn-text-xs">{{ $order->created_at?->format('h:i A') }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="fn-meta-label">{{ __('Total') }}</div>
                <div class="fn-order-total">{{ amount_with_currency_symbol($order->total_amount) }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="fn-meta-label">{{ __('Order Status') }}</div>
                @php $os = $order->status ?? 'pending'; @endphp
                <span class="fn-status-badge {{ $os==='complete' ? 'fn-badge-success' : ($os==='cancel' ? 'fn-badge-danger' : 'fn-badge-warn') }}">
                    {{ __($os) }}
                </span>
            </div>
            <div class="col-md-3 col-6">
                <div class="fn-meta-label">{{ __('Payment Status') }}</div>
                @php $ps = $order->payment_status ?? 'pending'; $ps_ok = in_array($ps, ['complete','success']); @endphp
                <span class="fn-status-badge {{ $ps_ok ? 'fn-badge-success' : ($ps==='cancel' ? 'fn-badge-danger' : 'fn-badge-warn') }}">
                    {{ __($ps) }}
                </span>
            </div>
        </div>
        @if($order->transaction_id)
        <div class="fn-transaction-row">
            {{ __('Transaction ID:') }} <strong>{{ $order->transaction_id }}</strong>
        </div>
        @endif
    </div>
</div>

{{-- Order Items --}}
<div class="fn-dash-box mb-3">
    <div class="fn-dash-box-header">
        <strong class="fn-box-heading">{{ __('Order Items') }}</strong>
    </div>
    @foreach(json_decode($order->order_details) ?? [] as $item)
    <div class="fn-order-item">
        <div class="fn-order-item-info">
            <div class="fn-fw-bold fn-dark-text">{{ $item->name }}</div>
            @if(!empty($item->options?->color_name))
                <div class="fn-muted fn-text-xs">{{ __('Color') }}: {{ $item->options->color_name }}</div>
            @endif
            @if(!empty($item->options?->size_name))
                <div class="fn-muted fn-text-xs">{{ __('Size') }}: {{ $item->options->size_name }}</div>
            @endif
            <div class="fn-muted fn-text-xs mt-1">{{ __('Qty') }}: {{ $item->qty }}</div>
        </div>
        <div class="fn-order-item-price">
            {{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}
        </div>
    </div>
    @endforeach
    <div class="fn-dash-box-pad">
        <div class="fn-summary-row"><span>{{ __('Subtotal') }}</span><span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span></div>
        @if($order_meta?->shipping_cost ?? false)
        <div class="fn-summary-row"><span>{{ __('Shipping') }}</span><span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span></div>
        @endif
        @if($order_meta?->discount ?? false)
        <div class="fn-summary-row"><span>{{ __('Discount') }}</span><span class="fn-accent-text">-{{ amount_with_currency_symbol($order_meta->discount) }}</span></div>
        @endif
        <div class="fn-summary-row fn-summary-total"><span>{{ __('Total') }}</span><span class="fn-summary-price">{{ amount_with_currency_symbol($order->total_amount) }}</span></div>
    </div>
</div>

{{-- Billing Info --}}
@if($order->address || $order->name)
<div class="fn-dash-box fn-dash-box-pad">
    <strong class="fn-box-heading fn-box-heading-block">{{ __('Billing Information') }}</strong>
    <div class="row g-2 fn-muted fn-text-sm">
        @if($order->name)<div class="col-md-6"><strong class="fn-dark-text">{{ __('Name') }}:</strong> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><strong class="fn-dark-text">{{ __('Email') }}:</strong> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><strong class="fn-dark-text">{{ __('Phone') }}:</strong> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><strong class="fn-dark-text">{{ __('Address') }}:</strong> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
