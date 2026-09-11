@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Checkout') }} @endsection
@section('page-title') {{ __('Checkout') }} @endsection

@section('style')
<style>
/* Override shared state-city-input component to match FitPeak dark theme */
.single-input .label-title,
.single-input label { font-family:'Barlow Condensed',sans-serif; font-size:12px; font-weight:700; color:var(--fp-muted); text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:6px; }
.single-input .form--control,
.single-input input { width:100%; background:var(--fp-surface); border:1px solid var(--fp-border); color:var(--fp-text); padding:10px 14px; font-size:14px; border-radius:var(--fp-radius); outline:none; transition:border-color .2s; }
.single-input .form--control:focus,
.single-input input:focus { border-color:var(--fp-green); }
/* Live dropdown */
.live-dropdown { position:absolute; top:100%; left:0; right:0; background:var(--fp-surface); border:1px solid var(--fp-border); z-index:100; max-height:200px; overflow-y:auto; border-radius:0 0 var(--fp-radius) var(--fp-radius); }
.live-dropdown .dropdown-item { padding:8px 14px; color:var(--fp-muted); font-size:13px; cursor:pointer; }
.live-dropdown .dropdown-item:hover { background:var(--fp-green-glow); color:var(--fp-green); }
</style>
@endsection

@section('content')
@if(Cart::count() > 0)

