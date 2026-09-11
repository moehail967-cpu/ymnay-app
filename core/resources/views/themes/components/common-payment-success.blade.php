@php
    $meta        = json_decode($payment_details->payment_meta ?? '{}');
    $orderItems  = json_decode($payment_details->order_details ?? '[]');
    $subtotal    = $meta?->subtotal ?? 0;
    $tax         = $meta?->product_tax ?? 0;
    $shipping    = $meta?->shipping_cost ?? 0;

    $discountAmount = 0;
    $discountType   = $payment_details?->product_coupon?->discount_type;
    $discountValue  = $payment_details?->product_coupon?->discount;
    if ($discountType === 'percentage' && $discountValue) {
        $discountAmount = ($subtotal * $discountValue) / 100;
    } elseif ($discountType === 'amount' && $discountValue) {
        $discountAmount = $discountValue;
    }
@endphp

<style>
/* ── Payment Success Page ─────────────────────── */
.ps-page { padding: 64px 0 80px; }

/* Header */
.ps-header { text-align: center; margin-bottom: 48px; }
.ps-check-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px; height: 80px;
    border-radius: 50%;
    background: rgba(var(--main-color-one-rgb, 30,136,229), .1);
    margin-bottom: 20px;
    animation: psPopIn .5s cubic-bezier(.34,1.56,.64,1) both;
}
.ps-check-wrap svg { width: 40px; height: 40px; }
.ps-check-wrap svg path {
    stroke: var(--main-color-one, #1e88e5);
    stroke-dasharray: 60;
    stroke-dashoffset: 60;
    animation: psDraw .6s .3s ease forwards;
}
.ps-thank { font-size: 28px; font-weight: 700; color: var(--heading-color, #1a1a1a); margin: 0 0 8px; line-height: 1.2; }
.ps-sub   { font-size: 15px; color: #6b7280; margin: 0 0 20px; }
.ps-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 16px;
    border-radius: 999px;
    background: rgba(var(--main-color-one-rgb,30,136,229),.08);
    border: 1px solid rgba(var(--main-color-one-rgb,30,136,229),.2);
    font-size: 13px;
    font-weight: 600;
    color: var(--main-color-one, #1e88e5);
}

/* Cards */
.ps-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.04);
}
.ps-card-head {
    padding: 16px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ps-card-head-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: rgba(var(--main-color-one-rgb,30,136,229),.1);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.ps-card-head-icon svg { width: 16px; height: 16px; color: var(--main-color-one,#1e88e5); }
.ps-card-title { font-size: 14px; font-weight: 700; color: #111827; margin: 0; }
.ps-card-body { padding: 20px 24px; }

/* Info rows */
.ps-info-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 13.5px;
}
.ps-info-row:last-child { border-bottom: none; padding-bottom: 0; }
.ps-info-label { color: #6b7280; font-weight: 500; flex-shrink: 0; }
.ps-info-val   { color: #111827; font-weight: 600; text-align: right; word-break: break-all; }
.ps-status-pill {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: capitalize;
    background: #d1fae5;
    color: #065f46;
}

/* Items table */
.ps-table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
.ps-table thead th {
    padding: 10px 12px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #9ca3af;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}
.ps-table thead th:last-child { text-align: right; }
.ps-table tbody td { padding: 12px 12px; vertical-align: top; border-bottom: 1px solid #f3f4f6; color: #374151; }
.ps-table tbody tr:last-child td { border-bottom: none; }
.ps-table td:last-child { text-align: right; font-weight: 600; color: #111827; white-space: nowrap; }
.ps-item-meta { font-size: 11.5px; color: #9ca3af; margin-top: 3px; }
.ps-qty { display: inline-flex; align-items: center; justify-content: center; min-width: 28px; height: 22px; padding: 0 6px; background: #f3f4f6; border-radius: 6px; font-size: 12px; font-weight: 700; color: #374151; }

/* Totals */
.ps-totals { border-top: 1px solid #e5e7eb; margin-top: 4px; padding-top: 4px; }
.ps-total-row {
    display: flex;
    justify-content: space-between;
    padding: 7px 12px;
    font-size: 13px;
    color: #6b7280;
}
.ps-total-row span:last-child { font-weight: 600; color: #374151; }
.ps-total-row.is-grand {
    background: rgba(var(--main-color-one-rgb,30,136,229),.06);
    border-radius: 8px;
    margin: 6px 0;
    font-size: 14px;
    font-weight: 700;
    color: #111827;
}
.ps-total-row.is-grand span:last-child { color: var(--main-color-one,#1e88e5); }
.ps-total-row.is-discount span:last-child { color: #16a34a; }

/* Buttons */
.ps-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 24px; }
.ps-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 11px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all .18s ease;
}
.ps-btn-primary {
    background: var(--main-color-one, #1e88e5);
    color: #fff;
    border-color: var(--main-color-one, #1e88e5);
}
.ps-btn-primary:hover { opacity: .88; color: #fff; }
.ps-btn-outline {
    background: transparent;
    color: var(--main-color-one,#1e88e5);
    border-color: var(--main-color-one,#1e88e5);
}
.ps-btn-outline:hover { background: var(--main-color-one,#1e88e5); color: #fff; }

/* Animations */
@keyframes psPopIn {
    from { transform: scale(.5); opacity: 0; }
    to   { transform: scale(1);  opacity: 1; }
}
@keyframes psDraw {
    to { stroke-dashoffset: 0; }
}
</style>

<div class="ps-page">
    <div class="container">

        {{-- ── Success Header ───────────────────────────── --}}
        <div class="ps-header">
            <div class="ps-check-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="ps-thank">{{ __('Order Confirmed!') }}</h1>
            <p class="ps-sub">{{ get_static_option('site_order_success_page_description') ?: __('Thank you! Your order has been placed successfully.') }}</p>
            <span class="ps-badge">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                {{ __('Order') }} #{{ $payment_details->id }}
            </span>
        </div>

        {{-- ── Main layout ─────────────────────────────── --}}
        <div class="row g-4 justify-content-center">

            {{-- Left — Items --}}
            <div class="col-lg-7">
                <div class="ps-card">
                    <div class="ps-card-head">
                        <div class="ps-card-head-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                            </svg>
                        </div>
                        <p class="ps-card-title">{{ __('Order Items') }}</p>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="ps-table">
                            <thead>
                                <tr>
                                    <th>{{ __('Item') }}</th>
                                    <th style="text-align:center">{{ __('Qty') }}</th>
                                    <th>{{ __('Price') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItems as $item)
                                <tr>
                                    <td>
                                        <div style="font-weight:600;color:#111827;">{{ $item->name }}</div>
                                        @if(!empty($item?->options?->color_name) || !empty($item?->options?->size_name) || !empty($item?->options?->attributes))
                                        <div class="ps-item-meta">
                                            @if(!empty($item?->options?->color_name))
                                                {{ __('Color:') }} {{ $item->options->color_name }}
                                                @if(!empty($item?->options?->size_name)) · @endif
                                            @endif
                                            @if(!empty($item?->options?->size_name))
                                                {{ __('Size:') }} {{ $item->options->size_name }}
                                            @endif
                                            @if(!empty($item?->options?->attributes))
                                                @foreach($item->options->attributes as $k => $v)
                                                    · {{ $k }}: {{ $v }}
                                                @endforeach
                                            @endif
                                        </div>
                                        @endif
                                    </td>
                                    <td style="text-align:center"><span class="ps-qty">×{{ $item->qty }}</span></td>
                                    <td>{{ amount_with_currency_symbol($item->price * $item->qty) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="ps-totals">
                        <div class="ps-total-row">
                            <span>{{ __('Subtotal') }}</span>
                            <span>{{ amount_with_currency_symbol($subtotal) }}</span>
                        </div>
                        @if($discountAmount > 0)
                        <div class="ps-total-row is-discount">
                            <span>{{ __('Coupon Discount') }}</span>
                            <span>− {{ amount_with_currency_symbol($discountAmount) }}</span>
                        </div>
                        @endif
                        @if($tax)
                        <div class="ps-total-row">
                            <span>{{ __('Tax') }}</span>
                            <span>{{ amount_with_currency_symbol($tax) }}</span>
                        </div>
                        @endif
                        @if($shipping)
                        <div class="ps-total-row">
                            <span>{{ __('Shipping') }}</span>
                            <span>{{ amount_with_currency_symbol($shipping) }}</span>
                        </div>
                        @endif
                        <div class="ps-total-row is-grand">
                            <span>{{ __('Total') }}</span>
                            <span>{{ amount_with_currency_symbol($payment_details->total_amount) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="ps-actions">
                    @if(auth()->guard('web')->check())
                        <a href="{{ theme_user_dashboard_url() }}" class="ps-btn ps-btn-primary">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                            {{ __('Go to Dashboard') }}
                        </a>
                    @endif
                    <a href="{{ theme_home_url() }}" class="ps-btn ps-btn-outline">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        {{ __('Continue Shopping') }}
                    </a>
                </div>
            </div>

            {{-- Right — Details --}}
            <div class="col-lg-4">

                {{-- Order Details --}}
                <div class="ps-card" style="margin-bottom:16px;">
                    <div class="ps-card-head">
                        <div class="ps-card-head-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <p class="ps-card-title">{{ __('Order Details') }}</p>
                    </div>
                    <div class="ps-card-body">
                        <div class="ps-info-row">
                            <span class="ps-info-label">{{ __('Order ID') }}</span>
                            <span class="ps-info-val">#{{ $payment_details->id }}</span>
                        </div>
                        <div class="ps-info-row">
                            <span class="ps-info-label">{{ __('Payment Type') }}</span>
                            <span class="ps-info-val">{{ $payment_details->checkout_type == 'cod' ? __('Cash on Delivery') : __('Digital Payment') }}</span>
                        </div>
                        @if($payment_details->payment_gateway)
                        <div class="ps-info-row">
                            <span class="ps-info-label">{{ __('Gateway') }}</span>
                            <span class="ps-info-val">{{ $payment_details->payment_gateway }}</span>
                        </div>
                        @endif
                        @if($payment_details->transaction_id)
                        <div class="ps-info-row">
                            <span class="ps-info-label">{{ __('Transaction') }}</span>
                            <span class="ps-info-val" style="font-size:12px;">{{ $payment_details->transaction_id }}</span>
                        </div>
                        @endif
                        <div class="ps-info-row">
                            <span class="ps-info-label">{{ __('Payment') }}</span>
                            <span class="ps-info-val"><span class="ps-status-pill">{{ $payment_details->payment_status }}</span></span>
                        </div>
                        <div class="ps-info-row">
                            <span class="ps-info-label">{{ __('Order Status') }}</span>
                            <span class="ps-info-val"><span class="ps-status-pill">{{ $payment_details->status }}</span></span>
                        </div>
                    </div>
                </div>

                {{-- Billing Details --}}
                <div class="ps-card">
                    <div class="ps-card-head">
                        <div class="ps-card-head-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <p class="ps-card-title">{{ __('Billing Details') }}</p>
                    </div>
                    <div class="ps-card-body">
                        <div class="ps-info-row">
                            <span class="ps-info-label">{{ __('Name') }}</span>
                            <span class="ps-info-val" style="text-transform:capitalize;">{{ $payment_details->name }}</span>
                        </div>
                        <div class="ps-info-row">
                            <span class="ps-info-label">{{ __('Email') }}</span>
                            <span class="ps-info-val" style="font-size:12.5px;text-transform:lowercase;">{{ $payment_details->email }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>{{-- /row --}}
    </div>
</div>
