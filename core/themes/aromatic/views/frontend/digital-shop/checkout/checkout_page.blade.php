@extends('tenant.frontend.frontend-page-master')

@section('title')
    {{__('Checkout')}}
@endsection

@section('page-title')
    {{__('Checkout')}}
@endsection


@section('content')
    @if(Cart::count() > 0)
        <div class="checkout-area padding-top-75 padding-bottom-50">
            <div class="container container-one">
                {!! theme_error_msg() !!}
                <div class="row">
                    @if(!empty(get_static_option('guest_order_system_status')))
                        @if(!empty(Auth::guard('web')->user()))
                            @include(include_theme_path('shop.checkout.partials.checkout_left_side'))
                        @else
                            <div class="col-xl-8 col-lg-7 mt-4">
                                <div class="sign-in register">
                                    <h4 class="title">{{__('Sign In to Continue')}}</h4>
                                    <div class="form-wrapper">
                                        {!! theme_error_msg() !!}
                                        {!! theme_flash_msg() !!}
                                        <form action="" method="post" enctype="multipart/form-data" class="account-form" id="login_form_order_page">
                                            <div class="error-wrap"></div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">{{__('Username')}}<span class="required">*</span></label>
                                                <input type="text" name="username" class="form-control" id="exampleInputEmail1" placeholder="Type your username">
                                            </div>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">{{__('Password')}}<span class="required">*</span></label>
                                                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                                            </div>

                                            <div class="form-group form-check">
                                                <div class="box-wrap">
                                                    <div class="left">
                                                        <input type="checkbox" name="remember" class="form-check-input" id="exampleCheck1">
                                                        <label class="form-check-label" for="exampleCheck1">{{__('Remember me')}}</label>
                                                    </div>
                                                    <div class="right">
                                                        <a href="{{theme_forget_password_url()}}">{{__('Forgot Password?')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="btn-wrapper">
                                                <button type="submit" id="login_btn" class="btn-default rounded-btn">{{__('Sign In')}}</button>
                                            </div>

                                        </form>
                                        <p class="info">{{__("Don'/t have an account")}} <a href="{{theme_register_url()}}" class="active">{{__('Sign up')}}</a></p>
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

            $(document).on('change', 'select[name=shift_country]', function (e){
                let el = $(this);
                let country = el.val();

                $.ajax({
                    url: '{{theme_checkout_state_url()}}',
                    type: 'GET',
                    data: {
                        country: country
                    },

                    beforeSend: () => {
                        el.parent().parent().find('.shift-another-state').html('');
                       $('.loader').show();
                    },
                    success: (data) => {
                        el.parent().parent().find('.shift-another-state').html(data.markup);
                        $('.loader').hide();
                    },
                    error: () => {}
                });
            });

            $(document).on('change', '.billing_address_country[name=country]', function (e){
                let el = $(this);
                let country = el.val();

                $.ajax({
                    url: '{{theme_checkout_state_url()}}',
                    type: 'GET',
                    data: {
                        country: country
                    },

                    beforeSend: () => {
                        el.parent().parent().find('.billing_address_state').html('');
                        $('.loader').show();
                    },
                    success: (data) => {
                        el.parent().parent().find('.billing_address_state').html(data.markup);
                        $('.loader').hide();
                    },
                    error: () => {}
                });
            });

            $(document).on('change', '.shift-another-country, .shift-another-state', function (e){
                let country = $('.shift-another-country :selected').val();
                let state = $('.shift-another-state :selected').val();

                $('.coupon-country').val(country);
                $('.coupon-state').val(state);

                getCountryStateBasedTotal(country, state);
            });

            $(document).on('change', '.billing_address_country, .billing_address_state', function (e){
                let country = $('.billing_address_country :selected').val();
                let state = $('.billing_address_state :selected').val();

                $('.coupon-country').val(country);
                $('.coupon-state').val(state);

                getCountryStateBasedTotal(country, state);
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
                checkout_btn.css({'background': 'var(--main-color-two)', 'border': '2px solid var(--main-color-two)', 'color': '#fff', 'cursor': 'pointer'});

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
                            $('.price-shipping span').last().html(currency + data.selected_shipping_method.options.cost);
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

            function getCountryStateBasedTotal(country, state) {
                $.ajax({
                    url: '{{theme_checkout_total_ajax_url()}}',
                    type: 'GET',
                    data: {
                        country: country,
                        state: state
                    },

                    beforeSend: () => {
                        $('.loader').show();
                    },
                    success: (data) => {
                        $('.shipping_method_wrapper').html(data.sync_price_total_markup);
                        $('.loader').hide();

                        $('.coupon-country').val(country);
                        $('.coupon-state').val(state);
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

                        if (data.type == 'success')
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

            //========== new code for both gateway option show
            var defaulGateway = $('#site_global_payment_gateway').val();

            // select default gateway
            if (defaulGateway && defaulGateway !== 'cash_on_delivery') {
                $('.payment-gateway-wrapper ul li[data-gateway="' + defaulGateway + '"]').addClass('selected');
                $('.payment_gateway_passing_clicking_name').val(defaulGateway);
            } else {
                $('#cash').prop('checked', true);
                $('.payment_gateway_passing_clicking_name').val('cash_on_delivery');
            }

            let customFormParent = $('.payment_gateway_extra_field_information_wrap');
            customFormParent.children().hide();

            // gateway list click
            $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) {
                e.preventDefault();

                // uncheck cash on delivery
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

                // add selected class
                $(this).addClass('selected').siblings().removeClass('selected');

                // hidden input set
                $('.payment_gateway_passing_clicking_name').val(gateway);
            });

            // manual transaction input
            $(document).on('keyup', '.manual_transaction_id input[name=trasaction_id]', function () {
                $('input[name=manual_trasaction_id]').val($(this).val());
            });

            // cash on delivery checkbox
            $(document).on('change', '#cash', function () {
                if ($(this).is(':checked')) {
                    // unselect all gateways
                    $('.payment-gateway-wrapper ul li').removeClass('selected');
                    $('.payment_gateway_passing_clicking_name').val('cash_on_delivery');
                } else {
                    $('.payment_gateway_passing_clicking_name').val('');
                }
            });

            // =======end new code for both gateway option show

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
                if (agreed.length !== 0)
                {
                    $('form.checkout-form').trigger('submit');
                } else {
                    toastr.error('{{__('You need to agree to our Terms & Conditions to complete the order')}}');
                }
            });

            $(document).on('keyup', 'input[name=postal_code]', function () {
                let el = $(this);
                if(isNaN(el.val()))
                    el.val('');
            });
        });
    </script>
@endsection