{{-- Progress Steps --}}
<div class="container" style="padding-top:28px;">
    <div class="d-flex align-items-center justify-content-center gap-0 mb-4">
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--fp-green);color:#000;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;margin:0 auto 6px;">
                <i class="mdi mdi-check" style="font-size:18px;"></i>
            </div>
            <div style="font-size:12px;font-weight:700;color:var(--fp-green);font-family:'Barlow Condensed',sans-serif;text-transform:uppercase;letter-spacing:1px;">{{ __('Cart') }}</div>
        </div>
        <div style="flex:2;height:2px;background:var(--fp-green);margin-bottom:20px;"></div>
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--fp-green);color:#000;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;margin:0 auto 6px;">2</div>
            <div style="font-size:12px;font-weight:700;color:var(--fp-green);font-family:'Barlow Condensed',sans-serif;text-transform:uppercase;letter-spacing:1px;">{{ __('Checkout') }}</div>
        </div>
        <div style="flex:2;height:2px;background:var(--fp-border);margin-bottom:20px;"></div>
        <div class="text-center" style="flex:1;">
            <div style="width:36px;height:36px;border-radius:50%;background:var(--fp-border);color:var(--fp-muted);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;margin:0 auto 6px;">3</div>
            <div style="font-size:12px;font-weight:700;color:var(--fp-muted);font-family:'Barlow Condensed',sans-serif;text-transform:uppercase;letter-spacing:1px;">{{ __('Confirmed') }}</div>
        </div>
    </div>

    <x-error-msg/>

    <div class="row g-4" style="padding-bottom:72px;">

        {{-- Left: Billing Form --}}
        <div class="col-lg-7">

            @if(!empty(get_static_option('guest_order_system_status')) && !theme_is_logged_in())
            <div class="fp-section-box">
                <div class="fp-section-box-title"><i class="mdi mdi-account-outline"></i> {{ __('Sign In to Continue') }}</div>
                <x-flash-msg/>
                <form id="login_form_order_page">
                    <div class="error-wrap mb-3"></div>
                    <div class="mb-3">
                        <label class="fp-label">{{ __('Username') }}</label>
                        <input type="text" name="username" class="fp-checkout-input" placeholder="{{ __('Type your username') }}">
                    </div>
                    <div class="mb-3">
                        <label class="fp-label">{{ __('Password') }}</label>
                        <input type="password" name="password" class="fp-checkout-input" placeholder="{{ __('Password') }}">
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <label class="d-flex align-items-center gap-2" style="font-size:13px;cursor:pointer;color:var(--fp-muted);">
                            <input type="checkbox" name="remember" style="accent-color:var(--fp-green);"> {{ __('Remember me') }}
                        </label>
                        <a href="{{ theme_forget_password_url() }}" style="font-size:13px;color:var(--fp-green);">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="fp-btn fp-btn-primary">{{ __('Sign In') }}</button>
                    <p class="mt-3" style="font-size:13px;color:var(--fp-muted);">
                        {{ __("Don't have an account?") }}
                        <a href="{{ theme_register_url() }}" style="color:var(--fp-green);">{{ __('Sign up') }}</a>
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

                {{-- Delivery Address --}}
                <div class="fp-section-box">
                    <div class="fp-section-box-title"><i class="mdi mdi-map-marker-outline"></i> {{ __('Delivery Address') }}</div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="fp-label">{{ __('Full Name') }}</label>
                            <input type="text" name="name" class="fp-checkout-input"
                                   placeholder="{{ __('Type Full Name') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="fp-label">{{ __('Mobile Number') }}</label>
                            <input type="tel" name="phone" class="fp-checkout-input"
                                   placeholder="{{ __('Type Mobile Number') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->phone : (auth('web')->user()?->mobile ?? old('phone')) }}@else{{ old('phone') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="fp-label">{{ __('Email Address') }}</label>
                            <input type="email" name="email" class="fp-checkout-input"
                                   placeholder="{{ __('Type Email') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="fp-label">{{ __('ZIP / PIN Code') }} <span style="color:var(--fp-green);">*</span></label>
                            <input type="text" name="postal_code" class="fp-checkout-input" maxlength="6"
                                   placeholder="{{ __('ZIP / PIN Code') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label class="fp-label">{{ __('Country') }}</label>
                            <select name="country" class="fp-checkout-input billing_address_country" id="country">
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
                            <label class="fp-label">{{ __('Address') }}</label>
                            <textarea name="address" class="fp-checkout-input" rows="3"
                                      placeholder="{{ __('Type Address') }}"
                                      style="height:auto;resize:vertical;" {{ $readonly }}>@auth('web'){{ $billing_info ? $billing_info->address : (auth('web')->user()?->address ?? old('address')) }}@else{{ old('address') }}@endauth</textarea>
                        </div>
                        <div class="col-12">
                            <label class="fp-label">{{ __('Order Notes') }} <span style="color:var(--fp-muted);font-weight:400;">({{ __('optional') }})</span></label>
                            <textarea name="message" class="fp-checkout-input" rows="2"
                                      placeholder="{{ __('e.g. Leave at door, ring bell…') }}"
                                      style="height:auto;resize:vertical;">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    @if(!theme_is_logged_in())
                    <div class="mt-4">
                        <a href="javascript:void(0)" class="create-accounts fp-btn fp-btn-outline" style="font-size:13px;display:inline-flex;">
                            <i class="mdi mdi-account-plus-outline"></i> {{ __('Create an Account') }}
                        </a>
                        <input type="hidden" name="create_accounts_input" class="create_accounts_input" style="display:none!important;width:auto!important;border:none!important;padding:0!important;">
                        <div class="checkout-form-open mt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="fp-label">{{ __('Username') }}</label>
                                    <input type="text" name="create_username" class="fp-checkout-input" placeholder="{{ __('Type a unique username') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="fp-label">{{ __('Password') }}</label>
                                    <input type="password" name="create_password" class="fp-checkout-input" placeholder="{{ __('Strong password') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="fp-label">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="create_password_confirmation" class="fp-checkout-input" placeholder="{{ __('Confirm password') }}">
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
                <div class="fp-section-box">
                    <div class="fp-section-box-title"><i class="mdi mdi-credit-card-outline"></i> {{ __('Payment Method') }}</div>

                    <div class="fp-checkout-payment-wrap">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <label class="d-flex align-items-center gap-2" style="font-size:14px;cursor:pointer;color:var(--fp-muted);">
                                <input type="checkbox" id="cash" style="accent-color:var(--fp-green);width:16px;height:16px;">
                                <i class="mdi mdi-cash-multiple" style="font-size:20px;color:var(--fp-green);"></i>
                                {{ __('Cash On Delivery') }}
                            </label>
                        </div>

                        <div class="payment-gateway-wrapper">
                            {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                        </div>

                        <div class="payment_gateway_extra_field_information_wrap mt-3"></div>

                        <div class="form-group d-none manual_transaction_id mt-3">
                            <label class="fp-label">{{ __('Transaction ID') }}</label>
                            <input type="text" name="trasaction_id" class="fp-checkout-input" placeholder="{{ __('Transaction ID') }}">
                        </div>
                        <div class="summernot_wrap" style="display:none;">
                            <div class="manual_description mt-2" style="font-size:13px;color:var(--fp-muted);"></div>
                        </div>

                        <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
                    </div>
                </div>

            </form>
        </div>

        {{-- Right: Order Summary --}}
        <div class="col-lg-5">
            <div class="fp-order-summary">
                <div class="fp-order-summary-title">{{ __('Your Order') }}</div>

                {{-- Coupon --}}
                <div class="mb-3">
                    <div class="d-flex gap-2">
                        <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
                        <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
                        <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
                        <input type="text" class="fp-checkout-input flex-grow-1 coupon-code"
                               placeholder="{{ __('Coupon Code') }}" name="used_coupon_display">
                        <button type="button" class="fp-btn fp-btn-primary fp-btn-sm coupon-btn" style="white-space:nowrap;">
                            {{ __('Apply') }}
                        </button>
                    </div>
                </div>

                {{-- Mini Cart --}}
                @foreach(theme_cart_items() as $data)
                <div class="fp-mini-cart-row">
                    <div class="fp-mini-cart-img">
                        {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:700;color:var(--fp-white);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $data->name }} ×{{ $data->qty }}
                        </div>
                        @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                        <div style="font-size:11px;color:var(--fp-muted);">
                            @if($data?->options?->color_name) {{ $data->options->color_name }} @endif
                            @if($data?->options?->size_name) · {{ $data->options->size_name }} @endif
                            @foreach((array)($data?->options?->attributes ?? []) as $attrK => $attrV)
                                @if($attrV !== null && $attrV !== '') · {{ ucfirst($attrK) }}: {{ $attrV }} @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <span class="fp-price" style="font-size:13px;white-space:nowrap;">
                        {{ amount_with_currency_symbol($data->price * $data->qty) }}
                    </span>
                </div>
                @endforeach

                {{-- Price Breakdown --}}
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
                    @endphp

                    @php
                        $subtotal    = (double) Cart::subtotal(0, '', '');
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
                                if ($default_shipping?->options?->tax_status) {
                                    $shipping_tax = ($default_shipping['options']['cost'] * $product_tax) / 100;
                                }
                                $shipping = $default_shipping['options']['cost'] ?? 0;
                            }
                        }
                        $total = $subtotal + $taxed_price + $shipping_tax + $shipping;
                    @endphp

                    <div class="fp-summary-row mt-2">
                        <span>{{ __('Subtotal') }}</span>
                        <span class="fp-summary-price">{{ site_currency_symbol() }}{{ Cart::subtotal() }}</span>
                    </div>
                    <div class="fp-summary-row coupon-price">
                        <span>{{ __('Coupon Discount (-)') }}</span>
                        <span class="fp-summary-price" style="color:var(--fp-green);">{{ amount_with_currency_symbol(0.00) }}</span>
                    </div>
                    <div class="fp-summary-row">
                        <span>{{ __('Tax (Incl)') }}</span>
                        <span class="fp-summary-price">{{ $product_tax }}%</span>
                    </div>

                    @if(theme_is_logged_in() && !empty($shipping_methods ?? []))
                    <div style="margin:10px 0 4px;font-size:12px;font-weight:700;color:var(--fp-white);text-transform:uppercase;letter-spacing:1px;">{{ __('Shipping') }}</div>
                    @foreach($shipping_methods ?? [] as $key => $method)
                    <div class="fp-summary-row" data-country="{{ $country ?? '' }}" data-state="{{ $state ?? '' }}">
                        <label class="d-flex align-items-center gap-2" style="cursor:pointer;font-size:13px;margin:0;color:var(--fp-muted);">
                            <input type="radio" class="shipping_methods"
                                   id="shipping-option-{{ $method['id'] }}"
                                   name="shipping_method_display"
                                   value="{{ $method['id'] }}"
                                   style="accent-color:var(--fp-green);"
                                   {{ $key === 0 ? 'checked' : '' }}>
                            {{ $method['name'] }}
                        </label>
                        <span class="fp-summary-price">{{ amount_with_currency_symbol($method['options']['cost']) }}</span>
                    </div>
                    @endforeach
                    @endif

                    <div class="fp-summary-row price-shipping">
                        <span>{{ __('Shipping Cost (+)') }}</span>
                        <span class="fp-summary-price">
                            {{ isset($default_shipping) ? amount_with_currency_symbol($default_shipping['options']['cost']) : '--' }}
                        </span>
                    </div>

                    <div class="fp-summary-row total price-total" data-total="{{ $subtotal + $taxed_price }}">
                        <span>{{ __('Total Amount') }}</span>
                        <span class="fp-summary-price" style="color:var(--fp-green);">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                {{-- Terms & Place Order --}}
                <div class="mt-3 d-flex align-items-center gap-2">
                    <input type="checkbox" id="agree" checked style="accent-color:var(--fp-green);width:15px;height:15px;">
                    <label for="agree" style="font-size:12px;color:var(--fp-muted);cursor:pointer;">
                        {{ __('I agree to the') }}
                        <a href="javascript:void(0)" style="color:var(--fp-green);">{{ __('Terms & Conditions') }}</a>
                    </label>
                </div>

                <button type="button" class="fp-btn fp-btn-primary w-100 justify-content-center proceed_checkout_btn checkout_disable mt-3"
                        style="font-size:15px;padding:14px;">
                    <i class="mdi mdi-lock-outline"></i> {{ __('Place Order') }}
                </button>

                <a href="{{ theme_cart_url() }}" class="fp-btn fp-btn-outline w-100 justify-content-center mt-2">
                    {{ __('Return to Cart') }}
                </a>

                <p style="font-size:11px;color:var(--fp-muted);text-align:center;margin-top:12px;line-height:1.5;">
                    <i class="mdi mdi-shield-check-outline" style="color:var(--fp-green);"></i>
                    {{ __('Secure & encrypted checkout') }}
                </p>
            </div>
        </div>

    </div>
