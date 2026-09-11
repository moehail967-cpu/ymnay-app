<div class="col-lg-4">
    <div class="bp-coupon-card">
        <label class="bp-coupon-label"><i class="las la-tag"></i> {{ __('Have a coupon?') }}</label>
        <div class="d-flex gap-2">
            <input type="text" id="bp-cart-coupon-input" class="bp-coupon-input flex-grow-1" placeholder="{{ __('Enter coupon code') }}">
            <button type="button" class="bp-btn bp-btn-green bp-btn-sm" id="bp-cart-coupon-btn">{{ __('Apply') }}</button>
        </div>
        <div id="bp-cart-coupon-msg" class="bp-coupon-msg"></div>
    </div>
    <div class="bp-order-summary mt-3">
        <div class="bp-summary-title">{{ __('Order Summary') }}</div>
        <div class="coupon-contents">
            @php
                $subtotal = \Gloudemans\Shoppingcart\Facades\Cart::subtotal(2, '.', ',');
                $total    = \Gloudemans\Shoppingcart\Facades\Cart::priceTotal(2, '.', ',');
            @endphp
            <div class="bp-summary-row"><span>{{ __('Subtotal') }}</span><span class="bp-summary-price">{{ site_currency_symbol() }}{{ $subtotal }}</span></div>
            <div class="bp-summary-row"><span>{{ __('Tax (Incl)') }}</span><span class="bp-summary-price">--</span></div>
            <div class="bp-summary-row bp-summary-total"><span>{{ __('Total Amount') }}</span><span class="bp-summary-price">{{ site_currency_symbol() }}{{ $total }}</span></div>
        </div>
        {!! apply_filters('nazmart:cart_summary', '') !!}
        <a href="{{ theme_checkout_url() }}" class="bp-btn bp-btn-green w-100 justify-content-center mt-4" style="font-size:15px;padding:13px;">
            {{ __('Proceed to Checkout') }} <i class="las la-arrow-right"></i>
        </a>
        <p style="font-size:11px;color:#888;text-align:center;margin-top:12px;">
            <i class="las la-shield-alt" style="color:var(--bp-accent);"></i> {{ __('Secure & encrypted checkout') }}
        </p>
    </div>
</div>
