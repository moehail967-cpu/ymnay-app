@extends(include_theme_path('user.user-master'))

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@php $order_meta = json_decode($order->payment_meta); @endphp

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div class="ms-dash-section-title" style="margin:0;">
        <i class="mdi mdi-receipt-outline" style="margin-right:6px;color:var(--ms-olive);"></i>
        {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ route('tenant.user.dashboard.package.order') }}"
       class="ms-btn-border"
       style="font-size:12px;padding:7px 14px;">
        <i class="mdi mdi-arrow-left" style="margin-right:4px;"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div class="ms-dash-card" style="margin-bottom:20px;">
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Date') }}</div>
            <div style="color:var(--ms-dark);font-weight:600;">{{ $order->created_at?->format('d M Y') }}</div>
            <div style="color:var(--ms-muted);font-size:12px;">{{ $order->created_at?->format('h:i A') }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Total') }}</div>
            <div style="color:var(--ms-dark);font-weight:700;font-size:20px;">{{ amount_with_currency_symbol($order->total_amount) }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Order Status') }}</div>
            @php $os = $order->status ?? 'pending'; $os_class = match(true){ in_array($os,['complete','success'])=>'ms-badge-success', $os==='cancel'=>'ms-badge-muted', default=>'ms-badge-warning' }; @endphp
            <span class="ms-badge {{ $os_class }}">{{ __($os) }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Payment Status') }}</div>
            @php $ps = $order->payment_status ?? 'pending'; $ps_class = match(true){ in_array($ps,['complete','success'])=>'ms-badge-success', $ps==='cancel'=>'ms-badge-muted', default=>'ms-badge-warning' }; @endphp
            <span class="ms-badge {{ $ps_class }}">{{ __($ps) }}</span>
        </div>
    </div>
    @if($order->transaction_id)
    <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--ms-border);font-size:12px;color:var(--ms-muted);">
        {{ __('Transaction ID:') }} <span style="color:var(--ms-dark);font-weight:600;">{{ $order->transaction_id }}</span>
    </div>
    @endif
</div>

{{-- Order Items --}}
<div class="ms-dash-card" style="padding:0;overflow:hidden;margin-bottom:20px;">
    <div style="padding:14px 20px;border-bottom:1px solid var(--ms-border);">
        <div class="ms-dash-section-title" style="margin:0;">{{ __('Order Items') }}</div>
    </div>
    <div style="overflow-x:auto;">
        <table class="ms-table" style="width:100%;">
            <thead>
                <tr>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('Qty') }}</th>
                    <th>{{ __('Price') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach(json_decode($order->order_details) ?? [] as $item)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--ms-dark);">{{ $item->name }}</div>
                        @if(!empty($item->options?->color_name))
                            <div style="font-size:12px;color:var(--ms-muted);">{{ __('Color') }}: {{ $item->options->color_name }}</div>
                        @endif
                        @if(!empty($item->options?->size_name))
                            <div style="font-size:12px;color:var(--ms-muted);">{{ __('Size') }}: {{ $item->options->size_name }}</div>
                        @endif
                        @foreach($item->options?->attributes ?? [] as $key => $val)
                            <div style="font-size:12px;color:var(--ms-muted);">{{ $key }}: {{ $val }}</div>
                        @endforeach
                    </td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div style="padding:16px 20px;border-top:1px solid var(--ms-border);">
        <div style="max-width:280px;margin-left:auto;">
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--ms-border);font-size:13px;color:var(--ms-muted);">
                <span>{{ __('Subtotal') }}</span>
                <span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span>
            </div>
            @if($order_meta?->shipping_cost ?? false)
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--ms-border);font-size:13px;color:var(--ms-muted);">
                <span>{{ __('Shipping') }}</span>
                <span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span>
            </div>
            @endif
            @if($order_meta?->discount ?? false)
            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid var(--ms-border);font-size:13px;color:var(--ms-muted);">
                <span>{{ __('Discount') }}</span>
                <span>-{{ amount_with_currency_symbol($order_meta->discount) }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;padding:10px 0;font-weight:700;color:var(--ms-dark);font-size:16px;">
                <span>{{ __('Total') }}</span>
                <span>{{ amount_with_currency_symbol($order->total_amount) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Billing Info --}}
@if($order->address || $order->name)
<div class="ms-dash-card">
    <div class="ms-dash-section-title">{{ __('Billing Information') }}</div>
    <div class="row g-2" style="font-size:13px;color:var(--ms-charcoal);">
        @if($order->name)<div class="col-md-6"><strong style="color:var(--ms-dark);">{{ __('Name') }}:</strong> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><strong style="color:var(--ms-dark);">{{ __('Email') }}:</strong> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><strong style="color:var(--ms-dark);">{{ __('Phone') }}:</strong> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><strong style="color:var(--ms-dark);">{{ __('Address') }}:</strong> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
