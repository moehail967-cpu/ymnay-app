@extends('theme::frontend.user.dashboard.user-master')

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@section('section')

@php $order_meta = json_decode($order->payment_meta); @endphp

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
    <div class="bp-dash-section-title mb-0">
        <i class="las la-receipt"></i>
        {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ theme_user_package_orders_url() }}" class="bp-btn bp-btn-outline bp-btn-sm">
        <i class="las la-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div class="bp-dash-box mb-3">
    <div class="bp-dash-box-pad">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="bp-meta-label">{{ __('Date') }}</div>
                <div class="bp-fw-bold bp-dark-text">{{ $order->created_at?->format('d M Y') }}</div>
                <div class="bp-muted" style="font-size:12px;">{{ $order->created_at?->format('h:i A') }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bp-meta-label">{{ __('Total') }}</div>
                <div class="bp-order-total">{{ amount_with_currency_symbol($order->total_amount) }}</div>
            </div>
            <div class="col-md-3 col-6">
                <div class="bp-meta-label">{{ __('Order Status') }}</div>
                @php $os = $order->status ?? 'pending'; @endphp
                <span class="bp-status-badge {{ $os==='complete' ? 'bp-badge-success' : ($os==='cancel' ? 'bp-badge-danger' : 'bp-badge-warn') }}">
                    {{ __($os) }}
                </span>
            </div>
            <div class="col-md-3 col-6">
                <div class="bp-meta-label">{{ __('Payment Status') }}</div>
                @php $ps = $order->payment_status ?? 'pending'; $ps_ok = in_array($ps, ['complete','success']); @endphp
                <span class="bp-status-badge {{ $ps_ok ? 'bp-badge-success' : ($ps==='cancel' ? 'bp-badge-danger' : 'bp-badge-warn') }}">
                    {{ __($ps) }}
                </span>
            </div>
        </div>
        @if($order->transaction_id)
        <div class="bp-transaction-row">
            {{ __('Transaction ID:') }} <strong>{{ $order->transaction_id }}</strong>
        </div>
        @endif
    </div>
</div>

{{-- Order Items --}}
<div class="bp-dash-box mb-3">
    <div class="bp-dash-box-header">
        <strong class="bp-box-heading">{{ __('Order Items') }}</strong>
    </div>
    @foreach(json_decode($order->order_details) ?? [] as $item)
    <div class="bp-order-item">
        <div class="bp-order-item-info">
            <div class="bp-fw-bold bp-dark-text">{{ $item->name }}</div>
            @if(!empty($item->options?->color_name))
                <div class="bp-muted" style="font-size:12px;">{{ __('Color') }}: {{ $item->options->color_name }}</div>
            @endif
            @if(!empty($item->options?->size_name))
                <div class="bp-muted" style="font-size:12px;">{{ __('Size') }}: {{ $item->options->size_name }}</div>
            @endif
            <div class="bp-muted" style="font-size:12px;margin-top:4px;">{{ __('Qty') }}: {{ $item->qty }}</div>
        </div>
        <div class="bp-order-item-price">
            {{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}
        </div>
    </div>
    @endforeach
    <div class="bp-dash-box-pad">
        <div class="bp-summary-row"><span>{{ __('Subtotal') }}</span><span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span></div>
        @if($order_meta?->shipping_cost ?? false)
        <div class="bp-summary-row"><span>{{ __('Shipping') }}</span><span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span></div>
        @endif
        @if($order_meta?->discount ?? false)
        <div class="bp-summary-row"><span>{{ __('Discount') }}</span><span class="bp-accent-text">-{{ amount_with_currency_symbol($order_meta->discount) }}</span></div>
        @endif
        <div class="bp-summary-row bp-summary-total"><span>{{ __('Total') }}</span><span class="bp-summary-price">{{ amount_with_currency_symbol($order->total_amount) }}</span></div>
    </div>
</div>

{{-- Billing Info --}}
@if($order->address || $order->name)
<div class="bp-dash-box bp-dash-box-pad">
    <strong class="bp-box-heading" style="display:block;margin-bottom:14px;">{{ __('Billing Information') }}</strong>
    <div class="row g-2 bp-muted" style="font-size:13px;">
        @if($order->name)<div class="col-md-6"><strong class="bp-dark-text">{{ __('Name') }}:</strong> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><strong class="bp-dark-text">{{ __('Email') }}:</strong> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><strong class="bp-dark-text">{{ __('Phone') }}:</strong> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><strong class="bp-dark-text">{{ __('Address') }}:</strong> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
