@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Order Details') }} #{{ $order->id }} @endsection

@php $order_meta = json_decode($order->payment_meta); @endphp

@section('dashboard_content')

<div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:20px;">
    <div style="font-size:18px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px;">
        <i class="mdi mdi-receipt-outline" style="color:var(--tz-blue);"></i>
        {{ __('Order') }} #{{ $order->id }}
    </div>
    <a href="{{ route('tenant.user.dashboard.package.order') }}"
       style="display:inline-flex;align-items:center;gap:6px;background:transparent;color:var(--tz-blue);border:1px solid var(--tz-blue);padding:8px 16px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:600;text-decoration:none;font-family:var(--tz-font);transition:all .2s;"
       onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--tz-blue)'">
        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

<div class="tz-dash-card tz-dash-card-body" style="margin-bottom:20px;">
    <div class="row g-3">
        <div class="col-md-3 col-6">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:6px;">{{ __('Date') }}</div>
            <div style="color:#fff;font-weight:600;">{{ $order->created_at?->format('d M Y') }}</div>
            <div style="color:var(--tz-muted);font-size:12px;">{{ $order->created_at?->format('h:i A') }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:6px;">{{ __('Total') }}</div>
            <div style="color:var(--tz-blue);font-size:22px;font-weight:800;">{{ amount_with_currency_symbol($order->total_amount) }}</div>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:6px;">{{ __('Order Status') }}</div>
            @php $os = $order->status ?? 'pending'; $oc = match(true){ in_array($os,['complete','success'])=>'tz-dash-badge-success', $os==='cancel'=>'tz-dash-badge-danger', default=>'tz-dash-badge-warning' }; @endphp
            <span class="tz-dash-badge {{ $oc }}">{{ __($os) }}</span>
        </div>
        <div class="col-md-3 col-6">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);margin-bottom:6px;">{{ __('Payment Status') }}</div>
            @php $ps = $order->payment_status ?? 'pending'; $pc = match(true){ in_array($ps,['complete','success'])=>'tz-dash-badge-success', $ps==='cancel'=>'tz-dash-badge-danger', default=>'tz-dash-badge-warning' }; @endphp
            <span class="tz-dash-badge {{ $pc }}">{{ __($ps) }}</span>
        </div>
    </div>
    @if($order->transaction_id)
    <div style="margin-top:14px;padding-top:12px;border-top:1px solid var(--tz-border);font-size:12px;color:var(--tz-muted);">
        {{ __('Transaction ID:') }} <span style="color:var(--tz-text);font-weight:600;">{{ $order->transaction_id }}</span>
    </div>
    @endif
</div>

<div class="tz-dash-card" style="padding:0;overflow:hidden;margin-bottom:20px;">
    <div class="tz-dash-card-header">{{ __('Order Items') }}</div>
    <div style="overflow-x:auto;">
        <table class="tz-dash-table">
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
                        <div style="font-weight:600;color:#fff;">{{ $item->name }}</div>
                        @if(!empty($item->options?->color_name))
                            <div style="font-size:12px;color:var(--tz-muted);">{{ __('Color') }}: {{ $item->options->color_name }}</div>
                        @endif
                        @if(!empty($item->options?->size_name))
                            <div style="font-size:12px;color:var(--tz-muted);">{{ __('Size') }}: {{ $item->options->size_name }}</div>
                        @endif
                    </td>
                    <td>{{ $item->qty }}</td>
                    <td style="color:var(--tz-blue);font-weight:700;">{{ amount_with_currency_symbol(($item->price * $item->qty) ?? 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding:16px 20px;border-top:1px solid var(--tz-border);">
        <div style="max-width:280px;margin-left:auto;">
            @if($order_meta?->subtotal ?? false)
            <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--tz-border);font-size:13px;color:var(--tz-muted);">
                <span>{{ __('Subtotal') }}</span><span>{{ amount_with_currency_symbol($order_meta->subtotal) }}</span>
            </div>
            @endif
            @if($order_meta?->shipping_cost ?? false)
            <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--tz-border);font-size:13px;color:var(--tz-muted);">
                <span>{{ __('Shipping') }}</span><span>{{ amount_with_currency_symbol($order_meta->shipping_cost) }}</span>
            </div>
            @endif
            @if($order_meta?->discount ?? false)
            <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--tz-border);font-size:13px;color:var(--tz-muted);">
                <span>{{ __('Discount') }}</span><span>-{{ amount_with_currency_symbol($order_meta->discount) }}</span>
            </div>
            @endif
            <div style="display:flex;justify-content:space-between;padding:12px 0;font-size:16px;font-weight:800;">
                <span style="color:#fff;">{{ __('Total') }}</span>
                <span style="color:var(--tz-blue);">{{ amount_with_currency_symbol($order->total_amount) }}</span>
            </div>
        </div>
    </div>
</div>

@if($order->address || $order->name)
<div class="tz-dash-card tz-dash-card-body">
    <div style="font-size:14px;font-weight:700;color:#fff;margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--tz-border);">{{ __('Billing Information') }}</div>
    <div class="row g-2" style="font-size:13px;color:var(--tz-text);">
        @if($order->name)<div class="col-md-6"><span style="color:var(--tz-muted);">{{ __('Name') }}:</span> {{ $order->name }}</div>@endif
        @if($order->email)<div class="col-md-6"><span style="color:var(--tz-muted);">{{ __('Email') }}:</span> {{ $order->email }}</div>@endif
        @if($order->phone)<div class="col-md-6"><span style="color:var(--tz-muted);">{{ __('Phone') }}:</span> {{ $order->phone }}</div>@endif
        @if($order->address)<div class="col-12"><span style="color:var(--tz-muted);">{{ __('Address') }}:</span> {{ $order->address }}</div>@endif
    </div>
</div>
@endif

@endsection
