<div class="col-xl-4 mt-4">
    <div class="ar-cart-summary">
        <div class="coupon-contents">
            @php
                $subtotal = 0;
                $carts = Cart::content();
                foreach ($carts ?? [] as $item) {
                    $subtotal += calculatePrice($item->price, $item->options) * $item->qty;
                }
                $total = $subtotal;
            @endphp

            <h4 class="ar-summary-heading">{{ __('Cart Total') }}</h4>

            <div class="ar-summary-lines mt-3">
                <div class="ar-summary-line">
                    <span>{{ __('Sub Total') }}</span>
                    <span class="ar-summary-val">{{ amount_with_currency_symbol($subtotal) }}</span>
                </div>
                <div class="ar-summary-line">
                    <span>{{ __('Tax') }}</span>
                    <span class="ar-summary-val">--</span>
                </div>
                <div class="ar-summary-line ar-summary-total">
                    <span>{{ __('Total Amount') }}</span>
                    <span class="ar-summary-val-total">{{ amount_with_currency_symbol($total) }}</span>
                </div>
            </div>

            {!! apply_filters('nazmart:cart_summary', '') !!}

            <div class="mt-4">
                <a href="{{ theme_checkout_url() }}" class="ar-btn ar-btn-red w-100 justify-content-center">
                    {{ __('Proceed to Checkout') }} <i class="las la-arrow-right"></i>
                </a>
            </div>
            <div class="mt-2">
                <a href="{{ theme_shop_url() }}" class="ar-btn ar-btn-outline w-100 justify-content-center">
                    {{ __('Continue Shopping') }}
                </a>
            </div>
        </div>
    </div>
</div>
