@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Checkout') }} @endsection
@section('page-title') {{ __('Checkout') }} @endsection

@section('content')
@if(Cart::count() > 0)
@php
    $carts = Cart::instance("default")->content();
    $itemsTotal = null;
    $enableTaxAmount = !\Modules\TaxModule\Services\CalculateTaxServices::isPriceEnteredWithTax();

    $tax = Modules\TaxModule\Services\CalculateTaxBasedOnCustomerAddress::init();
    $uniqueProductIds = $carts->pluck("id")->unique()->toArray();

    $country_id = old("country_id") ?? 0;
    $state_id = old("state_id") ?? 0;
    $city_id = old("city") ?? 0;

    if (auth('web')->check()) {
        $auth_user = auth('web')->user();
        if (get_static_option('calculate_tax_based_on') == 'customer_billing_address') {
            if ($auth_user->delivery_address) {
                $country_id = $auth_user?->delivery_address?->country_id;
                $state_id = $auth_user?->delivery_address?->state_id;
                $city_id = $auth_user?->delivery_address?->city;
            }
        } else {
            $country_id = $auth_user->country;
            $state_id = $auth_user->state;
            $city_id = $auth_user->city;
        }
    }

    $shippingTaxClass = \Modules\TaxModule\Entities\TaxClassOption::where("class_id", get_static_option("shipping_tax_class"));
    if (!empty($country_id)) { $shippingTaxClass->where("country_id", $country_id); }
    if (!empty($state_id))   { $shippingTaxClass->where("state_id", $state_id); }
    if (!empty($city_id))    { $shippingTaxClass->where("city_id", $city_id); }
    $shippingTaxClass = $shippingTaxClass->sum("rate");

    if (empty($uniqueProductIds)) {
        $taxProducts = collect([]);
    } else {
        if (\Modules\TaxModule\Services\CalculateTaxBasedOnCustomerAddress::is_eligible()) {
            $taxProducts = $tax->productIds($uniqueProductIds)->customerAddress($country_id, $state_id, $city_id)->generate();
        } else {
            $taxProducts = collect([]);
        }
    }
@endphp

<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('Checkout') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ theme_cart_url() }}">{{ __('Cart') }}</a>
            <span>/</span>
            <span class="current">{{ __('Checkout') }}</span>
        </div>
    </div>
</div>

<div class="bp-checkout-top">
    <div class="container">
        {!! theme_error_msg() !!}
    </div>
</div>

