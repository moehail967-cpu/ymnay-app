@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Checkout') }} @endsection
@section('page-title') {{ __('Checkout') }} @endsection

@section('content')
@if(Cart::count() > 0)

<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ __('Checkout') }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ __('Checkout') }}</span>
        </div>
    </div>
</div>

<div class="container cs-checkout-steps-wrap">
    <div class="cs-cart-steps">
        <div class="cs-cart-step">
            <span class="cs-cart-step-num"><i class="las la-check"></i></span>
            <span class="cs-cart-step-label">{{ __('Cart') }}</span>
        </div>
        <div class="cs-cart-step-line cs-step-done"></div>
        <div class="cs-cart-step active">
            <span class="cs-cart-step-num">2</span>
            <span class="cs-cart-step-label">{{ __('Checkout') }}</span>
        </div>
        <div class="cs-cart-step-line"></div>
        <div class="cs-cart-step">
            <span class="cs-cart-step-num">3</span>
            <span class="cs-cart-step-label">{{ __('Confirmed') }}</span>
        </div>
    </div>
</div>

<div class="container cs-checkout-wrap">
    <x-error-msg/>

    <div class="row g-4">

        {{-- ===== Left: Billing Form ===== --}}
        <div class="col-lg-7">

            @if(!empty(get_static_option('guest_order_system_status')) && !theme_is_logged_in())
            <div class="cs-checkout-box mb-4">
                <div class="cs-checkout-box-title"><i class="las la-user"></i> {{ __('Sign In to Continue') }}</div>
                <x-flash-msg/>
                <form id="login_form_order_page">
                    <div class="error-wrap mb-3"></div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="cs-checkout-label">{{ __('Username') }}</label>
                            <input type="text" name="username" class="cs-checkout-input" placeholder="{{ __('Type your username') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="cs-checkout-label">{{ __('Password') }}</label>
                            <input type="password" name="password" class="cs-checkout-input" placeholder="{{ __('Password') }}">
                        </div>
                        <div class="col-12 d-flex align-items-center justify-content-between">
                            <label class="cs-checkout-remember">
                                <input type="checkbox" name="remember"> {{ __('Remember me') }}
                            </label>
                            <a href="{{ theme_forget_password_url() }}" class="cs-checkout-forgot">{{ __('Forgot Password?') }}</a>
                        </div>
                        <div class="col-12">
                            <button type="submit" id="login_btn" class="cs-apply-btn">{{ __('Sign In') }}</button>
                            <span class="cs-checkout-signup-note ms-3">
                                {{ __("Don't have an account?") }}
                                <a href="{{ theme_register_url() }}" class="cs-checkout-link">{{ __('Sign up') }}</a>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            @endif

            @php $readonly = $billing_info ? 'readonly' : ''; @endphp

            <form action="{{ theme_checkout_url() }}" method="POST" class="checkout-form" enctype="multipart/form-data">
                {!! theme_csrf_field() !!}
                <input type="hidden" name="payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}" class="payment_gateway_passing_clicking_name">
                <input type="hidden" name="manual_trasaction_id" value="" class="form-control">
                <input type="hidden" name="shift_another_address" class="shift_another_address">
                <input type="hidden" name="used_coupon" class="used_coupon">
                <input type="hidden" name="cash_on_delivery" class="cash_on_delivery">
                <input type="hidden" name="shipping_method" class="shipping-method">

                <div class="cs-checkout-box">
                    <div class="cs-checkout-box-title"><i class="las la-map-marker"></i> {{ __('Delivery Address') }}</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="cs-checkout-label">{{ __('Full Name') }} <span class="cs-required">*</span></label>
                            <input type="text" name="name" class="cs-checkout-input"
                                   placeholder="{{ __('Type Full Name') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="cs-checkout-label">{{ __('Mobile Number') }} <span class="cs-required">*</span></label>
                            <input type="tel" name="phone" class="cs-checkout-input"
                                   placeholder="{{ __('Type Mobile Number') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->phone : (auth('web')->user()?->mobile ?? old('phone')) }}@else{{ old('phone') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="cs-checkout-label">{{ __('Email Address') }} <span class="cs-required">*</span></label>
                            <input type="email" name="email" class="cs-checkout-input"
                                   placeholder="{{ __('Type Email') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="cs-checkout-label">{{ __('ZIP / PIN Code') }} <span class="cs-required">*</span></label>
                            <input type="text" name="postal_code" class="cs-checkout-input" maxlength="6"
                                   placeholder="{{ __('ZIP / PIN Code') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="cs-checkout-label">{{ __('Country') }} <span class="cs-required">*</span></label>
                            <select name="country" class="cs-checkout-select billing_address_country" id="country">
                                @if($billing_info == null)
                                    <option value="" selected disabled>{{ __('Select a country') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                                    @endforeach
                                @else
                                    <option {{ $readonly }}>{{ $billing_info?->country?->name }}</option>
                                @endif
                            </select>
                        </div>
                        @include(('themes.components.state-city-input'))
                        <div class="col-12">
                            <label class="cs-checkout-label">{{ __('Address') }} <span class="cs-required">*</span></label>
                            <textarea name="address" class="cs-checkout-textarea" rows="3"
                                      placeholder="{{ __('Type Address') }}"
                                      {{ $readonly }}>@auth('web'){{ $billing_info ? $billing_info->address : (auth('web')->user()?->address ?? old('address')) }}@else{{ old('address') }}@endauth</textarea>
                        </div>
                        <div class="col-12">
                            <label class="cs-checkout-label">{{ __('Order Notes') }} <span class="cs-checkout-optional">({{ __('optional') }})</span></label>
                            <textarea name="message" class="cs-checkout-textarea" rows="2"
                                      placeholder="{{ __('e.g. Leave at door, ring bell…') }}">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    @if(!theme_is_logged_in())
                    <div class="mt-4">
                        <a href="javascript:void(0)" class="create-accounts cs-checkout-create-link">
                            {{ __('+ Create an Account') }}
                        </a>
                        <input type="hidden" name="create_accounts_input" class="create_accounts_input">
                        <div class="checkout-form-open cs-checkout-create-form mt-3" style="display:none;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="cs-checkout-label">{{ __('Username') }}</label>
                                    <input type="text" name="create_username" class="cs-checkout-input" placeholder="{{ __('Type a unique username') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="cs-checkout-label">{{ __('Password') }}</label>
                                    <input type="password" name="create_password" class="cs-checkout-input" placeholder="{{ __('Type a strong password') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="cs-checkout-label">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="create_password_confirmation" class="cs-checkout-input" placeholder="{{ __('Confirm password') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($billing_info != null)
                        @include(include_theme_path('shop.checkout.partials.shift_another_address'))
                    @endif
                </div>

                <div class="cs-checkout-box">
                    <div class="cs-checkout-box-title"><i class="las la-credit-card"></i> {{ __('Payment Method') }}</div>

                    @php $COD = get_static_option('cash_on_delivery'); @endphp
                    @if($COD === 'on')
                    <div class="cs-checkout-cod-row mb-3">
                        <label class="cs-checkout-cod-label">
                            <input type="checkbox" id="cash" class="cs-checkout-accent">
                            <i class="las la-money-bill-wave"></i>
                            {{ __('Cash On Delivery') }}
                        </label>
                    </div>
                    @endif

                    <div class="payment-gateway-wrapper cs-gw-wrapper">
                        {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                    </div>

                    <div class="payment_gateway_extra_field_information_wrap mt-3"></div>

                    <div class="form-group d-none manual_transaction_id mt-3">
                        <label class="cs-checkout-label">{{ __('Transaction ID') }}</label>
                        <input type="text" name="trasaction_id" class="cs-checkout-input" placeholder="{{ __('Transaction ID') }}">
                    </div>
                    <div class="summernot_wrap">
                        <div class="manual_description cs-checkout-manual-desc mt-2"></div>
                    </div>

                    <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
                </div>
            </form>
        </div>

        {{-- ===== Right: Order Summary ===== --}}
        <div class="col-lg-5">
            <div class="cs-order-summary">
                <div class="cs-summary-title">{{ __('Your Order') }}</div>

                <div class="cs-coupon-row mb-3">
                    <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
                    <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
                    <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
                    <input type="text" class="cs-coupon-input coupon-code flex-grow-1" placeholder="{{ __('Coupon Code') }}" name="used_coupon_display">
                    <button type="button" class="cs-apply-btn coupon-btn">{{ __('Apply') }}</button>
                </div>

                @foreach(theme_cart_items() as $data)
                <div class="cs-mini-cart-row">
                    <div class="cs-mini-cart-img">
                        {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                    </div>
                    <div class="cs-mini-cart-meta">
                        <div class="cs-mini-cart-name">{{ $data->name }} ×{{ $data->qty }}</div>
                    </div>
                    <span class="cs-mini-cart-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                </div>
                @endforeach

                <div class="shipping_method_wrapper">
                    @php
                        $has_delivery_address = false;
                        $authUser = auth('web')->user();
                        if ($authUser?->delivery_address) {
                            $has_delivery_address = true;
                            $country = (string)($authUser->delivery_address->country_id);
                            $state   = (string)($authUser->delivery_address->state_id);
                            $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)
                                ->whereJsonContains('state', $state)->pluck('zone_id')->toArray();
                            if (empty($shipping_zones)) {
                                $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)
                                    ->pluck('zone_id')->toArray();
                            }
                            $shipping_methods = \Modules\ShippingModule\Entities\ShippingMethod::with('options')
                                ->whereIn('zone_id', $shipping_zones)->get();
                        }
                        $subtotal    = (double) Cart::subtotal(0, '', '');
                        $product_tax = theme_product_tax();
                        $taxed_price = ($subtotal * $product_tax) / 100;
                        $shipping    = 0;
                        if (theme_is_logged_in() && $has_delivery_address) {
                            foreach (($shipping_methods ?? []) as $key => $m) {
                                if ($key == 0) { $default_shipping = $m; }
                            }
                            $shipping = $default_shipping['options']['cost'] ?? 0;
                        }
                        $total = $subtotal + $taxed_price + $shipping;
                    @endphp

                    <div class="cs-summary-row mt-2">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="cs-summary-price">{{ site_currency_symbol() }}{{ Cart::subtotal() }}</span>
                    </div>
                    <div class="cs-summary-row coupon-price">
                        <span>{{ __('Coupon Discount (-)') }}</span>
                        <span class="cs-summary-price">{{ amount_with_currency_symbol(0.00) }}</span>
                    </div>
                    <div class="cs-summary-row">
                        <span>{{ __('Tax (Incl)') }}</span>
                        <span class="cs-summary-price">{{ $product_tax }}%</span>
                    </div>

                    @if(theme_is_logged_in() && !empty($shipping_methods ?? []))
                    <div class="cs-checkout-ship-heading">{{ __('Shipping') }}</div>
                    @foreach($shipping_methods ?? [] as $key => $method)
                    <div class="cs-summary-row">
                        <label class="cs-checkout-ship-label">
                            <input type="radio" class="shipping_methods cs-checkout-accent"
                                   id="shipping-option-{{ $method['id'] }}"
                                   name="shipping_method_display"
                                   value="{{ $method['id'] }}"
                                   {{ $key === 0 ? 'checked' : '' }}>
                            {{ $method['name'] }}
                        </label>
                        <span class="cs-summary-price">{{ amount_with_currency_symbol($method['options']['cost']) }}</span>
                    </div>
                    @endforeach
                    @endif

                    <div class="cs-summary-row price-shipping">
                        <span>{{ __('Shipping Cost (+)') }}</span>
                        <span class="cs-summary-price">{{ isset($default_shipping) ? amount_with_currency_symbol($default_shipping['options']['cost']) : '--' }}</span>
                    </div>

                    <div class="cs-summary-row cs-summary-total price-total" data-total="{{ $subtotal + $taxed_price }}">
                        <span>{{ __('Total Amount') }}</span>
                        <span class="cs-summary-price">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                <div class="cs-checkout-terms mt-3">
                    <label class="cs-checkout-terms-label">
                        <input type="checkbox" id="agree" checked class="cs-checkout-accent">
                        {{ __('I agree to the') }}
                        <a href="javascript:void(0)" class="cs-checkout-link">{{ __('Terms & Conditions') }}</a>
                    </label>
                </div>

                <button type="button" class="cs-checkout-btn proceed_checkout_btn checkout_disable mt-3">
                    <i class="las la-lock"></i> {{ __('Place Order') }}
                </button>

                <a href="{{ theme_cart_url() }}" class="cs-cart-continue-btn w-100 justify-content-center mt-2">
                    {{ __('Return to Cart') }}
                </a>

                <p class="cs-summary-secure mt-3">
                    <i class="las la-shield-alt"></i> {{ __('Secure & encrypted checkout') }}
                </p>
            </div>
        </div>

    </div>
</div>

@else
    @include(include_theme_path('digital-shop.cart.cart_empty'))
@endif
@endsection

@section('scripts')
@include(('themes.components.state-city-input-js'))
<script>
(function ($) {
    $(document).ready(function () {

        $(document).on('click', '#login_btn', function (e) {
            e.preventDefault();
            var el = $(this);
            var form = $('#login_form_order_page');
            el.text('{{ __("Please Wait") }}');
            $.ajax({
                type: 'post',
                url: '{{ theme_ajax_login_url() }}',
                data: {
                    _token: '{{ theme_csrf() }}',
                    username: form.find('[name=username]').val(),
                    password: form.find('[name=password]').val(),
                    remember: form.find('[name=remember]').val(),
                },
                success: function (data) {
                    if (data.status === 'invalid') {
                        el.text('{{ __("Sign In") }}');
                        form.find('.error-wrap').html('<div class="alert alert-danger">' + data.msg + '</div>');
                    } else {
                        el.text('{{ __("Redirecting…") }}');
                        location.reload();
                    }
                },
                error: function (data) {
                    var errors = data.responseJSON?.errors ?? {};
                    var html = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; });
                    html += '</ul></div>';
                    form.find('.error-wrap').html(html);
                    el.text('{{ __("Sign In") }}');
                }
            });
        });

        $(document).on('click', '.create-accounts', function () {
            var input = $('.create_accounts_input');
            var form  = $('.cs-checkout-create-form');
            if (input.val() === '') {
                input.val('on');
                form.slideDown(300);
            } else {
                input.val('');
                form.slideUp(300);
            }
        });

        $(document).on('click', '.shift-another-address', function () {
            if ($(this).hasClass('active')) {
                $.each($('.shift-address-form input'), function (k, v) { $(v).val(''); });
                $('.shift_another_address').val('on');
            } else {
                $('.shift_another_address').val('');
            }
        });

        $(document).on('change', '.billing_address_country, .billing_address_state', function () {
            var country = $('.billing_address_country :selected').val();
            var state   = $('.billing_address_state :selected').val();
            $('.coupon-country').val(country);
            $('.coupon-state').val(state);
            getCountryStateBasedTotal(country, state);
        });

        $(document).on('change', '.shift-another-country, .shift-another-state', function () {
            var country = $('.shift-another-country :selected').val();
            var state   = $('.shift-another-state :selected').val();
            $('.coupon-country').val(country);
            $('.coupon-state').val(state);
            getCountryStateBasedTotal(country, state);
        });

        $(document).on('click', 'input.shipping_methods', function () {
            var method = $(this).val();
            var total  = $('.price-total').attr('data-total');
            $('.shipping-method').val(method);
            if (total !== undefined) {
                getShippingMethodBasedTotal(method, $('.coupon-country').val(), $('.coupon-state').val(), total);
            }
        });

        function getShippingMethodBasedTotal(method, country, state, total) {
            var btn = $('.checkout_disable');
            btn.addClass('proceed_checkout_btn').css('opacity', '1');
            $.ajax({
                url: '{{ theme_checkout_shipping_ajax_url() }}',
                type: 'GET',
                data: { shipping_method: method, country: country, state: state, total: total },
                beforeSend: function () { $('.loader').show(); },
                success: function (data) {
                    if (data.type === 'success') {
                        var c = '{{ site_currency_symbol() }}';
                        $('.price-shipping .cs-summary-price').html(c + data.selected_shipping_method.options.cost);
                        $('.price-total .cs-summary-price').html(c + data.total);
                        $('.coupon-shipping-method').val(method);
                    } else {
                        toastr.error(data.msg);
                        btn.css('opacity', '.5').removeClass('proceed_checkout_btn');
                    }
                    $('.loader').hide();
                }
            });
        }

        function getCountryStateBasedTotal(country, state) {
            $.ajax({
                url: '{{ theme_checkout_total_ajax_url() }}',
                type: 'GET',
                data: { country: country, state: state },
                beforeSend: function () { $('.loader').show(); },
                success: function (data) {
                    $('.shipping_method_wrapper').html(data.sync_price_total_markup);
                    $('.coupon-country').val(country);
                    $('.coupon-state').val(state);
                    $('.loader').hide();
                }
            });
        }

        $(document).on('click', '.coupon-btn', function (e) {
            e.preventDefault();
            var coupon = $('.coupon-code').val();
            $.ajax({
                url: '{{ theme_checkout_coupon_ajax_url() }}',
                type: 'GET',
                data: {
                    coupon: coupon,
                    country: $('.coupon-country').val(),
                    state: $('.coupon-state').val(),
                    shipping_method: $('.coupon-shipping-method').val()
                },
                beforeSend: function () { $('.used_coupon').val(''); $('.loader').show(); },
                success: function (data) {
                    $('.loader').hide();
                    if (data.type === 'error') {
                        toastr.error(data.msg);
                    } else if (data.type === 'success') {
                        var c = '{{ site_currency_symbol() }}';
                        $('.price-total').attr('data-total', data.coupon_amount);
                        $('.price-total .cs-summary-price').text(c + data.coupon_amount);
                        $('.coupon-price .cs-summary-price').text(c + data.coupon_price);
                        $('.used_coupon').val(coupon);
                        toastr.success(data.msg);
                    }
                },
                error: function (xhr) {
                    $.each(xhr.responseJSON?.errors ?? {}, function (k, v) { toastr.error(v); });
                    $('.loader').hide();
                }
            });
        });

        var defaultGateway = $('#site_global_payment_gateway').val();
        if (defaultGateway && defaultGateway !== 'cash_on_delivery') {
            $('.payment-gateway-wrapper ul li[data-gateway="' + defaultGateway + '"]').addClass('selected');
            $('.payment_gateway_passing_clicking_name').val(defaultGateway);
        } else {
            $('#cash').prop('checked', true);
            $('.payment_gateway_passing_clicking_name').val('cash_on_delivery');
        }

        var customFormParent = $('.payment_gateway_extra_field_information_wrap');
        customFormParent.children().hide();

        $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) {
            e.preventDefault();
            $('#cash').prop('checked', false);
            $('.cash_on_delivery').val('');

            var gateway = $(this).data('gateway');
            customFormParent.children().hide();

            if (gateway === 'manual_payment') {
                $('.manual_transaction_id').fadeIn().removeClass('d-none');
                $('.summernot_wrap').fadeIn();
                $('.manual_description').text($(this).data('description'));
            } else {
                $('.manual_transaction_id').addClass('d-none').fadeOut();
                $('.summernot_wrap').fadeOut();
                var wrapper = customFormParent.find('#' + gateway + '-parent-wrapper');
                if (wrapper.length) wrapper.fadeIn();
            }

            $(this).addClass('selected').siblings().removeClass('selected');
            $('.payment_gateway_passing_clicking_name').val(gateway);
        });

        $(document).on('keyup', '.manual_transaction_id input[name=trasaction_id]', function () {
            $('input[name=manual_trasaction_id]').val($(this).val());
        });

        $(document).on('change', '#cash', function () {
            if ($(this).is(':checked')) {
                $('.payment-gateway-wrapper ul li').removeClass('selected');
                $('.payment_gateway_passing_clicking_name').val('cash_on_delivery');
            } else {
                $('.payment_gateway_passing_clicking_name').val('');
            }
        });

        $(document).on('click', '.proceed_checkout_btn', function (e) {
            e.preventDefault();
            if ($('#agree:checked').length === 0) {
                toastr.error('{{ __('You need to agree to our Terms & Conditions to complete the order') }}');
                return;
            }
            var shipping_selected = $('input[type=radio][name=shipping_method_display]');
            if (shipping_selected.length > 0 && !shipping_selected.is(':checked')) {
                toastr.error('{{ __('You have to select a shipping method to complete the order') }}');
                return;
            }
            $('form.checkout-form').trigger('submit');
        });

        $(document).on('keyup', 'input[name=postal_code]', function () {
            if (isNaN($(this).val())) $(this).val('');
        });

    });
})(jQuery);
</script>
@endsection