</div>

@else
    @include(include_theme_path('shop.cart.cart_empty'))
@endif
@endsection

@section('scripts')
<x-custom-js.ajax-login/>
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
                data: { _token: '{{ theme_csrf() }}', username: form.find('[name=username]').val(), password: form.find('[name=password]').val(), remember: form.find('[name=remember]').val() },
                success: function (data) {
                    if (data.status === 'invalid') { el.text('{{ __("Sign In") }}'); form.find('.error-wrap').html('<div class="alert alert-danger">' + data.msg + '</div>'); }
                    else { el.text('{{ __("Redirecting...") }}'); location.reload(); }
                },
                error: function (data) {
                    var errors = data.responseJSON?.errors ?? {}, html = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; });
                    html += '</ul></div>';
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
            var country = $('.billing_address_country :selected').val(), state = $('.billing_address_state :selected').val();
            $('.coupon-country').val(country); $('.coupon-state').val(state);
            getCountryStateBasedTotal(country, state);
        });

        $(document).on('change', '.shift-another-country, .shift-another-state', function () {
            var country = $('.shift-another-country :selected').val(), state = $('.shift-another-state :selected').val();
            $('.coupon-country').val(country); $('.coupon-state').val(state);
            getCountryStateBasedTotal(country, state);
        });

        $(document).on('click', 'input.shipping_methods', function () {
            var shipping_method = $(this).val(), total = $('.price-total').attr('data-total');
            $('.shipping-method').val(shipping_method);
            if (total !== undefined) { getShippingMethodBasedTotal(shipping_method, $('.coupon-country').val(), $('.coupon-state').val(), total); }
        });

        function getShippingMethodBasedTotal(method, country, state, total) {
            var btn = $('.checkout_disable');
            btn.addClass('proceed_checkout_btn').css({'opacity': '1', 'cursor': 'pointer'});
            $.ajax({
                url: '{{ theme_checkout_shipping_ajax_url() }}', type: 'GET',
                data: { shipping_method: method, country: country, state: state, total: total },
                beforeSend: function () { $('.loader').show(); },
                success: function (data) {
                    if (data.type === 'success') {
                        var c = '{{ site_currency_symbol() }}';
                        $('.price-shipping .fp-summary-price').html(c + data.selected_shipping_method.options.cost);
                        $('.price-total .fp-summary-price').html(c + data.total);
                        $('.coupon-shipping-method').val(method);
                    } else { toastr.error(data.msg); btn.css({'opacity': '.5', 'cursor': 'not-allowed'}).removeClass('proceed_checkout_btn'); }
                    $('.loader').hide();
                }
            });
        }

        function getCountryStateBasedTotal(country, state) {
            $.ajax({
                url: '{{ theme_checkout_total_ajax_url() }}', type: 'GET',
                data: { country: country, state: state },
                beforeSend: function () { $('.loader').show(); },
                success: function (data) { $('.shipping_method_wrapper').html(data.sync_price_total_markup); $('.coupon-country').val(country); $('.coupon-state').val(state); $('.loader').hide(); }
            });
        }

        $(document).on('click', '.coupon-btn', function (e) {
            e.preventDefault();
            var coupon = $('.coupon-code').val();
            $.ajax({
                url: '{{ theme_checkout_coupon_ajax_url() }}', type: 'GET',
                data: { coupon: coupon, country: $('.coupon-country').val(), state: $('.coupon-state').val(), shipping_method: $('.coupon-shipping-method').val() },
                beforeSend: function () { $('.used_coupon').val(''); $('.loader').show(); },
                success: function (data) {
                    $('.loader').hide();
                    if (data.type === 'error') { toastr.error(data.msg); }
                    else if (data.type === 'success') {
                        var c = '{{ site_currency_symbol() }}';
                        $('.price-total').attr('data-total', data.coupon_amount);
                        $('.price-total .fp-summary-price').text(c + data.coupon_amount);
                        $('.coupon-price .fp-summary-price').text(c + data.coupon_price);
                        $('.used_coupon').val(coupon);
                        toastr.success(data.msg);
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
            if (gateway === 'manual_payment') { $('.manual_transaction_id').fadeIn().removeClass('d-none'); $('.summernot_wrap').fadeIn(); $('.manual_description').text($(this).data('description')); }
            else { $('.manual_transaction_id').addClass('d-none').fadeOut(); $('.summernot_wrap').fadeOut(); var wrapper = customFormParent.find('#' + gateway + '-parent-wrapper'); if (wrapper.length) wrapper.fadeIn(); }
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
            else { toastr.error('{{ __('You need to agree to our Terms & Conditions to complete the order') }}'); }
        });

        $(document).on('keyup', 'input[name=postal_code]', function () { if (isNaN($(this).val())) $(this).val(''); });

    });
})(jQuery);
</script>
@endsection