<div class="bp-checkout-body">
    <div class="container" style="padding-top:32px;padding-bottom:72px;">
        <div class="row g-4">

            {{-- LEFT: Billing Form --}}
            <div class="col-lg-8">
                @if(!empty(get_static_option('guest_order_system_status')))
                    @if(empty(Auth::guard('web')->user()))
                    <div class="bp-section-box mb-3">
                        <div class="bp-section-box-title"><i class="las la-sign-in-alt"></i> {{ __('Sign In to Continue') }}</div>
                        {!! theme_error_msg() !!}
                        {!! theme_flash_msg() !!}
                        <form action="" method="post" class="account-form" id="login_form_order_page">
                            <div class="error-wrap mb-3"></div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="bp-label">{{ __('Username') }} <span class="bp-checkout-required">*</span></label>
                                    <input type="text" name="username" class="bp-input" placeholder="{{ __('Type your username') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="bp-label">{{ __('Password') }} <span class="bp-checkout-required">*</span></label>
                                    <input type="password" name="password" class="bp-input" placeholder="{{ __('Password') }}">
                                </div>
                                <div class="col-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <label class="bp-checkout-remember d-flex align-items-center gap-2">
                                        <input type="checkbox" name="remember" class="bp-checkout-accent"> {{ __('Remember me') }}
                                    </label>
                                    <a href="{{ theme_forget_password_url() }}" class="bp-checkout-forgot">{{ __('Forgot Password?') }}</a>
                                </div>
                                <div class="col-12">
                                    <button type="submit" id="login_btn" class="bp-btn bp-btn-green">{{ __('Sign In') }}</button>
                                </div>
                            </div>
                        </form>
                        <p class="bp-checkout-guest-note">{{ __("Don't have an account?") }} <a href="{{ theme_register_url() }}" class="bp-checkout-guest-link">{{ __('Sign up') }}</a></p>
                    </div>
                    @endif
                @endif

                <div class="bp-section-box">
                    <div class="bp-section-box-title"><i class="las la-map-marker"></i> {{ __('Billing Details') }}</div>

                    @php $readonly = $billing_info ? 'readonly' : ''; @endphp

                    <form action="{{ theme_checkout_url() }}" method="POST" class="checkout-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}" class="payment_gateway_passing_clicking_name">
                        <input type="hidden" name="manual_trasaction_id" value="" class="form-control">
                        <input type="hidden" class="shift_another_address" name="shift_another_address">
                        <input type="hidden" class="used_coupon" name="used_coupon">
                        <input type="hidden" class="cash_on_delivery" name="cash_on_delivery">
                        <input type="hidden" class="shipping-method" name="shipping_method">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="bp-label">{{ __('Full Name') }}</label>
                                <input class="bp-input" type="text" name="name" placeholder="{{ __('Type Full Name') }}"
                                    value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth" {{ $readonly }}>
                            </div>
                            <div class="col-md-6">
                                <label class="bp-label">{{ __('Mobile Number') }}</label>
                                <input class="bp-input" type="tel" name="phone" placeholder="{{ __('Type Mobile Number') }}"
                                    value="@auth('web'){{ $billing_info ? $billing_info->phone : (!empty(auth('web')->user()?->mobile) ? auth('web')->user()?->mobile : old('phone')) }}@else{{ old('phone') }}@endauth" {{ $readonly }}>
                            </div>
                            <div class="col-md-6">
                                <label class="bp-label">{{ __('Email Address') }}</label>
                                <input class="bp-input" type="email" name="email" placeholder="{{ __('Type Email') }}"
                                    value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth" {{ $readonly }}>
                            </div>
                            <div class="col-md-6">
                                <label class="bp-label">{{ __('ZIP / PIN Code') }} <x-fields.mandatory-indicator/></label>
                                <input class="bp-input" type="text" name="postal_code" placeholder="{{ __('ZIP / PIN Code') }}" maxlength="6"
                                    value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth" {{ $readonly }}>
                            </div>
                            <div class="col-md-6">
                                <label class="bp-label">{{ __('Country') }}</label>
                                <select class="bp-input bp-select billing_address_country" name="country" id="country">
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
                                <label class="bp-label">{{ __('Address') }}</label>
                                <textarea class="bp-input bp-textarea" name="address" placeholder="{{ __('Type Address') }}" {{ $readonly }}>@auth('web'){{ $billing_info ? $billing_info->address : (!empty(auth('web')->user()?->address) ? auth('web')->user()?->address : old('address')) }}@else{{ old('address') }}@endauth</textarea>
                            </div>

                            @if(!Auth::guard('web')->check())
                            <div class="col-12">
                                <div class="create-account-wrapper">
                                    <a href="javascript:void(0)" class="bp-checkout-create-link create-accounts">
                                        <i class="las la-user-plus"></i> {{ __('Create An Account') }}
                                    </a>
                                    <input type="hidden" class="create_accounts_input" name="create_accounts_input">
                                    <div class="checkout-form-open bp-checkout-create-form mt-3">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="bp-label">{{ __('Username') }}</label>
                                                <input class="bp-input" type="text" name="create_username" placeholder="{{ __('Type a unique username') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="bp-label">{{ __('Password') }}</label>
                                                <input class="bp-input" type="password" name="create_password" placeholder="{{ __('Type a strong password') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="bp-label">{{ __('Confirm Password') }}</label>
                                                <input class="bp-input" type="password" name="create_password_confirmation" placeholder="{{ __('Confirm your password') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($billing_info != null)
                                @include(include_theme_path('digital-shop.checkout.partials.shift_another_address'))
                            @endif

                            <div class="col-12">
                                <label class="bp-label">{{ __('Order Notes') }} <span class="bp-checkout-notes-label">({{ __('optional') }})</span></label>
                                <textarea class="bp-input bp-textarea" name="message" placeholder="{{ __('Type Messages') }}">{{ old('message') }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- RIGHT: Order Summary --}}
            <div class="col-lg-4">
                <div class="bp-order-summary">
                    <div class="bp-summary-title">{{ __('Order Summary') }}</div>

                    <div style="margin-bottom:16px;">
                        @php $subtotal = null; $itemsTotal = null; $v_tax_total = 0; @endphp
                        @foreach($carts ?? [] as $data)
                        @php
                            $taxAmount = $taxProducts->where("id", $data->id)->first();
                            if (!empty($taxAmount)) {
                                $taxAmount->tax_options_sum_rate = $taxAmount->tax_options_sum_rate ?? 0;
                                $price = calculatePrice($data->price, $taxAmount);
                                $v_tax_total += calculatePrice($data->price, $taxAmount, "percentage") * $data->qty;
                            } else {
                                $price = calculatePrice($data->price, $data->options);
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

        </div>
    </div>
</div>

@else
    @include(include_theme_path('digital-shop.cart.cart_empty'))
@endif
@endsection

@section('scripts')
    {!! theme_ajax_login_js() !!}
    @include(('themes.components.state-city-input-js'))
    <script>
    $(function () {
        $(document).on('click', '.create-accounts', function () {
            let need_account = $('.create_accounts_input');
            if (need_account.val() === '') { need_account.val('on'); } else { need_account.val(''); }
            $('.create-account-wrapper .checkout-form-open').toggleClass('active');
        });

        $(document).on('click', '#login_btn', function (e) {
            e.preventDefault();
            var formContainer = $('#login_form_order_page');
            var el = $(this);
            el.text('{{ __("Please Wait") }}');
            $.ajax({
                type: 'post', url: '{{ theme_ajax_login_url() }}',
                data: { _token: '{{ csrf_token() }}', username: formContainer.find('input[name="username"]').val(), password: formContainer.find('input[name="password"]').val(), remember: formContainer.find('input[name="remember"]').val() },
                success: function (data) {
                    if (data.status === 'invalid') { el.text('{{ __("Login") }}'); formContainer.find('.error-wrap').html('<div class="alert alert-danger">' + data.msg + '</div>'); }
                    else { formContainer.find('.error-wrap').html(''); el.text('{{ __("Login Success.. Redirecting ..") }}'); location.reload(); }
                },
                error: function (data) {
                    var response = data.responseJSON.errors;
                    formContainer.find('.error-wrap').html('<ul class="alert alert-danger"></ul>');
                    $.each(response, function (value, index) { formContainer.find('.error-wrap ul').append('<li>' + index + '</li>'); });
                    el.text('{{ __("Login") }}');
                }
            });
        });

        $(document).on('change', '.billing_address_country', function () {
            let country = $('.billing_address_country :selected').val();
            $('.coupon-country').val(country);
        });

        $(document).on('click', '.coupon-btn', function (e) {
            e.preventDefault();
            let coupon = $('.coupon-code').val();
            let country = $('.coupon-country').val();
            let shipping = $('.coupon-shipping-method').val();
            let user_coupon = $('.used_coupon');
            $.ajax({
                url: '{{ theme_checkout_coupon_ajax_url() }}', type: 'GET',
                data: { coupon: coupon, country: country, shipping_method: shipping },
                beforeSend: function () { user_coupon.val(''); $('.loader').show(); },
                success: function (data) {
                    if (data.type === 'error') { toastr.error(data.msg); }
                    $('.loader').hide();
                    if (data.type === 'success') {
                        let currency_symbol = '{{ site_currency_symbol() }}';
                        $('.price-total').attr('data-total', data.coupon_amount);
                        $('.price-total .bp-summary-price').text(currency_symbol + data.coupon_amount);
                        $('.coupon-price .bp-summary-price').text(currency_symbol + data.coupon_price);
                        user_coupon.val(coupon);
                        toastr.success(data.msg);
                    }
                },
                error: function (error) {
                    let responseData = error.responseJSON.errors;
                    $.each(responseData, function (index, value) { toastr.error(value); });
                    $('.loader').hide();
                }
            });
        });

        var defaulGateway = $('#site_global_payment_gateway').val();
        if (defaulGateway && defaulGateway !== 'cash_on_delivery') {
            $('.payment-gateway-wrapper ul li[data-gateway="' + defaulGateway + '"]').addClass('selected');
            $('.payment_gateway_passing_clicking_name').val(defaulGateway);
        } else {
            $('#cash').prop('checked', true);
            $('.payment_gateway_passing_clicking_name').val('cash_on_delivery');
        }

        let customFormParent = $('.payment_gateway_extra_field_information_wrap');
        customFormParent.children().hide();

        $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) {
            e.preventDefault();
            $('#cash').prop('checked', false);
            $('.cash_on_delivery').val('');
            let gateway = $(this).data('gateway');
            customFormParent.children().hide();
            if (gateway === 'manual_payment') {
                $('.manual_transaction_id').fadeIn().removeClass('d-none');
                $('.summernot_wrap').fadeIn();
                $('.manual_description').text($(this).data('description'));
            } else {
                $('.manual_transaction_id').addClass('d-none').fadeOut();
                $('.summernot_wrap').fadeOut();
                let wrapper = customFormParent.find('#' + gateway + '-parent-wrapper');
                if (wrapper.length > 0) { wrapper.fadeIn(); }
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
                toastr.error('{{ __("You need to agree to our Terms & Conditions to complete the order") }}');
                return;
            }
            $('form.checkout-form').trigger('submit');
        });

        $(document).on('keyup', 'input[name=postal_code]', function () {
            let el = $(this);
            if (isNaN(el.val())) el.val('');
        });
    });
    </script>
@endsection
