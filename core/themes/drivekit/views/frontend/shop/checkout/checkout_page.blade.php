@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Checkout') }} @endsection
@section('page-title') {{ __('Checkout') }} @endsection

@section('content')
@if(Cart::count() > 0)

{{-- Page Banner --}}
<div class="dk-page-banner">
    <div class="dk-page-banner-overlay"></div>
    <div class="dk-page-banner-bar"></div>
    <div class="container dk-page-banner-inner">
        <h1 class="dk-page-banner-title">{{ __('Checkout') }}</h1>
        <nav class="dk-page-banner-nav">
            <a href="{{ theme_home_url() }}" class="dk-page-banner-link">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right dk-page-banner-sep"></i>
            <span class="dk-page-banner-current">{{ __('Checkout') }}</span>
        </nav>
    </div>
</div>

<div class="container" style="padding-top:28px;">

    {{-- Step Indicator --}}
    <div class="dk-steps">
        <div class="dk-step done">
            <span class="dk-step-num"><i class="mdi mdi-check"></i></span> {{ __('Cart') }}
        </div>
        <div class="dk-step-line done"></div>
        <div class="dk-step active">
            <span class="dk-step-num">2</span> {{ __('Checkout') }}
        </div>
        <div class="dk-step-line"></div>
        <div class="dk-step">
            <span class="dk-step-num">3</span> {{ __('Confirmed') }}
        </div>
    </div>

    <x-error-msg/>

    <div class="row g-4" style="padding-bottom:72px;">

        {{-- Left: Form --}}
        <div class="col-lg-7">

            {{-- Guest Login Panel --}}
            @if(!empty(get_static_option('guest_order_system_status')) && !theme_is_logged_in())
            <div class="dk-checkout-section">
                <div class="dk-checkout-heading">
                    <i class="mdi mdi-account-outline"></i> {{ __('Sign In to Continue') }}
                </div>
                <x-flash-msg/>
                <form id="login_form_order_page">
                    <div class="error-wrap mb-3"></div>
                    <div class="mb-3">
                        <label class="dk-input-label">{{ __('Username') }}</label>
                        <input type="text" name="username" placeholder="{{ __('Type your username') }}" class="dk-input">
                    </div>
                    <div class="mb-3">
                        <label class="dk-input-label">{{ __('Password') }}</label>
                        <input type="password" name="password" placeholder="{{ __('Password') }}" class="dk-input">
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <label class="d-flex align-items-center gap-2" style="font-size:12px;cursor:pointer;color:var(--dk-silver);">
                            <input type="checkbox" name="remember" style="accent-color:var(--dk-red);"> {{ __('Remember me') }}
                        </label>
                        <a href="{{ theme_forget_password_url() }}" class="dk-forgot-link">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="dk-btn dk-btn-primary w-100" style="justify-content:center;font-size:12px;padding:12px;">
                        <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                    </button>
                    <p class="mt-3" style="font-size:13px;color:var(--dk-silver);">
                        {{ __("Don't have an account?") }}
                        <a href="{{ theme_register_url() }}" style="color:var(--dk-red);font-weight:700;">{{ __('Sign up') }}</a>
                    </p>
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

                {{-- Billing Address --}}
                <div class="dk-checkout-section">
                    <div class="dk-checkout-heading">
                        <i class="mdi mdi-map-marker"></i> {{ __('Delivery Address') }}
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="dk-input-label">{{ __('Full Name') }}</label>
                            <input type="text" name="name" class="dk-input" placeholder="{{ __('Type Full Name') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="dk-input-label">{{ __('Mobile Number') }}</label>
                            <input type="tel" name="phone" class="dk-input" placeholder="{{ __('Type Mobile Number') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->phone : (auth('web')->user()?->mobile ?? old('phone')) }}@else{{ old('phone') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="dk-input-label">{{ __('Email Address') }}</label>
                            <input type="email" name="email" class="dk-input" placeholder="{{ __('Type Email') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="dk-input-label">{{ __('ZIP / PIN Code') }}</label>
                            <input type="text" name="postal_code" class="dk-input" maxlength="6" placeholder="{{ __('ZIP Code') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="dk-input-label">{{ __('Country') }}</label>
                            <select name="country" class="dk-checkout-select billing_address_country" id="country">
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
                            <label class="dk-input-label">{{ __('Address') }}</label>
                            <textarea name="address" class="dk-input" rows="3" placeholder="{{ __('Type Address') }}" {{ $readonly }}>@auth('web'){{ $billing_info ? $billing_info->address : (auth('web')->user()?->address ?? old('address')) }}@else{{ old('address') }}@endauth</textarea>
                        </div>
                        <div class="col-12">
                            <label class="dk-input-label">{{ __('Order Notes') }} <span style="font-weight:400;text-transform:none;font-size:11px;">({{ __('optional') }})</span></label>
                            <textarea name="message" class="dk-input" rows="2" placeholder="{{ __('e.g. Call before delivery, special instructions…') }}">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    @if(!theme_is_logged_in())
                    <div class="mt-4">
                        <a href="javascript:void(0)" class="create-accounts" style="color:var(--dk-red);font-size:13px;font-weight:700;text-decoration:none;">
                            + {{ __('Create an Account') }}
                        </a>
                        <input type="hidden" name="create_accounts_input" class="create_accounts_input">
                        <div class="checkout-form-open mt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="dk-input-label">{{ __('Username') }}</label>
                                    <input type="text" name="create_username" class="dk-input" placeholder="{{ __('Type a unique username') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="dk-input-label">{{ __('Password') }}</label>
                                    <input type="password" name="create_password" class="dk-input" placeholder="{{ __('Type a strong password') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="dk-input-label">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="create_password_confirmation" class="dk-input" placeholder="{{ __('Confirm password') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($billing_info != null)
                        @include(include_theme_path('shop.checkout.partials.shift_another_address'))
                    @endif
                </div>

                {{-- Payment Method --}}
                <div class="dk-checkout-section">
                    <div class="dk-checkout-heading">
                        <i class="mdi mdi-credit-card-outline"></i> {{ __('Payment Method') }}
                    </div>

                    <label class="dk-payment-option">
                        <input type="checkbox" id="cash">
                        <i class="mdi mdi-cash"></i>
                        {{ __('Cash On Delivery') }}
                    </label>

                    <div class="payment-gateway-wrapper">
                        {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                    </div>

                    <div class="payment_gateway_extra_field_information_wrap mt-3"></div>

                    <div class="form-group d-none manual_transaction_id mt-3">
                        <label class="dk-input-label">{{ __('Transaction ID') }}</label>
                        <input type="text" name="trasaction_id" class="dk-input" placeholder="{{ __('Transaction ID') }}">
                    </div>
                    <div class="summernot_wrap" style="display:none;">
                        <div class="manual_description mt-2" style="font-size:13px;color:var(--dk-silver);"></div>
                    </div>

                    <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
                </div>
            </form>
        </div>

        {{-- Right: Order Summary --}}
        <div class="col-lg-5">
            <div class="dk-order-summary">
                <div class="dk-order-summary-title"><i class="mdi mdi-receipt"></i> {{ __('Your Order') }}</div>

                {{-- Coupon --}}
                <div class="dk-coupon-bar">
                    <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
                    <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
                    <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
                    <input type="text" class="coupon-code dk-coupon-input" placeholder="{{ __('Coupon Code') }}" name="used_coupon_display">
                    <button type="button" class="dk-btn dk-btn-primary dk-btn-sm coupon-btn">{{ __('Apply') }}</button>
                </div>

                {{-- Cart Items --}}
                @foreach(theme_cart_items() as $data)
                <div class="dk-checkout-item">
                    <div class="dk-checkout-item-thumb">
                        {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                    </div>
                    <div class="dk-checkout-item-info">
                        <div class="dk-checkout-item-name">{{ $data->name }} ×{{ $data->qty }}</div>
                        @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                        <div class="dk-checkout-item-variant">
                            @if($data?->options?->color_name) {{ $data->options->color_name }} @endif
                            @if($data?->options?->size_name) · {{ $data->options->size_name }} @endif
                            @foreach((array)($data?->options?->attributes ?? []) as $attrK => $attrV)
                                @if($attrV !== null && $attrV !== '') · {{ ucfirst($attrK) }}: {{ $attrV }} @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <span class="dk-checkout-item-price">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                </div>
                @endforeach

                {{-- Pricing Rows --}}
                <div class="shipping_method_wrapper">
                    @php
                        $has_delivery_address = false;
                        $authUser = auth('web')->user();
                        if ($authUser?->delivery_address) {
                            $has_delivery_address = true;
                            $country = (string)($authUser->delivery_address->country_id);
                            $state   = (string)($authUser->delivery_address->state_id);
                            $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)->whereJsonContains('state', $state)->pluck('zone_id')->toArray();
                            if (empty($shipping_zones)) {
                                $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)->pluck('zone_id')->toArray();
                            }
                            $shipping_methods = \Modules\ShippingModule\Entities\ShippingMethod::with('options')->whereIn('zone_id', $shipping_zones)->get();
                        }
                    @endphp
                    @php
                        $subtotal    = theme_cart_subtotal();
                        $product_tax = theme_product_tax();
                        $taxed_price = ($subtotal * $product_tax) / 100;
                        $shipping_tax = 0;
                        $shipping     = 0;
                        if (theme_is_logged_in() && $has_delivery_address) {
                            $is_default = false;
                            foreach (($shipping_methods ?? []) as $key => $m) {
                                if ($key == 0) { $is_default = true; $default_shipping = $m; }
                            }
                            if (isset($default_shipping)) {
                                $raw_shipping = (float) ($default_shipping['options']['cost'] ?? 0);
                                $shipping     = (float) apply_filters('nazmart:product_price', $raw_shipping, null);
                                if ($default_shipping?->options?->tax_status) { $shipping_tax = ($shipping * $product_tax) / 100; }
                            }
                        }
                        $total = $subtotal + $taxed_price + $shipping_tax + $shipping;
                    @endphp

                    <div class="dk-order-row">
                        <span class="dk-order-row-label">{{ __('Subtotal') }}</span>
                        <span class="dk-order-row-value">{{ amount_with_currency_symbol($subtotal) }}</span>
                    </div>
                    <div class="dk-order-row coupon-price">
                        <span class="dk-order-row-label">{{ __('Coupon Discount (-)') }}</span>
                        <span class="dk-order-row-value dk-price" style="color:var(--dk-red);">{{ amount_with_currency_symbol(0.00) }}</span>
                    </div>
                    <div class="dk-order-row">
                        <span class="dk-order-row-label">{{ __('Tax (Incl)') }}</span>
                        <span class="dk-order-row-value">{{ $product_tax }}%</span>
                    </div>

                    @if(theme_is_logged_in() && !empty($shipping_methods ?? []))
                    <div class="mt-3 mb-1">
                        <div class="dk-shipping-heading">{{ __('Shipping') }}</div>
                        @foreach($shipping_methods ?? [] as $key => $method)
                        <div class="dk-shipping-row" data-country="{{ $country ?? '' }}" data-state="{{ $state ?? '' }}">
                            <label class="dk-shipping-label">
                                <input type="radio" class="shipping_methods" id="shipping-option-{{ $method['id'] }}" name="shipping_method_display"
                                       value="{{ $method['id'] }}" {{ $key === 0 ? 'checked' : '' }}>
                                {{ $method['name'] }}
                            </label>
                            <span class="dk-shipping-cost">{{ amount_with_currency_symbol((float) apply_filters('nazmart:product_price', (float) ($method['options']['cost'] ?? 0), null)) }}</span>
                        </div>
                        @endforeach
                    @endif

                    <div class="dk-order-row price-shipping">
                        <span class="dk-order-row-label">{{ __('Shipping Cost (+)') }}</span>
                        <span class="dk-order-row-value dk-price">{{ isset($default_shipping) ? amount_with_currency_symbol((float) apply_filters('nazmart:product_price', (float) ($default_shipping['options']['cost'] ?? 0), null)) : '--' }}</span>
                    </div>

                    <div class="dk-order-total price-total" data-total="{{ round($subtotal + $taxed_price, 2) }}">
                        <span class="dk-order-total-label">{{ __('Total Amount') }}</span>
                        <span class="dk-order-total-value dk-price">{{ amount_with_currency_symbol($total) }}</span>
                    </div>

                    @if(theme_is_logged_in() && !empty($shipping_methods ?? []))
                    </div>
                    @endif
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                {{-- Terms & Place Order --}}
                <div class="dk-terms-row">
                    <input type="checkbox" id="agree" checked>
                    <label for="agree" class="dk-terms-label">
                        {{ __('I agree to the') }}
                        <a href="javascript:void(0)">{{ __('Terms & Conditions') }}</a>
                    </label>
                </div>

                <button type="button" class="dk-btn dk-btn-primary proceed_checkout_btn checkout_disable w-100" style="justify-content:center;font-size:13px;padding:14px;">
                    <i class="mdi mdi-lock"></i> {{ __('Place Order') }}
                </button>

                <a href="{{ theme_cart_url() }}" class="dk-btn dk-btn-ghost w-100" style="justify-content:center;font-size:11px;padding:10px;margin-top:8px;">
                    <i class="mdi mdi-arrow-left"></i> {{ __('Return to Cart') }}
                </a>

                <div class="dk-secure-note">
                    <i class="mdi mdi-shield-check"></i> {{ __('Secure & encrypted checkout') }}
                </div>
            </div>
        </div>

    </div>
</div>

@else
    @include(include_theme_path('shop.cart.cart_empty'))
@endif
@endsection

@section('scripts')
@include(('themes.components.state-city-input-js'))

<script>
(function ($) {
    $(document).ready(function () {

        $(document).on('click', '#login_btn', function (e) {
            e.preventDefault();
            var el = $(this), form = $('#login_form_order_page');
            el.text('{{ __("Please Wait") }}');
            $.ajax({
                type: 'post', url: '{{ theme_ajax_login_url() }}',
                data: { _token: '{{ theme_csrf() }}', username: form.find('[name=username]').val(), password: form.find('[name=password]').val(), remember: form.find('[name=remember]').is(':checked') ? 1 : 0 },
                success: function (data) {
                    if (data.status === 'invalid') { el.text('{{ __("Sign In") }}'); form.find('.error-wrap').html('<div style="padding:10px 14px;background:rgba(229,48,48,.1);border:1px solid rgba(229,48,48,.3);border-radius:var(--dk-radius);color:var(--dk-red);font-size:13px;margin-bottom:8px;">' + data.msg + '</div>'); }
                    else { el.text('{{ __("Redirecting...") }}'); location.reload(); }
                },
                error: function (data) {
                    var errors = data.responseJSON?.errors ?? {}; var html = '<div style="padding:10px 14px;background:rgba(229,48,48,.1);border:1px solid rgba(229,48,48,.3);border-radius:var(--dk-radius);color:var(--dk-red);font-size:13px;margin-bottom:8px;"><ul style="margin:0;padding-left:16px;">';
                    $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; }); html += '</ul></div>';
                    form.find('.error-wrap').html(html); el.text('{{ __("Sign In") }}');
                }
            });
        });

        $(document).on('click', '.create-accounts', function () {
            var need_account = $('.create_accounts_input');
            if (need_account.val() === '') { need_account.val('on'); $('.checkout-form-open').addClass('active'); }
            else { need_account.val(''); $('.create_accounts_input').val(''); $('.checkout-form-open').removeClass('active'); }
        });

        $(document).on('click', '.shift-another-address', function () {
            if ($(this).hasClass('active')) { $.each($('.shift-address-form input'), function (k, v) { $(v).val(''); }); $('.shift_another_address').val('on'); }
            else { $('.shift_another_address').val(''); }
        });

        $(document).on('change', '.billing_address_country[name=country]', function () {
            var el = $(this);
            $.ajax({ url: '{{ theme_checkout_state_url() }}', type: 'GET', data: { country: el.val() },
                beforeSend: function () { el.closest('.row').find('.billing_address_state').html(''); $('.loader').show(); },
                success: function (data) { el.closest('.row').find('.billing_address_state').html(data.markup); $('.loader').hide(); }
            });
        });

        $(document).on('change', 'select[name=shift_country]', function () {
            var el = $(this);
            $.ajax({ url: '{{ theme_checkout_state_url() }}', type: 'GET', data: { country: el.val() },
                beforeSend: function () { el.closest('.row').find('.shift-another-state').html(''); $('.loader').show(); },
                success: function (data) { el.closest('.row').find('.shift-another-state').html(data.markup); $('.loader').hide(); }
            });
        });

        $(document).on('change', '.billing_address_country, .billing_address_state', function () {
            var country = $('.billing_address_country :selected').val();
            var state   = $('.billing_address_state :selected').val();
            $('.coupon-country').val(country); $('.coupon-state').val(state);
            getCountryStateBasedTotal(country, state);
        });

        $(document).on('change', '.shift-another-country, .shift-another-state', function () {
            var country = $('.shift-another-country :selected').val();
            var state   = $('.shift-another-state :selected').val();
            $('.coupon-country').val(country); $('.coupon-state').val(state);
            getCountryStateBasedTotal(country, state);
        });

        $(document).on('click', 'input.shipping_methods', function () {
            var shipping_method = $(this).val();
            var total = $('.price-total').attr('data-total');
            $('.shipping-method').val(shipping_method);
            if (total !== undefined) { getShippingMethodBasedTotal(shipping_method, $('.coupon-country').val(), $('.coupon-state').val(), total); }
        });

        function getShippingMethodBasedTotal(method, country, state, total) {
            var btn = $('.checkout_disable');
            btn.addClass('proceed_checkout_btn').css({'opacity': '1', 'cursor': 'pointer'});
            $.ajax({ url: '{{ theme_checkout_shipping_ajax_url() }}', type: 'GET',
                data: { shipping_method: method, country: country, state: state, total: total },
                beforeSend: function () { $('.loader').show(); },
                success: function (data) {
                    if (data.type === 'success') {
                        var c = '{{ site_currency_symbol() }}';
                        $('.price-shipping .dk-price').html(c + data.selected_shipping_method.options.cost);
                        $('.price-total .dk-price').html(c + data.total);
                        $('.coupon-shipping-method').val(method);
                    } else { toastr.error(data.msg); btn.css({'opacity': '.5', 'cursor': 'not-allowed'}).removeClass('proceed_checkout_btn'); }
                    $('.loader').hide();
                }
            });
        }

        function getCountryStateBasedTotal(country, state) {
            $.ajax({ url: '{{ theme_checkout_total_ajax_url() }}', type: 'GET', data: { country: country, state: state },
                beforeSend: function () { $('.loader').show(); },
                success: function (data) { $('.shipping_method_wrapper').html(data.sync_price_total_markup); $('.coupon-country').val(country); $('.coupon-state').val(state); $('.loader').hide(); }
            });
        }

        $(document).on('click', '.coupon-btn', function (e) {
            e.preventDefault();
            var coupon = $('.coupon-code').val();
            $.ajax({ url: '{{ theme_checkout_coupon_ajax_url() }}', type: 'GET',
                data: { coupon: coupon, country: $('.coupon-country').val(), state: $('.coupon-state').val(), shipping_method: $('.coupon-shipping-method').val() },
                beforeSend: function () { $('.used_coupon').val(''); $('.loader').show(); },
                success: function (data) {
                    $('.loader').hide();
                    if (data.type === 'error') { toastr.error(data.msg); }
                    else if (data.type === 'success') {
                        var c = '{{ site_currency_symbol() }}';
                        $('.price-total').attr('data-total', data.coupon_amount);
                        $('.price-total .dk-price').text(c + data.coupon_amount);
                        $('.coupon-price .dk-price').text(c + data.coupon_price);
                        $('.used_coupon').val(coupon); toastr.success(data.msg);
                    }
                },
                error: function (xhr) { $.each(xhr.responseJSON?.errors ?? {}, function (k, v) { toastr.error(v); }); $('.loader').hide(); }
            });
        });

        var defaultGateway = $('#site_global_payment_gateway').val();
        if (defaultGateway && defaultGateway !== 'cash_on_delivery') {
            $('.payment-gateway-wrapper ul li[data-gateway="' + defaultGateway + '"]').addClass('selected');
            $('.payment_gateway_passing_clicking_name').val(defaultGateway);
        } else { $('#cash').prop('checked', true); $('.payment_gateway_passing_clicking_name').val('cash_on_delivery'); }

        var customFormParent = $('.payment_gateway_extra_field_information_wrap');
        customFormParent.children().hide();

        $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) {
            e.preventDefault();
            $('#cash').prop('checked', false); $('.cash_on_delivery').val('');
            var gateway = $(this).data('gateway');
            customFormParent.children().hide();
            if (gateway === 'manual_payment') {
                $('.manual_transaction_id').fadeIn().removeClass('d-none');
                $('.summernot_wrap').fadeIn(); $('.manual_description').text($(this).data('description'));
            } else {
                $('.manual_transaction_id').addClass('d-none').fadeOut(); $('.summernot_wrap').fadeOut();
                var wrapper = customFormParent.find('#' + gateway + '-parent-wrapper');
                if (wrapper.length) wrapper.fadeIn();
            }
            $(this).addClass('selected').siblings().removeClass('selected');
            $('.payment_gateway_passing_clicking_name').val(gateway);
        });

        $(document).on('keyup', '.manual_transaction_id input[name=trasaction_id]', function () { $('input[name=manual_trasaction_id]').val($(this).val()); });
        $(document).on('change', '#cash', function () {
            if ($(this).is(':checked')) { $('.payment-gateway-wrapper ul li').removeClass('selected'); $('.payment_gateway_passing_clicking_name').val('cash_on_delivery'); }
            else { $('.payment_gateway_passing_clicking_name').val(''); }
        });

        $(document).on('click', '.proceed_checkout_btn', function (e) {
            e.preventDefault();
            if ($('#agree:checked').length !== 0) { $('form.checkout-form').trigger('submit'); }
            else { toastr.error('{{ __("You need to agree to our Terms & Conditions to complete the order") }}'); }
        });

        $(document).on('keyup', 'input[name=postal_code]', function () { if (isNaN($(this).val())) $(this).val(''); });
    });
})(jQuery);
</script>
@endsection
