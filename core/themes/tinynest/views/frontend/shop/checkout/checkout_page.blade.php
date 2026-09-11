@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Checkout') }} @endsection
@section('page-title') {{ __('Checkout') }} @endsection

@section('content')
@if(Cart::count() > 0)

<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ __('Checkout') }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_cart_url() }}">{{ __('Cart') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Checkout') }}</span>
        </div>
    </div>
</div>

<div class="container tn-page-wrap">
    {!! theme_flash_msg() !!}
    {!! theme_error_msg() !!}

    {{-- Guest login --}}
    @if(!empty(get_static_option('guest_order_system_status')) && !theme_is_logged_in())
    <div class="tn-sidebar-card mb-4">
        <div class="tn-sidebar-title"><i class="las la-user me-2"></i>{{ __('Sign In to Continue') }}</div>
        <x-flash-msg/>
        <form id="tn_login_form_order_page">
            <div class="error-wrap mb-3"></div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="tn-label">{{ __('Username') }}</label>
                    <input type="text" name="username" class="tn-input" placeholder="{{ __('Type your username') }}">
                </div>
                <div class="col-md-6">
                    <label class="tn-label">{{ __('Password') }}</label>
                    <input type="password" name="password" class="tn-input" placeholder="{{ __('Password') }}">
                </div>
                <div class="col-12 d-flex align-items-center justify-content-between">
                    <label class="d-flex align-items-center gap-2" style="font-size:13px;cursor:pointer;">
                        <input type="checkbox" name="remember" style="accent-color:var(--tn-purple);"> {{ __('Remember me') }}
                    </label>
                    <a href="{{ theme_forget_password_url() }}" style="font-size:13px;color:var(--tn-purple);">{{ __('Forgot Password?') }}</a>
                </div>
                <div class="col-12">
                    <button type="submit" id="tn_login_btn" class="tn-btn tn-btn-primary">{{ __('Sign In') }}</button>
                    <p class="mt-2" style="font-size:13px;color:var(--tn-muted);">
                        {{ __("Don't have an account?") }}
                        <a href="{{ theme_register_url() }}" style="color:var(--tn-purple);font-weight:700;">{{ __('Sign up') }}</a>
                    </p>
                </div>
            </div>
        </form>
    </div>
    @endif

    <form action="{{ theme_checkout_url() }}" method="POST" class="checkout-form" enctype="multipart/form-data">
        {!! theme_csrf_field() !!}
        {{-- Required hidden fields --}}
        <input type="hidden" name="payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}" class="payment_gateway_passing_clicking_name">
        <input type="hidden" name="manual_trasaction_id" value="" class="form-control">
        <input type="hidden" name="shift_another_address" class="shift_another_address">
        <input type="hidden" name="used_coupon" class="used_coupon">
        <input type="hidden" name="cash_on_delivery" class="cash_on_delivery">
        <input type="hidden" name="shipping_method" class="shipping-method">

        <div class="row g-4">

            {{-- Billing --}}
            <div class="col-lg-7">
                <div class="tn-sidebar-card">
                    <div class="tn-sidebar-title">{{ __('Billing Details') }}</div>

                    @php $readonly = $billing_info ? 'readonly' : ''; @endphp
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="tn-label">{{ __('Full Name') }} *</label>
                            <input type="text" name="name" class="tn-input" required
                                   placeholder="{{ __('Full Name') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="tn-label">{{ __('Email') }} *</label>
                            <input type="email" name="email" class="tn-input" required
                                   placeholder="{{ __('Email Address') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="tn-label">{{ __('Phone') }} *</label>
                            <input type="tel" name="phone" class="tn-input" required
                                   placeholder="{{ __('Phone Number') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->phone : (auth('web')->user()?->mobile ?? old('phone')) }}@else{{ old('phone') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="tn-label">{{ __('Country') }} *</label>
                            <select name="country" class="tn-input billing_address_country" id="tn_country" required>
                                @if($billing_info == null)
                                    <option value="" disabled selected>{{ __('Select Country') }}</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ old('country') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option {{ $readonly }}>{{ $billing_info?->country?->name }}</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 position-relative">
                            <label class="tn-label">{{ __('State') }}</label>
                            <input type="text" name="state" class="tn-input live-state-input"
                                   placeholder="{{ __('State / Province') }}"
                                   value="{{ old('state', $billing_info?->state?->name ?? '') }}">
                            <div class="live-dropdown state-dropdown"></div>
                        </div>
                        <div class="col-md-6 position-relative">
                            <label class="tn-label">{{ __('City') }}</label>
                            <input type="text" name="city" class="tn-input live-city-input"
                                   placeholder="{{ __('City / Town') }}"
                                   value="{{ old('city', $billing_info?->city ?? '') }}">
                            <div class="live-dropdown city-dropdown"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="tn-label">{{ __('Postal Code') }}</label>
                            <input type="text" name="postal_code" class="tn-input"
                                   placeholder="{{ __('ZIP / Postal Code') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-12">
                            <label class="tn-label">{{ __('Address') }} *</label>
                            <textarea name="address" class="tn-input tn-input-textarea" rows="3"
                                      placeholder="{{ __('Street address') }}"
                                      {{ $readonly }}>@auth('web'){{ $billing_info ? $billing_info->address : (auth('web')->user()?->address ?? old('address')) }}@else{{ old('address') }}@endauth</textarea>
                        </div>
                        <div class="col-12">
                            <label class="tn-label">{{ __('Order Notes') }}</label>
                            <textarea name="message" class="tn-input tn-input-textarea" rows="2"
                                      placeholder="{{ __('Any special instructions…') }}">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    {{-- Create account for guests --}}
                    @if(!theme_is_logged_in())
                    <div class="mt-4">
                        <a href="javascript:void(0)" class="create-accounts" style="color:var(--tn-purple);font-size:13px;font-weight:700;">
                            + {{ __('Create an Account') }}
                        </a>
                        <input type="hidden" name="create_accounts_input" class="create_accounts_input">
                        <div class="checkout-form-open mt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="tn-label">{{ __('Username') }}</label>
                                    <input type="text" name="create_username" class="tn-input" placeholder="{{ __('Choose a username') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="tn-label">{{ __('Password') }}</label>
                                    <input type="password" name="create_password" class="tn-input" placeholder="{{ __('Strong password') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="tn-label">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="create_password_confirmation" class="tn-input" placeholder="{{ __('Confirm password') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Shift another address --}}
                    @if($billing_info != null)
                        @include(include_theme_path('shop.checkout.partials.shift_another_address'))
                    @endif
                </div>

                {{-- Shipping --}}
                @if(theme_shipping_methods()->isNotEmpty())
                <div class="tn-sidebar-card">
                    <div class="tn-sidebar-title">{{ __('Shipping Method') }}</div>
                    @foreach(theme_shipping_methods() as $method)
                    <label class="tn-filter-item">
                        <div class="d-flex align-items-center gap-2">
                            <input type="radio" class="shipping_methods" name="shipping_method_display"
                                   value="{{ $method->id }}" style="accent-color:var(--tn-purple);"
                                   {{ $loop->first ? 'checked' : '' }}>
                            <span class="tn-filter-label">{{ $method->name }}</span>
                        </div>
                        <span style="font-size:13px;font-weight:700;color:var(--tn-purple);">
                            {{ $method->cost > 0 ? amount_with_currency_symbol($method->cost) : __('Free') }}
                        </span>
                    </label>
                    @endforeach
                </div>
                @endif

                {{-- Payment --}}
                <div class="tn-sidebar-card">
                    <div class="tn-sidebar-title">{{ __('Payment Method') }}</div>

                    {{-- Cash on Delivery --}}
                    <div class="mb-3">
                        <label class="d-flex align-items-center gap-2" style="font-size:13px;font-weight:700;cursor:pointer;">
                            <input type="checkbox" id="tn_cash_check" style="accent-color:var(--tn-purple);width:16px;height:16px;">
                            <i class="las la-money-bill me-1" style="font-size:18px;color:var(--tn-purple);"></i>
                            {{ __('Cash On Delivery') }}
                        </label>
                    </div>

                    <div class="payment-gateway-wrapper">
                        {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                    </div>
                    <div class="payment_gateway_extra_field_information_wrap mt-3"></div>

                    {{-- Manual payment transaction ID --}}
                    <div class="manual_transaction_id d-none mt-3">
                        <label class="tn-label">{{ __('Transaction ID') }}</label>
                        <input type="text" name="trasaction_id" class="tn-input" placeholder="{{ __('Transaction ID') }}">
                    </div>
                    <div class="summernot_wrap" style="display:none;">
                        <div class="manual_description mt-2" style="font-size:13px;color:var(--tn-muted);"></div>
                    </div>

                    <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-5">
                <div class="tn-sidebar-card tn-sticky-sidebar">
                    <div class="tn-sidebar-title">{{ __('Your Order') }}</div>

                    @foreach(theme_cart_items() as $item)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom tn-summary-sep tn-order-item-row">
                        <div class="d-flex align-items-center gap-2">
                            {!! render_image_markup_by_attachment_id($item->options->image ?? null, 'tn-order-item-img') !!}
                            <div>
                                <div class="tn-order-item-name">{{ $item->name }}</div>
                                @php $tn_meta = theme_cart_item_meta($item); @endphp
                                @if($tn_meta)<div class="tn-order-item-qty">{{ $tn_meta }}</div>@endif
                                <div class="tn-order-item-qty">× {{ $item->qty }}</div>
                            </div>
                        </div>
                        <span class="tn-order-item-total">{{ amount_with_currency_symbol($item->subtotal()) }}</span>
                    </div>
                    @endforeach

                    {{-- Coupon --}}
                    <div class="tn-coupon-wrap my-3">
                        <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
                        <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
                        <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
                        <div class="d-flex gap-2">
                            <input type="text" class="coupon-code tn-input" placeholder="{{ __('Coupon Code') }}"
                                   name="used_coupon_display">
                            <button type="button" class="tn-btn tn-btn-outline tn-btn-sm coupon-btn" style="white-space:nowrap;">
                                {{ __('Apply') }}
                            </button>
                        </div>
                        <div id="tn_coupon_msg" class="tn-coupon-msg mt-1"></div>
                    </div>

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
                            $subtotal    = (double) Cart::subtotal(0, '', '');
                            $product_tax = theme_product_tax();
                            $taxed_price = ($subtotal * $product_tax) / 100;
                            $shipping_tax = 0; $shipping = 0;
                            if (theme_is_logged_in() && $has_delivery_address) {
                                foreach (($shipping_methods ?? []) as $key => $m) {
                                    if ($key == 0) { $default_shipping = $m; }
                                }
                                if (isset($default_shipping)) {
                                    if ($default_shipping?->options?->tax_status) { $shipping_tax = ($default_shipping['options']['cost'] * $product_tax) / 100; }
                                    $shipping = $default_shipping['options']['cost'] ?? 0;
                                }
                            }
                            $total = $subtotal + $taxed_price + $shipping_tax + $shipping;
                        @endphp

                        <div class="d-flex justify-content-between py-2 border-bottom tn-summary-sep">
                            <span class="tn-summary-label">{{ __('Subtotal') }}</span>
                            <strong>{{ site_currency_symbol() }}{{ Cart::subtotal() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom tn-summary-sep coupon-price">
                            <span class="tn-summary-label">{{ __('Coupon Discount (-)') }}</span>
                            <strong style="color:var(--tn-purple);" class="pf-price">{{ amount_with_currency_symbol(0.00) }}</strong>
                        </div>
                        @if($product_tax > 0)
                        <div class="d-flex justify-content-between py-2 border-bottom tn-summary-sep">
                            <span class="tn-summary-label">{{ __('Tax (Incl)') }}</span>
                            <strong>{{ $product_tax }}%</strong>
                        </div>
                        @endif

                        @if(theme_is_logged_in() && !empty($shipping_methods ?? []))
                        <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tn-muted);padding:8px 0 4px;">{{ __('Shipping') }}</div>
                        @foreach($shipping_methods ?? [] as $key => $method)
                        <div class="d-flex justify-content-between py-1 border-bottom tn-summary-sep">
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;font-size:13px;margin:0;">
                                <input type="radio" class="shipping_methods" name="shipping_method_display"
                                       value="{{ $method['id'] }}" style="accent-color:var(--tn-purple);" {{ $key === 0 ? 'checked' : '' }}>
                                {{ $method['name'] }}
                            </label>
                            <span style="font-weight:700;font-size:13px;">{{ amount_with_currency_symbol($method['options']['cost']) }}</span>
                        </div>
                        @endforeach
                        @endif

                        <div class="d-flex justify-content-between py-2 border-bottom tn-summary-sep price-shipping">
                            <span class="tn-summary-label">{{ __('Shipping Cost (+)') }}</span>
                            <strong class="pf-price">{{ isset($default_shipping) ? amount_with_currency_symbol($default_shipping['options']['cost']) : '--' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between py-3 price-total" data-total="{{ $subtotal + $taxed_price }}" style="border-top:2px solid var(--tn-border);margin-top:4px;">
                            <span class="tn-summary-total-label">{{ __('Total') }}</span>
                            <strong class="tn-order-total-val pf-price" id="tn_order_total" style="font-size:20px;color:var(--tn-purple);">
                                {{ site_currency_symbol() }}{{ $total }}
                            </strong>
                        </div>
                    </div>

                    {!! apply_filters('nazmart:cart_summary', '') !!}

                    {{-- Terms & Conditions --}}
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <input type="checkbox" id="tn_agree" checked style="accent-color:var(--tn-purple);width:15px;height:15px;">
                        <label for="tn_agree" style="font-size:12px;color:var(--tn-muted);cursor:pointer;">
                            {{ __('I agree to the') }}
                            <a href="javascript:void(0)" style="color:var(--tn-purple);">{{ __('Terms & Conditions') }}</a>
                        </label>
                    </div>

                    <button type="button" class="tn-btn tn-btn-primary w-100 proceed_checkout_btn checkout_disable">
                        <i class="las la-lock me-2"></i> {{ __('Place Order') }}
                    </button>

                    <a href="{{ theme_cart_url() }}" class="tn-btn tn-btn-outline w-100 mt-2">
                        <i class="las la-arrow-left me-1"></i> {{ __('Return to Cart') }}
                    </a>

                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:11px;color:var(--tn-muted);">
                        <i class="las la-shield-alt" style="color:var(--tn-purple);"></i>
                        {{ __('Secure & encrypted checkout') }}
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

@else
    <div class="container tn-page-wrap text-center py-5">
        <i class="las la-shopping-cart" style="font-size:64px;color:var(--tn-border);"></i>
        <h3 class="mt-3" style="color:var(--tn-muted);">{{ __('Your cart is empty') }}</h3>
        <a href="{{ theme_shop_url() }}" class="tn-btn tn-btn-primary mt-3">{{ __('Continue Shopping') }}</a>
    </div>
@endif
@endsection

@section('scripts')
<x-custom-js.ajax-login/>
@include(('themes.components.state-city-input-js'))

<script>
(function ($) {
    $(document).ready(function () {

        // Guest login
        $(document).on('click', '#tn_login_btn', function (e) {
            e.preventDefault();
            var el = $(this), form = $('#tn_login_form_order_page');
            el.text('{{ __("Please Wait") }}');
            $.ajax({
                type: 'post', url: '{{ theme_ajax_login_url() }}',
                data: { _token: '{{ theme_csrf() }}', username: form.find('[name=username]').val(), password: form.find('[name=password]').val(), remember: form.find('[name=remember]').val() },
                success: function (data) {
                    if (data.status === 'invalid') { el.text('{{ __("Sign In") }}'); form.find('.error-wrap').html('<div class="alert alert-danger">' + data.msg + '</div>'); }
                    else { el.text('{{ __("Redirecting...") }}'); location.reload(); }
                },
                error: function (data) {
                    var errors = data.responseJSON?.errors ?? {}; var html = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; }); html += '</ul></div>';
                    form.find('.error-wrap').html(html); el.text('{{ __("Sign In") }}');
                }
            });
        });

        // Create account toggle
        $(document).on('click', '.create-accounts', function () {
            var input = $('.create_accounts_input');
            if (input.val() === '') { input.val('on'); $('.checkout-form-open').addClass('active'); }
            else { input.val(''); $('.checkout-form-open').removeClass('active'); }
        });

        // Cash on delivery toggle
        $(document).on('change', '#tn_cash_check', function () {
            if ($(this).is(':checked')) {
                $('.payment-gateway-wrapper ul li').removeClass('selected');
                $('.payment_gateway_passing_clicking_name').val('cash_on_delivery');
                $('.cash_on_delivery').val('on');
            } else {
                $('.cash_on_delivery').val('');
                $('.payment_gateway_passing_clicking_name').val('');
            }
        });

        // Payment gateway selection
        var defaultGateway = $('#site_global_payment_gateway').val();
        if (defaultGateway && defaultGateway !== 'cash_on_delivery') {
            $('.payment-gateway-wrapper ul li[data-gateway="' + defaultGateway + '"]').addClass('selected');
            $('.payment_gateway_passing_clicking_name').val(defaultGateway);
        } else {
            $('#tn_cash_check').prop('checked', true);
            $('.payment_gateway_passing_clicking_name').val('cash_on_delivery');
            $('.cash_on_delivery').val('on');
        }

        var customFormParent = $('.payment_gateway_extra_field_information_wrap');
        customFormParent.children().hide();

        $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) {
            e.preventDefault();
            $('#tn_cash_check').prop('checked', false);
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

        // Country → state AJAX
        $(document).on('change', '.billing_address_country[name=country]', function () {
            var el = $(this);
            $.ajax({ url: '{{ theme_checkout_state_url() }}', type: 'GET', data: { country: el.val() },
                beforeSend: function () { el.closest('.row').find('.billing_address_state').html(''); $('.loader').show(); },
                success: function (data) { el.closest('.row').find('.billing_address_state').html(data.markup); $('.loader').hide(); }
            });
        });

        // Shipping method change
        $(document).on('click', 'input.shipping_methods', function () {
            var shipping_method = $(this).val();
            var total = $('.price-total').attr('data-total');
            $('.shipping-method').val(shipping_method);
            if (total !== undefined) {
                getShippingMethodBasedTotal(shipping_method, $('.coupon-country').val(), $('.coupon-state').val(), total);
            }
        });

        function getShippingMethodBasedTotal(method, country, state, total) {
            var btn = $('.checkout_disable');
            btn.addClass('proceed_checkout_btn').css({'opacity':'1','cursor':'pointer'});
            $.ajax({ url: '{{ theme_checkout_shipping_ajax_url() }}', type: 'GET',
                data: { shipping_method: method, country: country, state: state, total: total },
                beforeSend: function () { $('.loader').show(); },
                success: function (data) {
                    if (data.type === 'success') {
                        var c = '{{ site_currency_symbol() }}';
                        $('.price-shipping .pf-price').html(c + data.selected_shipping_method.options.cost);
                        $('.price-total .pf-price').html(c + data.total);
                        $('.coupon-shipping-method').val(method);
                    } else { toastr.error(data.msg); btn.css({'opacity':'.5','cursor':'not-allowed'}).removeClass('proceed_checkout_btn'); }
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

        $(document).on('change', '.billing_address_country, .billing_address_state', function () {
            var country = $('.billing_address_country :selected').val(), state = $('.billing_address_state :selected').val();
            $('.coupon-country').val(country); $('.coupon-state').val(state);
            getCountryStateBasedTotal(country, state);
        });

        // Coupon
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
                        $('.price-total .pf-price').text(c + data.coupon_amount);
                        $('.coupon-price .pf-price').text(c + data.coupon_price);
                        $('.used_coupon').val(coupon);
                        toastr.success(data.msg);
                    }
                },
                error: function (xhr) { $.each(xhr.responseJSON?.errors ?? {}, function (k, v) { toastr.error(v); }); $('.loader').hide(); }
            });
        });

        // Place Order
        $(document).on('click', '.proceed_checkout_btn', function (e) {
            e.preventDefault();
            if ($('#tn_agree:checked').length !== 0) { $('form.checkout-form').trigger('submit'); }
            else { toastr.error('{{ __("You need to agree to our Terms & Conditions to complete the order") }}'); }
        });

        $(document).on('keyup', 'input[name=postal_code]', function () { if (isNaN($(this).val())) $(this).val(''); });
    });
})(jQuery);
</script>
@endsection
