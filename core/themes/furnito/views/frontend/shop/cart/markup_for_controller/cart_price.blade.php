@php
    $subtotal = 0;
    foreach (Cart::content() as $item) {
        $subtotal += calculatePrice($item->price, $item->options) * $item->qty;
    }
@endphp

<h3 class="fn-summary-title">{{ __('Cart Total') }}</h3>

<div class="fn-summary-row">
    <span class="fn-summary-label">{{ __('Subtotal') }}</span>
    <span class="fn-summary-value">{{ amount_with_currency_symbol($subtotal) }}</span>
</div>

{!! apply_filters('nazmart:cart_summary', '') !!}

<div class="fn-summary-total">
    <span class="fn-summary-label">{{ __('Total') }}</span>
    <span class="fn-summary-value">{{ amount_with_currency_symbol($subtotal) }}</span>
</div>

<a href="{{ theme_checkout_url() }}" class="fn-btn fn-btn-gold w-100 mt-4">
    {{ __('Proceed to Checkout') }} <i class="las la-arrow-right"></i>
</a>
