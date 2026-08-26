<div class="col-xl-4 col-lg-5 mt-4">
    <div class="ar-order-summary">

        {{-- Coupon --}}
        <div class="ar-coupon-form">
            <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
            <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
            <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
            <div class="ar-coupon-wrap">
                <input class="ar-input coupon-code" type="text" placeholder="{{ __('Coupon Code') }}" name="used_coupon">
                <button type="button" class="ar-btn ar-btn-red ar-coupon-btn coupon-btn">{{ __('Apply') }}</button>
            </div>
        </div>

        {{-- Order items --}}
        <h4 class="ar-order-summary-title">{{ __('Order Summary') }}</h4>

        <div class="ar-order-items">
            @php
                $subtotal = null;
                $itemsTotal = null;
                $v_tax_total = 0;
            @endphp
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

                <div class="ar-order-item">
                    <div class="ar-order-item-thumb">
                        {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                        <span class="ar-order-item-qty">{{ $data->qty }}</span>
                    </div>
                    <div class="ar-order-item-info">
                        <span class="ar-order-item-name">{{ $data->name }}</span>
                        <span class="ar-order-item-variants">
                            @if($data?->options?->color_name)
                                {{ __('Color:') }} {{ $data?->options?->color_name }}@if($data?->options?->size_name), @endif
                            @endif
                            @if($data?->options?->size_name)
                                {{ __('Size:') }} {{ $data?->options?->size_name }}
                            @endif
                            @if($data?->options?->attributes)
                                @foreach($data?->options?->attributes as $key => $attribute)
                                    {{ $key.':' }} {{ $attribute }}{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            @endif
                        </span>
                    </div>
                    <span class="ar-order-item-price">{{ amount_with_currency_symbol($price * $data->qty) }}</span>
                </div>

                @php
                    $subtotal += $price * $data->qty;
                    $itemsTotal = $subtotal + $v_tax_total;
                @endphp
            @endforeach
        </div>

        {{-- Price breakdown --}}
        <div class="shipping_method_wrapper">
            @php
                $tax_ = 0;
                $has_delivery_address = false;
                $user = Auth::guard('web')->user();

                if ($user != null) {
                    if ($user?->delivery_address != null) {
                        $has_delivery_address = true;
                        $country = (string)($user?->delivery_address?->country_id);
                        $state   = (string)($user?->delivery_address?->state_id);

                        $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)
                            ->whereJsonContains('state', $state)->pluck('zone_id')->toArray();

                        if (empty($shipping_zones)) {
                            $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)
                                ->pluck('zone_id')->toArray();
                        }

                        $shipping_methods = \Modules\ShippingModule\Entities\ShippingMethod::with('options')
                            ->whereIn('zone_id', $shipping_zones)->get();

                        $location_tax_data = get_product_shipping_tax_data($user?->delivery_address);
                        $tax_ = calculatePercentageAmount($itemsTotal, $location_tax_data['product_tax'] ?? 0) ?? 0;
                    }
                }
            @endphp

            <div class="ar-order-rows">
                <div class="ar-order-row">
                    <span>{{ __('Sub Total') }}</span>
                    <span>{{ amount_with_currency_symbol($subtotal) }}</span>
                </div>

                @if($user != null)
                    @foreach($shipping_methods ?? [] as $key => $method)
                        @php
                            $is_default = false;
                            if ($method->is_default) {
                                $is_default = true;
                                $default_shipping = $method;
                            }
                        @endphp
                        <div class="ar-order-row" data-country="{{ isset($country) ? $country : 0 }}"
                             data-state="{{ isset($state) ? $state : 0 }}" data-city="{{ isset($city) ? $city : 0 }}">
                            <span class="ar-shipping-radio">
                                <input type="radio" class="shipping_methods" id="shipping-option-{{ $method['id'] }}"
                                       name="shipping_method" value="{{ $method['id'] }}">
                                <label for="shipping-option-{{ $method['id'] }}">{{ $method['name'] }}</label>
                            </span>
                            <span>{{ amount_with_currency_symbol(calculatePrice($method['options']['cost'], $shippingTaxClass, "shipping")) }}</span>
                        </div>
                    @endforeach
                @endif

                @if(get_static_option('tax_system') == 'advance_tax_system')
                    @if($enableTaxAmount)
                        <div class="ar-order-row">
                            <span>{{ __('Tax (Incl)') }}</span>
                            <span>{{ amount_with_currency_symbol($v_tax_total) }}</span>
                        </div>
                    @else
                        <div class="ar-order-row">
                            <span>{{ __('Tax (Incl)') }}</span>
                            <span>{{ get_static_option("display_price_in_the_shop") == 'including' ? __("Inclusive Tax") : "" }}</span>
                        </div>
                    @endif
                @else
                    <div class="ar-order-row">
                        <span>{{ __('Tax (Incl)') }}</span>
                        <span>{{ amount_with_currency_symbol($tax_) }}</span>
                    </div>
                @endif

                <div class="ar-order-row coupon-price">
                    <span>{{ __('Coupon Discount (-)') }}</span>
                    <span>{{ amount_with_currency_symbol(0.00) }}</span>
                </div>
                <div class="ar-order-row price-shipping">
                    <span>{{ __('Shipping Cost (+)') }}</span>
                    <span>{{ isset($is_default) && $is_default ? amount_with_currency_symbol(0) : '--' }}</span>
                </div>
            </div>

            @if(get_static_option('tax_system') == 'advance_tax_system')
                <div class="ar-order-row ar-order-row-total price-total"
                     data-total="{{ $subtotal + $v_tax_total }}" {{ !isset($is_default) ? 'data-total='.$itemsTotal.'' : '' }}>
                    <h6 class="ar-order-total-label">{{ __('Total Amount') }}</h6>
                    <span class="ar-order-total-val">{{ amount_with_currency_symbol($itemsTotal) }}</span>
                </div>
            @else
                <div class="ar-order-row ar-order-row-total price-total"
                     data-total="{{ $subtotal + $tax_ }}" {{ !isset($is_default) ? 'data-total='.($itemsTotal + $tax_).'' : '' }}>
                    <h6 class="ar-order-total-label">{{ __('Total Amount') }}</h6>
                    <span class="ar-order-total-val">{{ amount_with_currency_symbol($itemsTotal + $tax_) }}</span>
                </div>
            @endif
        </div>

        {!! apply_filters('nazmart:cart_summary', '') !!}

        {{-- Cash on delivery --}}
        @php $COD = get_static_option('cash_on_delivery'); @endphp
        @if($COD === 'on')
            <div class="ar-cod-wrap">
                <label class="ar-cod-label">
                    <input class="check-input" type="checkbox" id="cash" name="cash_on_delivery" value="on"
                        {{ !empty(get_static_option('site_default_payment_gateway') === 'cash_on_delivery') ? 'checked' : '' }}>
                    <input type="hidden" name="selected_payment_gateway" value="cash_on_delivery">
                    <span>{{ __('Cash On Delivery') }}</span>
                </label>
            </div>
        @endif

        {{-- Payment methods --}}
        <div class="ar-payment-section mt-3">
            <h6 class="ar-payment-label">{{ __('Select Payment Method') }}</h6>
            <div class="payment-gateway-wrapper mt-2">
                {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForUseLandlordGateway() !!}
                <div class="form-group d-none w-100 mt-3 manual_transaction_id">
                    <p class="alert alert-info manual_description"></p>
                    <input type="text" name="trasaction_id" class="ar-input" placeholder="{{ __('Transaction ID') }}">
                </div>
                <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
            </div>
        </div>

        {{-- Terms --}}
        <div class="ar-agree-wrap">
            <label class="ar-agree-label">
                <input class="check-input" type="checkbox" id="agree" checked>
                <span>{{ __('I have read and agree to the website') }}
                    <a href="javascript:void(0)" class="ar-terms-link">{{ __('terms and conditions*') }}</a>
                </span>
            </label>
        </div>

        {{-- Place order --}}
        <div class="mt-3">
            <a href="javascript:void(0)" class="ar-btn ar-btn-red w-100 justify-content-center checkout_disable proceed_checkout_btn">
                {{ __('Place Order') }} <i class="las la-lock"></i>
            </a>
        </div>
        <div class="mt-2">
            <a href="{{ theme_cart_url() }}" class="ar-btn ar-btn-outline w-100 justify-content-center">
                <i class="las la-arrow-left"></i> {{ __('Return to Cart') }}
            </a>
        </div>
    </div>
</div>
