@php
    $subtotal = 0;
    $carts = \Gloudemans\Shoppingcart\Facades\Cart::content();
    foreach ($carts ?? [] as $item) {
        $subtotal += calculatePrice($item->price, $item->options) * $item->qty;
    }
    $total = $subtotal;
@endphp

<div class="ar-summary-lines">
    <div class="ar-summary-line">
        <span>{{ __('Sub Total') }}</span>
        <span class="ar-summary-val">{{ amount_with_currency_symbol($subtotal) }}</span>
    </div>
    <div class="ar-summary-line">
        <span>{{ __('Tax (Incl)') }}</span>
        <span class="ar-summary-val">--</span>
    </div>
    <div class="ar-summary-line ar-summary-total">
        <span>{{ __('Total Amount') }}</span>
        <span class="ar-summary-val-total">{{ amount_with_currency_symbol($total) }}</span>
    </div>
</div>

{!! apply_filters('nazmart:cart_summary', '') !!}

<a href="{{ theme_checkout_url() }}"
   class="ar-btn ar-btn-red w-100 justify-content-center mt-4">
    {{ __('Proceed to Checkout') }} <i class="mdi mdi-arrow-right"></i>
</a>

<p class="ar-cart-secure-note">
    <i class="mdi mdi-shield-check ar-cart-secure-icon"></i>
    {{ __('Secure & encrypted checkout') }}
</p>

<div class="ar-cart-trust-icons">
    <i class="mdi mdi-lock" title="{{ __('Secure') }}"></i>
    <i class="mdi mdi-credit-card-outline" title="{{ __('Cards accepted') }}"></i>
    <i class="mdi mdi-shield-check" title="{{ __('Protected') }}"></i>
</div>
