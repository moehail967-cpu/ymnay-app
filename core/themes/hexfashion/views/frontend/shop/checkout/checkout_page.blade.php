@extends(route_prefix().'frontend.frontend-page-master')

@section('title') {{__('Checkout')}} @endsection
@section('page-title') {{__('Checkout')}} @endsection

@section('content')
    {{-- Page Banner --}}
    <div class="hf-page-banner">
        <div class="container">
            <h1 class="hf-page-banner-title">{{ __('Checkout') }}</h1>
            <nav class="hf-breadcrumb">
                <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
                <span class="hf-breadcrumb-sep"><i class="las la-angle-right"></i></span>
                <a href="{{ theme_cart_url() }}">{{ __('Cart') }}</a>
                <span class="hf-breadcrumb-sep"><i class="las la-angle-right"></i></span>
                <span>{{ __('Checkout') }}</span>
            </nav>
        </div>
    </div>

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

            if (auth('web')->check())
            {
                $auth_user = auth('web')->user();

                if (get_static_option('calculate_tax_based_on') == 'customer_billing_address')
                {
                    if ($auth_user->delivery_address)
                    {
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
            if(!empty($country_id)){
                $shippingTaxClass->where("country_id", $country_id);
            }
            if(!empty($state_id)){
                $shippingTaxClass->where("state_id", $state_id);
            }
            if(!empty($city_id)){
                $shippingTaxClass->where("city_id", $city_id);
            }

            $shippingTaxClass = $shippingTaxClass->sum("rate");

            if(empty($uniqueProductIds))
            {
                $taxProducts = collect([]);
            }
            else
            {
                if(\Modules\TaxModule\Services\CalculateTaxBasedOnCustomerAddress::is_eligible()){
                    $taxProducts = $tax
                        ->productIds($uniqueProductIds)
                        ->customerAddress($country_id, $state_id, $city_id)
                        ->generate();
                }
                else
                {
                    $taxProducts = collect([]);
                }
            }
        @endphp

        <div class="hf-checkout-section">
            <div class="container">
                {!! theme_error_msg() !!}
                <div class="hf-checkout-layout">
                    @if(!empty(get_static_option('guest_order_system_status')))
                        @if(!empty(Auth::guard('web')->user()))
                            @include(include_theme_path('shop.checkout.partials.checkout_left_side'))
                        @else
                            <div class="hf-checkout-left">
                                <div class="hf-checkout-login">
                                    <h4>{{__('Sign In to Continue')}}</h4>
                                    <div class="form-wrapper">
                                        {!! theme_error_msg() !!}
                                        {!! theme_flash_msg() !!}
                                        <form action="" method="post" enctype="multipart/form-data" class="account-form" id="login_form_order_page">
                                            <div class="error-wrap"></div>
                                            <div class="hf-form-group mb-3">
                                                <label class="hf-form-label">{{__('Username')}}<span class="hf-form-required">*</span></label>
                                                <input type="text" name="username" class="hf-form-input" placeholder="{{ __('Type your username') }}">
                                            </div>
                                            <div class="hf-form-group mb-3">
                                                <label class="hf-form-label">{{__('Password')}}<span class="hf-form-required">*</span></label>
                                                <input type="password" name="password" class="hf-form-input" placeholder="{{ __('Password') }}">
                                            </div>
                                            <div class="hf-form-group mb-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <label class="d-flex align-items-center gap-2" style="font-size:13px;">
                                                        <input type="checkbox" name="remember" style="accent-color:#E8603C;">
                                                        {{__('Remember me')}}
                                                    </label>
                                                    <a href="{{theme_forget_password_url()}}" class="hf-checkout-link">{{__('Forgot Password?')}}</a>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <button type="submit" id="login_btn" class="hf-btn hf-btn-primary">{{__('Sign In')}}</button>
                                            </div>
                                        </form>
                                        <p class="mt-3" style="font-size:13px;color:#666;">
                                            {{__("Don't have an account?")}}
                                            <a href="{{theme_register_url()}}" class="hf-checkout-link">{{__('Sign up')}}</a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        @include(include_theme_path('shop.checkout.partials.checkout_left_side'))
                    @endif

                    @include(include_theme_path('shop.checkout.partials.checkout_right_side'))
                </div>
            </div>
        </div>
    @else
        @include(include_theme_path('shop.cart.cart_empty'))
    @endif
@endsection

@section('scripts')
    {!! theme_ajax_login_js() !!}
    @include(('themes.components.state-city-input-js'))

    <script>
        $(function (){
            $(document).on('click', '#login_btn', function (e) {
                e.preventDefault();
                var formContainer = $('#login_form_order_page');
                var el = $(this);
                var username = formContainer.find('input[name="username"]').val();
                var password = formContainer.find('input[name="password"]').val();
                var remember = formContainer.find('input[name="remember"]').val();

                el.text('{{__("Please Wait")}}');

                $.ajax({
                    type: 'post',
                    url: "{{theme_ajax_login_url()}}",
                    data: {
                        _token: "{{csrf_token()}}",
                        username: username,
                        password: password,
                        remember: remember,
                    },
                    success: function (data) {
                        if (data.status === 'invalid') {
                            el.text('{{__("Login")}}')
                            formContainer.find('.error-wrap').html('<div class="alert alert-danger">' + data.msg + '</div>');
                        } else {
                            formContainer.find('.error-wrap').html('');
                            el.text('{{__("Login Success.. Redirecting ..")}}');
                            location.reload();
                        }
                    },
                    error: function (data) {
                        var response = data.responseJSON.errors
                        formContainer.find('.error-wrap').html('<ul class="alert alert-danger"></ul>');
                        $.each(response, function (value, index) {
                            formContainer.find('.error-wrap ul').append('<li>' + index + '</li>');
                        });
                        el.text('{{__("Login")}}');
                    }
                });
            });

            $(document).on('click', '.shift-another-address', function (){
                let el = $(this);

                let $items;
                if (el.hasClass('active')) {
                    $items = $('.shift-address-form input');
                    $.each($items, function (key, value){
                        $(value).val('');
                    });

                    $('.shift_another_address').val('on');
                }

                if (el.hasClass('active') === false) {
                    $('.shift_another_address').val('');
                }
            });

            $(document).on('change', '.shift-another-country, .shift-another-state, .shift-another-city', function (e){
                let country = $('.shift-another-country :selected').val();
                let state = $('.shift-another-state :selected').val();
                let city = $('.shift-another-city :selected').val();

                $('.coupon-country').val(country);
                $('.coupon-state').val(state);
                $('.coupon-city').val(city);

                getCountryStateBasedTotal(country, state, city);
            });

            $(document).on('change', '.billing_address_country, .billing_address_state, .billing_address_city', function (e){
                let country = $('.billing_address_country :selected').val();
                let state = $('.billing_address_state :selected').val();
                let city = $('.billing_address_city :selected').val();

                $('.coupon-country').val(country);
                $('.coupon-state').val(state);
                $('.coupon-city').val(city);

                getCountryStateBasedTotal(country, state, city);
            });

            $(document).on('click', 'input[name=shipping_method]', function (){
                let el = $(this);
                let shipping_method = el.val();
                let total = $('.price-total').attr('data-total');

                $('.shipping-method').val(shipping_method);

                if (total !== undefined)
                {
                    getShippingMethodBasedTotal(shipping_method, $('.coupon-country').val(), $('.coupon-state').val(), total);
                }
            });

            function getShippingMethodBasedTotal(shipping_method ,country, state, total) {
                let checkout_btn = $('.checkout_disable');
                checkout_btn.addClass('proceed_checkout_btn');
                checkout_btn.css({'background': 'var(--main-color-one)', 'border': '2px solid var(--main-color-one)', 'color': '#fff', 'cursor': 'pointer'});

                $.ajax({
                    url: '{{theme_checkout_shipping_ajax_url()}}',
                    type: 'GET',
                    data: {
                        shipping_method: shipping_method,
                        country: country,
                        state: state,
                        total: total
                    },beforeSend: () => {
                        $('.loader').show();
                    },
                    success: (data) => {
                        if (data.type === 'success')
                        {
                            let currency = '{{site_currency_symbol()}}';
                            $('.price-shipping span').last().html(currency + data.selected_shipping_method.options.final_cost);
                            $('.price-total span').last().html(currency + data.total);
                            $('.loader').hide();

                            $('.coupon-shipping-method').val(shipping_method);
                        } else {
                            toastr.error(data.msg);
                            checkout_btn.css({'background': '#9d9d9d', 'border': '2px solid #9d9d9d', 'color': '#fff', 'cursor': 'not-allowed'});
                            checkout_btn.removeClass('proceed_checkout_btn');
                            $('.loader').hide();
                        }
                    },
                    error: () => {}
                });
            }

            function getCountryStateBasedTotal(country, state, city) {
                $.ajax({
                    url: '{{theme_checkout_total_ajax_url()}}',
                    type: 'GET',
                    data: {
                        country: country,
                        state: state,
                        city: city
                    },

                    beforeSend: () => {
                        $('.loader').show();
                    },
                    success: (data) => {
                        $('.shipping_method_wrapper').html(data.sync_price_total_markup);
                        $('.loader').hide();

                        $('.coupon-country').val(country);
                        $('.coupon-state').val(state);
                        $('.coupon-city').val(city);
                    },
                    error: () => {}
                });
            }

            $(document).on('click', '.coupon-btn', function (e){
                e.preventDefault();

                let coupon = $('.coupon-code').val();
                let country = $('.coupon-country').val();
                let state = $('.coupon-state').val();
                let shipping = $('.coupon-shipping-method').val();

                let user_coupon = $('.used_coupon');

                $.ajax({
                    url: '{{theme_checkout_coupon_ajax_url()}}',
                    type: 'GET',
                    data: {
                        coupon: coupon,
                        country: country,
                        state: state,
                        shipping_method: shipping
                    },

                    beforeSend: () => {
                        user_coupon.val('');
                        $('.loader').show();
                    },
                    success: (data) => {
                        if (data.type === 'error')
                        {
                            toastr.error(data.msg);
                        }

                        $('.loader').hide();

                        if (data.type === 'success')
                        {
                            let currency_symbol = '{{site_currency_symbol()}}';
                            $('.price-total').attr('data-total', data.coupon_amount);
                            $('.price-total span').text(currency_symbol+data.coupon_amount);
                            $('.coupon-price span:last').text(currency_symbol+data.coupon_price);
                            user_coupon.val(coupon);

                            toastr.success(data.msg);
                        }
                    },
                    error: (error) => {
                        let responseData = error.responseJSON.errors;
                        $.each(responseData, function (index, value){
                            toastr.error(value);
                        });

                        $('.loader').hide();
                    }
                });
            });

            //========== payment gateway selection
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
                let manual_transaction_div = $('.manual_transaction_id');
                let manual_description = $('.manual_description');
                let summernot_wrap_div = $('.summernot_wrap');

                customFormParent.children().hide();

                if (gateway === 'manual_payment') {
                    manual_transaction_div.fadeIn().removeClass('d-none');
                    summernot_wrap_div.fadeIn();
                    manual_description.text($(this).data('description'));
                } else {
                    manual_transaction_div.addClass('d-none').fadeOut();
                    summernot_wrap_div.fadeOut();

                    let wrapper = customFormParent.find('#' + gateway + '-parent-wrapper');
                    if (wrapper.length > 0) {
                        wrapper.fadeIn();
                    }
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

            $(document).on('click', '.create-accounts', function (e){
                let need_account = $('.create_accounts_input');

                if(need_account.val() === '')
                {
                    need_account.val('on');
                } else {
                    need_account.val('');
                }

                $('.create-account-wrapper .checkout-form-open').toggleClass('active')
            });

            $(document).on('click', '.proceed_checkout_btn', function (e){
                e.preventDefault();

                let agreed = $('#agree:checked');
                if (agreed.length === 0)
                {
                    toastr.error('{{__('You need to agree to our Terms & Conditions to complete the order')}}');
                    return ;
                }

                let shipping_selected = $('input[type=radio][name=shipping_method]');
                if (shipping_selected.length > 0)
                {
                    let checked = $(shipping_selected).is(':checked');
                    if (!checked)
                    {
                        toastr.error('{{__('You have to select a shipping method to complete the order')}}');
                        return ;
                    }
                }

                $('form.checkout-form').trigger('submit');
            });

            $(document).on('change', 'select[name=shift_country], select[name=country]', function (e) {
                e.preventDefault();

                let country_id = $(this).val();

                $.post(`{{theme_state_ajax_url()}}`,
                    {
                        _token: `{{csrf_token()}}`,
                        country: country_id
                    },
                    function (data) {
                        let stateField = $('.stateField');
                        stateField.empty();
                        stateField.append(`<option value="">{{__('Select a state')}}</option>`);

                        let cityField = $('.cityField');
                        cityField.empty();
                        cityField.append(`<option value="">{{__('Select a city')}}</option>`);

                        $.each(data.states , function (index, value) {
                            stateField.append(
                                `<option value="${value.id}">${value.name}</option>`
                            );
                        });
                    }
                )
            });

            $(document).on('change', 'select[name=shift_state], select[name=state]', function (e) {
                e.preventDefault();

                let state_id = $(this).val();

                $.post(`{{theme_city_search_url()}}`,
                    {
                        _token: `{{csrf_token()}}`,
                        state: state_id
                    },
                    function (data) {
                        let cityField = $('.cityField');
                        cityField.empty();
                        cityField.append(`<option value="">{{__('Select a city')}}</option>`);

                        $.each(data.cities , function (index, value) {
                            cityField.append(
                                `<option value="${value.id}">${value.name}</option>`
                            );
                        });
                    }
                )
            });

            $(document).on('keyup', 'input[name=postal_code]', function () {
                let el = $(this);
                if(isNaN(el.val()))
                    el.val('');
            });
        });
    </script>
@endsection
