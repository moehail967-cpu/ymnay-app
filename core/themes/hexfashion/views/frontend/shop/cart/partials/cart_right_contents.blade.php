<div class="hf-cart-summary">
    <h4 class="hf-cart-summary-title">{{__('Cart Total')}}</h4>
    <div class="coupon-contents hf-cart-summary-body">
        @php
            $subtotal = 0;
            $carts = Cart::content();
            foreach ($carts ?? [] as $item) {
                $subtotal += calculatePrice($item->price, $item->options) * $item->qty;
            }
            $total = $subtotal;
        @endphp
        <div class="hf-cart-summary-row">
            <span>{{__('Sub Total')}}</span>
            <span>{{amount_with_currency_symbol($subtotal)}}</span>
        </div>
        <div class="hf-cart-summary-row">
            <span>{{__('Tax')}} ({{__('Incl')}}. --%) </span>
            <span>--</span>
        </div>
        <div class="hf-cart-summary-row hf-cart-summary-total">
            <span>{{__('Total Amount')}}</span>
            <span>{{amount_with_currency_symbol($total)}}</span>
        </div>
        {!! apply_filters('nazmart:cart_summary', '') !!}
        <a href="{{theme_checkout_url()}}" class="hf-btn hf-btn-primary hf-btn-block mt-3">{{__('Proceed to Checkout')}}</a>
    </div>
</div>
