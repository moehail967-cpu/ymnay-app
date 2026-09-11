@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@php $order_meta = json_decode($order->payment_meta); @endphp

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div class="sz-dash-section-title" style="margin-bottom:0;padding-bottom:0;">
        <i class="mdi mdi-receipt-outline" style="margin-right:8px;color:var(--sz-red);"></i>
        {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ route('tenant.user.dashboard.package.order') }}" class="sz-dash-btn sz-dash-btn-outline" style="padding:7px 16px;font-size:11px;">
        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

{{-- Status Bar --}}
<div class="sz-dash-card" style="margin-bottom:20px;">
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div style="font-family:var(--sz-font-head);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;">{{ __('Date') }}</div>
            <div style="color:var(--sz-dark);font-weight:600;">{{ $order->created_at?->format('d M Y') }}</div>
            <div style="color:var(--sz-muted);font-size:12px;">{{ $order->created_at?->format('h:i A') }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-family:var(--sz-font-head);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;">{{ __('Total') }}</div>
            <div style="color:var(--sz-red);font-family:var(--sz-font-head);font-size:22px;font-weight:700;">{{ amount_with_currency_symbol($order->total_amount) }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-family:var(--sz-font-head);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;">{{ __('Order Status') }}</div>
            @php $os = $order->status ?? 'pending'; $oc = match(true){ in_array($os,['complete','success'])=>'sz-dash-badge-success', $os==='cancel'=>'sz-dash-badge-danger', default=>'sz-dash-badge-warning' }; @endphp
            <span class="sz-dash-badge {{ $oc }}">{{ __($os) }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-family:var(--sz-font-head);font-size:10px;font-weight:400;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;">{{ __('Payment Status') }}</div>
            @php $ps = $order->payment_status ?? 'pending'; $pc = match(true){ in_array($ps,['complete','success'])=>'sz-dash-badge-success', $ps==='cancel'=>'sz-dash-badge-danger', default=>'sz-dash-badge-warning' }; @endphp
            <span class="sz-dash-badge {{ $pc }}">{{ __($ps) }}</span>
        </div>
    </div>
    @if($order->transaction_id)
    <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--sz-border);font-size:12px;color:var(--sz-muted);">
        {{ __('Transaction ID:') }} <span style="color:var(--sz-dark);font-weight:600;">{{ $order->transaction_id }}</span>
    </div>
    @endif
</div>

{{-- Order Items --}}
<div class="sz-dash-card" style="padding:0;overflow:hidden;margin-bottom:20px;">
    <div style="background:var(--sz-navy);padding:14px 20px;">
        <span class="sz-dash-section-title" style="color:#fff;margin:0;padding:0;border-bottom:none;display:block;">{{ __('Order Items') }}</span>
    </div>
    <div style="overflow-x:auto;">
        <table class="sz-dash-table">
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
                        <div style="font-weight:600;color:var(--sz-dark);">{{ $item->name }}</div>
                        @if(!empty($item->options?->color_name))
                            <div style="font-size:12px;color:var(--sz-muted);">{{ __('Color') }}: {{ $item->options->color_name }}</div>
                        @endif
                        @if(!empty($item->options?->size_name))
                            <div style="font-size:12px;color:var(--sz-muted);">{{ __('Size') }}: {{ $item->options->size_name }}</div>
                        @endif
                        @foreach($item->options?->attributes ?? [] as $key => $val)
                            <div style="font-size:12px;color:var(--sz-muted);">{{ $key }}: {{ $val }}</div>
                        @endforeach
                    </td>
                    <td style="color:var(--sz-muted);">{{ $item->qty }}</td>
                    <td><strong>{{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div style="padding:16px 24px;border-top:2px solid var(--sz-border);">
        <div style="max-width:280px;margin-left:auto;">
            <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--sz-border);font-size:13px;color:var(--sz-muted);">
                <span style="font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;font-size:11px;">{{ __('Subtotal') }}</span>
                <span>{{ amount_with_currency_symbol($order_meta->subtotal ?? 0) }}</span>
            </div>
            @if($order_meta?->shipping_cost ?? false)
            <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--sz-border);font-size:13px;color:var(--sz-muted);">
                <span style="font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;font-size:11px;">{{ __('Shipping') }}</span>
                <span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span>
            </div>
            @endif
            @if($order_meta?->discount ?? false)
            <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--sz-border);font-size:13px;color:var(--sz-muted);">
                <span style="font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;font-size:11px;">{{ __('Discount') }}</span>
                <span>-{{ amount_with_currency_symbol($order_meta->discount) }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;padding:12px 0;font-family:var(--sz-font-head);font-size:16px;font-weight:700;color:var(--sz-dark);text-transform:uppercase;letter-spacing:1px;">
                <span>{{ __('Total') }}</span>
                <span style="color:var(--sz-red);">{{ amount_with_currency_symbol($order->total_amount) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Billing Info --}}
@if($order->address || $order->name)
<div class="sz-dash-card">
    <div class="sz-dash-section-title">{{ __('Billing Information') }}</div>
    <div class="row g-2" style="font-size:13px;color:var(--sz-dark);">
        @if($order->name)<div class="col-md-6"><strong style="font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:.5px;font-size:11px;color:var(--sz-muted);">{{ __('Name') }}:</strong> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><strong style="font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:.5px;font-size:11px;color:var(--sz-muted);">{{ __('Email') }}:</strong> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><strong style="font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:.5px;font-size:11px;color:var(--sz-muted);">{{ __('Phone') }}:</strong> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><strong style="font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:.5px;font-size:11px;color:var(--sz-muted);">{{ __('Address') }}:</strong> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
