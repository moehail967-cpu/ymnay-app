<div class="hf-checkout-summary mt-4">
    <h2 class="hf-cart-summary-title"> {{__('Order Summary')}} </h2>
    <form action="#" class="hf-coupon-row coupon-contents-form mt-4">
        <input type="hidden" class="coupon-country" name="coupon_country"
               value="{{$billing_info ? $billing_info->country_id: ''}}">
        <input type="hidden" class="coupon-state" name="coupon_state"
               value="{{$billing_info ? $billing_info->state_id: ''}}">
        <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method"
               value="">
        <input class="hf-form-input coupon-code" type="text" placeholder="{{__('Coupon Code')}}"
               name="used_coupon">
        <button type="button" class="hf-btn hf-btn-outline coupon-btn"> {{__('Apply Coupon')}}</button>
    </form>

    <div class="hf-checkout-items coupon-border mt-4 mb-4">
        @foreach($cart_data as $data)
            <div class="hf-checkout-item">
                <div class="hf-checkout-item-img">
                    <a href="javascript:void(0)">
                        {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                    </a>
                </div>
                <div class="hf-checkout-item-name">
                    <a href="javascript:void(0)"> {{$data->name}} </a>
                    <span class="hf-cart-meta d-block mt-1">
                        @if($data?->options?->color_name)
                            {{__('Color:')}} {{$data?->options?->color_name}} ,
                        @endif
                        @if($data?->options?->size_name)
                            {{__('Size:')}} {{$data?->options?->size_name}}
                        @endif
                        @if($data?->options?->attributes)
                            <br>
                            @foreach($data?->options?->attributes as $key => $attribute)
                                {{$key.':'}} {{$attribute}}{{!$loop->last ? ',' : ''}}
                            @endforeach
                        @endif
                    </span>
                    <span class="hf-cart-meta"> {{__('Quantity:')}} {{$data->qty}} </span>
                </div>
                <span class="hf-checkout-item-price"> {{amount_with_currency_symbol($data->price * $data->qty)}} </span>
            </div>
        @endforeach
    </div>

    <div class="coupon-contents-details">
        <div class="hf-summary-row">
            <span> {{__('Sub Total')}} </span>
            <span> {{site_currency_symbol().Cart::subtotal()}} </span>
        </div>
        <div class="shipping_method_wrapper">
            @php
                $has_delivery_address = false;
                $user = Auth::guard('web')->user();
                if ($user != null)
                {
                    if ($user?->delivery_address != null)
                    {
                        $has_delivery_address = true;
                        $country = (string)($user?->delivery_address?->country_id);
                        $state = (string)($user?->delivery_address?->state_id);

                        $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)->whereJsonContains('state', $state)
                        ->pluck('zone_id')
                        ->toArray();

                        if (empty($shipping_zones))
                            {
                                $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)
                                ->pluck('zone_id')
                                ->toArray();
                            }

                        $shipping_methods = \Modules\ShippingModule\Entities\ShippingMethod::with('options')->whereIn('zone_id', $shipping_zones)->get();
                    }
                }
            @endphp

            @if($user != null)
                <div class="hf-summary-row d-block">
                    <strong style="font-size:13px;">{{__('Shipping')}}</strong>
                    @foreach($shipping_methods ?? [] as $key => $method)
                        @php
                            $is_default = false;
                            if ($key == 0)
                            {
                                $is_default = true;
                                $default_shipping = $method;
                            }
                        @endphp

                        <div class="hf-summary-row" data-country="{{isset($country) ?? 0}}"
                            data-state="{{isset($state) ?? 0}}">
                            <span class="coupon-radio-item">
                                <input type="radio" class="shipping_methods"
                                       id="shipping-option-{{$method['id']}}"
                                       name="shipping_method" value="{{$method['id']}}">
                                <label for="shipping-option-{{$method['id']}}">{{$method['name']}}</label>
                            </span>
                            <span>{{amount_with_currency_symbol($method['options']['cost'])}}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="shipping_tax_total">
                <div class="hf-summary-row">
                    <span> {{__('Tax (Incl)')}} </span>
                    <span> {{$product_tax.'%'}} </span>
                </div>
                <div class="hf-summary-row coupon-price">
                    <span> {{__('Coupon Discount (-)')}} </span>
                    <span> {{amount_with_currency_symbol(0.00)}} </span>
                </div>
                <div class="hf-summary-row price-shipping">
                    <span> {{__('Shipping Cost (+)')}} </span>
                    <span> {{isset($is_default) && $is_default ? amount_with_currency_symbol($default_shipping['options']['cost']) : '--'}} </span>
                </div>

                @php
                    $subtotal = (double) Cart::subtotal(0, '', '');
                    $taxed_price = ($subtotal * $product_tax) / 100;

                    $shipping_tax = 0;
                    if (Auth::guard('web')->check())
                    {
                        if ($has_delivery_address)
                        {
                            if(isset($default_shipping) && $default_shipping?->options?->tax_status)
                            {
                                $shipping_tax = ($default_shipping['options']['cost'] * $product_tax) / 100;
                            }
                            $shipping = isset($is_default) && $is_default ? $default_shipping['options']['cost'] ?? 0 : 0;
                        } else {
                            $shipping = 0;
                        }
                    } else {
                        $shipping = 0;
                    }

                    $total = $subtotal + ($taxed_price + $shipping_tax) + $shipping;
                @endphp
                <div class="hf-summary-row hf-summary-total price-total"
                    data-total="{{$subtotal+$taxed_price}}" {{!isset($is_default) ? 'data-total='.$total.'' : ''}}
                ">
                    <span> {{__('Total Amount')}} </span>
                    <span class="coupon-price"> {{site_currency_symbol().$total}} </span>
                </div>
            </div>
        </div>
    </div>

    @php $COD = get_static_option('cash_on_delivery'); @endphp
    @if($COD === 'on')
        <div class="hf-check-row cash-on-delivery mt-2">
            <input class="check-input"
                   type="checkbox"
                   id="cash"
                   name="cash_on_delivery"
                   value="on"
                {{ !empty(get_static_option('site_default_payment_gateway') === 'cash_on_delivery') ? 'checked' : '' }}
            >
            <input type="hidden" name="selected_payment_gateway" value="cash_on_delivery">
            <label for="cash"> {{__('Cash On Delivery')}} </label>
        </div>
    @endif

    <div class="hf-payment-section mt-4">
        <h6 class="hf-payment-label"> {{__('Select Payment Method')}} </h6>
        <div class="hf-payment-card payment-card mt-3">
            {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
            <div class="d-none w-100 mt-3 manual_transaction_id">
                <p class="alert alert-info manual_description"></p>
                <input type="text" name="trasaction_id" class="hf-form-input"
                       placeholder="{{__('Transaction ID')}}">
            </div>
            <input type="hidden" id="site_global_payment_gateway"
                   value="{{get_static_option('site_default_payment_gateway')}}">
        </div>
    </div>

    <div class="hf-check-row mt-3">
        <input class="check-input" type="checkbox" id="agree" checked>
        <label for="agree"> {{__('I have read and agree to the website')}} <a
                class="hf-terms-link terms-condition" href="javascript:void(0)"> {{__('terms and conditions*')}} </a> </label>
    </div>

    <div class="mt-3">
        <a href="javascript:void(0)"
           class="hf-btn hf-btn-primary hf-btn-block checkout_disable proceed_checkout_btn"> {{__('Proceed to Checkout')}} </a>
    </div>

    <div class="mt-3">
        <a href="{{theme_cart_url()}}" class="hf-btn hf-btn-outline hf-btn-block"> {{__('Return to Cart')}} </a>
    </div>
</div>
