<div class="col-lg-4">
    <div class="bp-order-summary">
        <div class="bp-summary-title">{{ __('Order Summary') }}</div>

        <div style="margin-bottom:16px;">
            @php $subtotal = null; $itemsTotal = null; $v_tax_total = 0; @endphp
            @foreach($carts ?? [] as $data)
            @php
                $default_shipping_cost = null;
                $taxAmount = $taxProducts->where("id", $data->id)->first();
                if (!empty($taxAmount)) {
                    $taxAmount->tax_options_sum_rate = $taxAmount->tax_options_sum_rate ?? 0;
                    $price = calculatePrice($data->price, $taxAmount);
                    $regular_price = calculatePrice($data->options->regular_price, $data->options);
                    $v_tax_total += calculatePrice($data->price, $taxAmount, "percentage") * $data->qty;
                } else {
                    $price = calculatePrice($data->price, $data->options);
                    $regular_price = calculatePrice($data->options->regular_price, $data->options);
                }
            @endphp
            <div class="bp-mini-cart-row">
                <div class="bp-mini-cart-img">
                    {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                </div>
                <div class="bp-checkout-mini-item-meta flex-grow-1">
                    <div class="bp-order-item-name">{{ $data->name }}</div>
                    <div style="font-size:12px;color:#888;">{{ __('Qty:') }} {{ $data->qty }}</div>
                </div>
                <div class="bp-order-item-price">{{ amount_with_currency_symbol($price * $data->qty) }}</div>
            </div>
            @php $subtotal += $price * $data->qty; $itemsTotal = $subtotal + $v_tax_total; @endphp
            @endforeach
        </div>

        <div style="border-top:1px solid #f0f0f0;padding-top:16px;margin-bottom:16px;">
            <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
            <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
            <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
            <label class="bp-label" style="margin-bottom:6px;">{{ __('Coupon Code') }}</label>
            <div class="d-flex gap-2">
                <input class="bp-input coupon-code flex-grow-1" type="text" placeholder="{{ __('Coupon Code') }}" name="used_coupon">
                <button type="button" class="bp-btn bp-btn-green bp-btn-sm coupon-btn">{{ __('Apply') }}</button>
            </div>
        </div>

        <div class="shipping_method_wrapper" style="border-top:1px solid #f0f0f0;padding-top:16px;">
            <div class="shipping_tax_total">
                <div class="bp-summary-row">
                    <span>{{ __('Subtotal') }}</span>
                    <span class="bp-summary-price">{{ amount_with_currency_symbol($subtotal) }}</span>
                </div>
                <div class="bp-summary-row coupon-price">
                    <span>{{ __('Coupon Discount (-)') }}</span>
                    <span>{{ amount_with_currency_symbol(0.00) }}</span>
                </div>
                <div class="bp-summary-row bp-summary-total price-total" data-total="{{ $subtotal + $v_tax_total }}">
                    <span>{{ __('Total Amount') }}</span>
                    <span class="bp-summary-price">{{ amount_with_currency_symbol($itemsTotal) }}</span>
                </div>
            </div>
        </div>

        {!! apply_filters('nazmart:cart_summary', '') !!}

        @php $COD = get_static_option('cash_on_delivery'); @endphp
        @if($COD === 'on')
        <div class="bp-summary-row mt-3">
            <label class="bp-checkout-cod-label d-flex align-items-center gap-2">
                <input class="bp-checkout-accent-sm" type="checkbox" id="cash" name="cash_on_delivery" value="on"
                    {{ !empty(get_static_option('site_default_payment_gateway') === 'cash_on_delivery') ? 'checked' : '' }}>
                <i class="las la-truck bp-checkout-cod-icon"></i> {{ __('Cash On Delivery') }}
            </label>
            <input type="hidden" name="selected_payment_gateway" value="cash_on_delivery">
        </div>
        @endif

        <div class="mt-3">
            <div class="bp-label" style="margin-bottom:10px;">{{ __('Select Payment Method') }}</div>
            <div class="payment-gateway-wrapper">
                {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
            </div>
            <div class="d-none w-100 mt-3 manual_transaction_id">
                <div class="bp-checkout-summernot summernot_wrap">
                    <p class="bp-checkout-manual-desc manual_description"></p>
                </div>
                <input type="text" name="trasaction_id" class="bp-input mt-2" placeholder="{{ __('Transaction ID') }}">
            </div>
            <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
        </div>

        <div class="mt-3">
            <label class="bp-checkout-terms-label d-flex align-items-center gap-2">
                <input class="bp-checkout-accent" type="checkbox" id="agree" checked>
                {{ __('I have read and agree to the website') }}
                <a href="javascript:void(0)" class="bp-checkout-terms-link">{{ __('terms and conditions*') }}</a>
            </label>
        </div>
        <div class="mt-3">
            <a href="javascript:void(0)"
               class="bp-btn bp-btn-green w-100 justify-content-center bp-checkout-place-order checkout_disable proceed_checkout_btn">
                <i class="las la-lock"></i> {{ __('Proceed to Checkout') }}
            </a>
        </div>
        <div class="mt-2">
            <a href="{{ theme_cart_url() }}" class="bp-btn bp-btn-outline w-100 justify-content-center">
                <i class="las la-arrow-left"></i> {{ __('Return to Cart') }}
            </a>
        </div>
    </div>
</div>
