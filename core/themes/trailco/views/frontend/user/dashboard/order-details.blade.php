@extends(include_theme_path('user.dashboard.user-master'))

@section('title') {{ __('Order Details') }} @endsection
@section('page-title') {{ __('Order Details') }} @endsection

@section('dashboard-content')

{!! theme_error_msg() !!}
{!! theme_flash_msg() !!}

{{-- Order Summary Bar --}}
<div class="tr-dash-card" style="margin-bottom:20px;">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-receipt-outline" style="margin-right:6px;"></i>{{ __('Order') }} #{{ $order->id }}</span>
        <span style="font-size:12px;color:var(--tr-stone);">{{ $order->created_at->format('d M Y, H:i') }}</span>
    </div>
    <div class="tr-dash-card-body">
        <div class="row g-3">
            <div class="col-6 col-md-3">
                <div style="font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px;">{{ __('Payment') }}</div>
                @php
                    $pStyle = match($order->payment_status) {
                        'complete' => 'color:var(--tr-olive);border:1px solid var(--tr-olive);',
                        'pending'  => 'color:#9a7a30;border:1px solid #c9a84c;',
                        default    => 'color:var(--tr-terra);border:1px solid var(--tr-terra);'
                    };
                @endphp
                <span class="tr-dash-badge" style="{{ $pStyle }}background:rgba(0,0,0,.03);">{{ ucfirst($order->payment_status) }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div style="font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px;">{{ __('Order Status') }}</div>
                <span class="tr-dash-badge" style="background:rgba(92,122,62,.12);color:var(--tr-olive);border:1px solid var(--tr-olive);">{{ ucfirst($order->order_status ?? 'pending') }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div style="font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px;">{{ __('Payment Via') }}</div>
                <span style="font-size:13px;font-weight:600;color:var(--tr-bark);">{{ ucfirst($order->payment_gateway ?? 'N/A') }}</span>
            </div>
            <div class="col-6 col-md-3">
                <div style="font-size:11px;font-weight:700;color:var(--tr-stone);text-transform:uppercase;letter-spacing:.7px;margin-bottom:6px;">{{ __('Total') }}</div>
                <span style="font-size:20px;font-weight:800;color:var(--tr-olive);">{{ amount_with_currency_symbol($order->total) }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Order Items --}}
<div class="tr-dash-card" style="margin-bottom:20px;">
    <div class="tr-dash-card-header">
        <span><i class="mdi mdi-format-list-bulleted" style="margin-right:6px;"></i>{{ __('Items') }}</span>
    </div>
    <div class="tr-dash-card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="tr-dash-table">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Options') }}</th>
                        <th>{{ __('Qty') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Subtotal') }}</th>
                    </tr>
                </thead>
                <tbody>
                @php $order_items = json_decode($order->order_details ?? '[]') ?? []; @endphp
                @foreach($order_items as $item)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:50px;height:50px;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;flex-shrink:0;">
                                {!! render_image_markup_by_attachment_id($item->options->image ?? null, '', 'grid') !!}
                            </div>
                            <span style="font-size:13px;font-weight:600;color:var(--tr-bark);">{{ $item->name ?? '' }}</span>
                        </div>
                    </td>
                    <td style="font-size:12px;color:var(--tr-stone);">
                        @if($item->options->color_name ?? null) <span>{{ __('Color:') }} {{ $item->options->color_name }}</span><br>@endif
                        @if($item->options->size_name ?? null) <span>{{ __('Size:') }} {{ $item->options->size_name }}</span><br>@endif
                    </td>
                    <td style="color:var(--tr-stone);">{{ $item->qty ?? 1 }}</td>
                    <td style="color:var(--tr-stone);">{{ amount_with_currency_symbol($item->price ?? 0) }}</td>
                    <td style="font-weight:700;color:var(--tr-bark);">{{ amount_with_currency_symbol(($item->price ?? 0) * ($item->qty ?? 1)) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Billing Info --}}
    <div class="col-md-6">
        <div class="tr-dash-card" style="height:100%;margin-bottom:0;">
            <div class="tr-dash-card-header">
                <span><i class="mdi mdi-map-marker-outline" style="margin-right:6px;"></i>{{ __('Billing Info') }}</span>
            </div>
            <div class="tr-dash-card-body" style="font-size:13px;color:var(--tr-bark);line-height:2;">
                <strong style="color:var(--tr-olive);">{{ $order->billing_name ?? $order->name ?? '' }}</strong><br>
                {{ $order->billing_email ?? $order->email ?? '' }}<br>
                {{ $order->billing_phone ?? $order->phone ?? '' }}<br>
                {{ implode(', ', array_filter([
                    $order->billing_city ?? $order->city ?? null,
                    $order->billing_state ?? $order->state ?? null,
                    $order->billing_country ?? $order->country ?? null
                ])) }}
            </div>
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="col-md-6">
        <div class="tr-dash-card" style="height:100%;margin-bottom:0;">
            <div class="tr-dash-card-header">
                <span><i class="mdi mdi-calculator-variant-outline" style="margin-right:6px;"></i>{{ __('Summary') }}</span>
            </div>
            <div class="tr-dash-card-body">
                @php $order_meta = json_decode($order->payment_meta ?? '{}'); @endphp
                @foreach([
                    [__('Subtotal'), $order_meta->subtotal ?? 0],
                    [__('Shipping'), $order_meta->shipping_cost ?? 0],
                    [__('Tax'), $order_meta->product_tax ?? 0],
                    [__('Discount'), $order->coupon_discounted ?? 0],
                ] as [$label, $val])
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:7px 0;border-bottom:1px solid var(--tr-border);">
                    <span style="color:var(--tr-stone);">{{ $label }}</span>
                    <span style="font-weight:600;color:var(--tr-bark);">{{ amount_with_currency_symbol($val) }}</span>
                </div>
                @endforeach
                <div style="display:flex;justify-content:space-between;padding:12px 0 0;font-weight:800;">
                    <span style="color:var(--tr-bark);">{{ __('Total') }}</span>
                    <span style="font-size:18px;color:var(--tr-olive);">{{ amount_with_currency_symbol($order->total) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="margin-top:20px;">
    <a href="{{ route('tenant.user.dashboard.package.order') }}" class="tr-btn tr-btn-outline">
        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Orders') }}
    </a>
</div>

@endsection
