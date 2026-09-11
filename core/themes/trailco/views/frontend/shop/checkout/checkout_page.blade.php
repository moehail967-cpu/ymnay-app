@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Checkout') }} @endsection
@section('page-title') {{ __('Checkout') }} @endsection

@section('content')
@if(Cart::count() > 0)

<div style="background:var(--tr-bark);padding:24px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:26px;font-weight:900;color:#fff;margin-bottom:6px;">{{ __('Checkout') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Checkout') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:28px;">
    <div style="display:flex;align-items:center;gap:0;margin-bottom:32px;max-width:480px;">
        <div style="display:flex;align-items:center;gap:8px;color:var(--tr-stone);padding:10px 18px;font-size:13px;font-weight:600;">
            <span style="width:20px;height:20px;border-radius:50%;background:var(--tr-olive);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:12px;"><i class="mdi mdi-check"></i></span> {{ __('Cart') }}
        </div>
        <div style="flex:1;height:2px;background:var(--tr-olive);"></div>
        <div style="display:flex;align-items:center;gap:8px;background:var(--tr-olive);color:#fff;padding:10px 18px;border-radius:var(--tr-radius);font-size:13px;font-weight:700;">
            <span style="width:20px;height:20px;border-radius:50%;background:#fff;color:var(--tr-olive);display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:900;">2</span> {{ __('Checkout') }}
        </div>
        <div style="flex:1;height:2px;background:var(--tr-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;color:var(--tr-stone);padding:10px 18px;font-size:13px;font-weight:600;">
            <span style="width:20px;height:20px;border-radius:50%;border:2px solid var(--tr-border);display:inline-flex;align-items:center;justify-content:center;font-size:10px;">3</span> {{ __('Done') }}
        </div>
    </div>

    <x-error-msg/>

    @php
        $inputStyle  = 'width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-family:inherit;font-size:14px;outline:none;background:#fff;transition:border-color .2s;';
        $labelStyle  = 'font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:6px;display:block;';
        $selectStyle = $inputStyle . 'cursor:pointer;';
    @endphp

    <div class="row g-4" style="padding-bottom:72px;">
        <div class="col-lg-7">

            @if(!empty(get_static_option('guest_order_system_status')) && !theme_is_logged_in())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;margin-bottom:20px;">
                <div style="background:var(--tr-bark);padding:14px 20px;font-size:13px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px;">
                    <i class="mdi mdi-account"></i> {{ __('Sign In to Continue') }}
                </div>
                <div style="padding:24px;">
                    <x-flash-msg/>
                    <form id="login_form_order_page">
                        <div class="error-wrap mb-3"></div>
                        <div class="mb-3">
                            <label style="{{ $labelStyle }}">{{ __('Username') }}</label>
                            <input type="text" name="username" placeholder="{{ __('Type your username') }}" style="{{ $inputStyle }}"
                                   onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                        </div>
                        <div class="mb-3">
                            <label style="{{ $labelStyle }}">{{ __('Password') }}</label>
                            <input type="password" name="password" placeholder="{{ __('Password') }}" style="{{ $inputStyle }}"
                                   onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="d-flex align-items-center gap-2" style="font-size:13px;cursor:pointer;">
                                <input type="checkbox" name="remember" style="accent-color:var(--tr-olive);"> {{ __('Remember me') }}
                            </label>
                            <a href="{{ theme_forget_password_url() }}" style="font-size:13px;color:var(--tr-olive);">{{ __('Forgot Password?') }}</a>
                        </div>
                        <button type="submit" id="login_btn" class="tr-btn tr-btn-primary">{{ __('Sign In') }}</button>
                    </form>
                </div>
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

                <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;margin-bottom:20px;">
                    <div style="background:var(--tr-bark);padding:14px 20px;font-size:13px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px;"><i class="mdi mdi-map-marker"></i> {{ __('Delivery Address') }}</div>
                    <div style="padding:24px;">
                        <div class="row g-3">
                            <div class="col-12">
                                <label style="{{ $labelStyle }}">{{ __('Full Name') }}</label>
                                <input type="text" name="name" style="{{ $inputStyle }}" placeholder="{{ __('Type Full Name') }}"
                                       value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth"
                                       onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'"
                                       {{ $readonly }}>
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $labelStyle }}">{{ __('Mobile Number') }}</label>
                                <input type="tel" name="phone" style="{{ $inputStyle }}" placeholder="{{ __('Type Mobile Number') }}"
                                       value="@auth('web'){{ $billing_info ? $billing_info->phone : (auth('web')->user()?->mobile ?? old('phone')) }}@else{{ old('phone') }}@endauth"
                                       onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'"
                                       {{ $readonly }}>
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $labelStyle }}">{{ __('Email Address') }}</label>
                                <input type="email" name="email" style="{{ $inputStyle }}" placeholder="{{ __('Type Email') }}"
                                       value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth"
                                       onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'"
                                       {{ $readonly }}>
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $labelStyle }}">{{ __('ZIP / PIN Code') }}</label>
                                <input type="text" name="postal_code" style="{{ $inputStyle }}" maxlength="6" placeholder="{{ __('ZIP / PIN Code') }}"
                                       value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth"
                                       onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'"
                                       {{ $readonly }}>
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $labelStyle }}">{{ __('Country') }}</label>
                                <select name="country" style="{{ $selectStyle }}" class="billing_address_country" id="country">
                                    @if($billing_info == null)
                                        <option value="" selected disabled>{{ __('Select a country') }}</option>
                                        @foreach($countries as $country) <option value="{{ $country->id }}">{{ $country->name }}</option> @endforeach
                                    @else
                                        <option {{ $readonly }}>{{ $billing_info?->country?->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="tr-state-city-row">
                                @include(('themes.components.state-city-input'))
                            </div>
                            <div class="col-12">
                                <label style="{{ $labelStyle }}">{{ __('Address') }}</label>
                                <textarea name="address" style="{{ $inputStyle }}height:80px;resize:vertical;" rows="3" placeholder="{{ __('Type Address') }}"
                                          onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'"
                                          {{ $readonly }}>@auth('web'){{ $billing_info ? $billing_info->address : (auth('web')->user()?->address ?? old('address')) }}@else{{ old('address') }}@endauth</textarea>
                            </div>
                            <div class="col-12">
                                <label style="{{ $labelStyle }}">{{ __('Order Notes') }}</label>
                                <textarea name="message" style="{{ $inputStyle }}height:70px;resize:vertical;" rows="2" placeholder="{{ __('e.g. Leave at trailhead, instructions…') }}"
                                          onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">{{ old('message') }}</textarea>
                            </div>
                        </div>
                        @if(!theme_is_logged_in())
                        <div class="mt-4">
                            <a href="javascript:void(0)" class="create-accounts" style="color:var(--tr-olive);font-size:13px;font-weight:700;">+ {{ __('Create an Account') }}</a>
                            <input type="hidden" name="create_accounts_input" class="create_accounts_input">
                            <div class="checkout-form-open mt-3">
                                <div class="row g-3">
                                    <div class="col-12"><label style="{{ $labelStyle }}">{{ __('Username') }}</label><input type="text" name="create_username" style="{{ $inputStyle }}" placeholder="{{ __('Type a unique username') }}"></div>
                                    <div class="col-md-6"><label style="{{ $labelStyle }}">{{ __('Password') }}</label><input type="password" name="create_password" style="{{ $inputStyle }}" placeholder="{{ __('Type a strong password') }}"></div>
                                    <div class="col-md-6"><label style="{{ $labelStyle }}">{{ __('Confirm Password') }}</label><input type="password" name="create_password_confirmation" style="{{ $inputStyle }}" placeholder="{{ __('Confirm password') }}"></div>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($billing_info != null)
                            @include(include_theme_path('shop.checkout.partials.shift_another_address'))
                        @endif
                    </div>
                </div>

                <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;">
                    <div style="background:var(--tr-bark);padding:14px 20px;font-size:13px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px;"><i class="mdi mdi-credit-card-outline"></i> {{ __('Payment Method') }}</div>
                    <div style="padding:24px;">
                        <div class="mb-3">
                            <label class="d-flex align-items-center gap-2" style="font-size:14px;cursor:pointer;">
                                <input type="checkbox" id="cash" style="accent-color:var(--tr-olive);width:16px;height:16px;">
                                <i class="mdi mdi-cash" style="font-size:20px;color:var(--tr-olive);"></i>
                                {{ __('Cash On Delivery') }}
                            </label>
                        </div>
                        <div class="payment-gateway-wrapper">{!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}</div>
                        <div class="payment_gateway_extra_field_information_wrap mt-3"></div>
                        <div class="form-group d-none manual_transaction_id mt-3">
                            <label style="{{ $labelStyle }}">{{ __('Transaction ID') }}</label>
                            <input type="text" name="trasaction_id" style="{{ $inputStyle }}" placeholder="{{ __('Transaction ID') }}">
                        </div>
                        <div class="summernot_wrap" style="display:none;"><div class="manual_description mt-2" style="font-size:13px;color:var(--tr-stone);"></div></div>
                        <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;position:sticky;top:100px;">
                <div style="background:var(--tr-bark);padding:14px 20px;font-size:13px;font-weight:700;color:#fff;">{{ __('Your Order') }}</div>
                <div style="padding:24px;">
                    <div class="mb-3">
                        <div class="d-flex gap-2">
                            <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
                            <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
                            <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
                            <input type="text" class="coupon-code flex-grow-1" style="padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:13px;outline:none;"
                                   placeholder="{{ __('Coupon Code') }}" name="used_coupon_display"
                                   onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                            <button type="button" class="tr-btn tr-btn-terra coupon-btn" style="font-size:12px;white-space:nowrap;padding:0 16px;">{{ __('Apply') }}</button>
                        </div>
                    </div>

                    @foreach(theme_cart_items() as $data)
                    <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--tr-border);">
                        <div style="width:52px;height:52px;border-radius:var(--tr-radius);overflow:hidden;border:1px solid var(--tr-border);flex-shrink:0;">{!! render_image_markup_by_attachment_id($data?->options?->image) !!}</div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:13px;font-weight:700;color:var(--tr-bark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $data->name }} ×{{ $data->qty }}</div>
                            @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                            <div style="font-size:11px;color:var(--tr-stone);">
                                @if($data?->options?->color_name) {{ $data->options->color_name }} @endif
                                @if($data?->options?->size_name) · {{ $data->options->size_name }} @endif
                                @foreach((array)($data?->options?->attributes ?? []) as $attrK => $attrV)
                                    @if($attrV !== null && $attrV !== '') · {{ ucfirst($attrK) }}: {{ $attrV }} @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <span style="font-size:13px;font-weight:900;color:var(--tr-bark);white-space:nowrap;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
                    </div>
                    @endforeach

                    <div class="shipping_method_wrapper">
                        @php
                            $has_delivery_address = false; $authUser = auth('web')->user();
                            if ($authUser?->delivery_address) {
                                $has_delivery_address = true;
                                $country = (string)($authUser->delivery_address->country_id); $state = (string)($authUser->delivery_address->state_id);
                                $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)->whereJsonContains('state', $state)->pluck('zone_id')->toArray();
                                if (empty($shipping_zones)) { $shipping_zones = \Modules\ShippingModule\Entities\ZoneRegion::whereJsonContains('country', $country)->pluck('zone_id')->toArray(); }
                                $shipping_methods = \Modules\ShippingModule\Entities\ShippingMethod::with('options')->whereIn('zone_id', $shipping_zones)->get();
                            }
                        @endphp
                        @php
                            $subtotal = (double) Cart::subtotal(0, '', ''); $product_tax = theme_product_tax(); $taxed_price = ($subtotal * $product_tax) / 100; $shipping_tax = 0; $shipping = 0;
                            if (theme_is_logged_in() && $has_delivery_address) { foreach (($shipping_methods ?? []) as $key => $m) { if ($key == 0) { $default_shipping = $m; } } if (isset($default_shipping)) { if ($default_shipping?->options?->tax_status) { $shipping_tax = ($default_shipping['options']['cost'] * $product_tax) / 100; } $shipping = $default_shipping['options']['cost'] ?? 0; } }
                            $total = $subtotal + $taxed_price + $shipping_tax + $shipping;
                        @endphp
                        @php $rowStyle = 'display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--tr-border);font-size:13px;'; @endphp
                        <div style="{{ $rowStyle }} margin-top:12px;"><span style="color:var(--tr-stone);">{{ __('Subtotal') }}</span><span style="font-weight:700;">{{ site_currency_symbol() }}{{ Cart::subtotal() }}</span></div>
                        <div style="{{ $rowStyle }}" class="coupon-price"><span style="color:var(--tr-stone);">{{ __('Coupon Discount (-)') }}</span><span style="font-weight:700;color:var(--tr-terra);" class="pf-price">{{ amount_with_currency_symbol(0.00) }}</span></div>
                        <div style="{{ $rowStyle }}"><span style="color:var(--tr-stone);">{{ __('Tax (Incl)') }}</span><span style="font-weight:700;">{{ $product_tax }}%</span></div>
                        @if(theme_is_logged_in() && !empty($shipping_methods ?? []))
                        <div style="margin:10px 0 4px;font-size:12px;font-weight:700;color:var(--tr-bark);">{{ __('Shipping') }}</div>
                        @foreach($shipping_methods ?? [] as $key => $method)
                        <div style="{{ $rowStyle }}" data-country="{{ $country ?? '' }}" data-state="{{ $state ?? '' }}">
                            <label class="d-flex align-items-center gap-2" style="cursor:pointer;font-size:13px;margin:0;">
                                <input type="radio" class="shipping_methods" id="shipping-option-{{ $method['id'] }}" name="shipping_method_display" value="{{ $method['id'] }}" style="accent-color:var(--tr-olive);" {{ $key === 0 ? 'checked' : '' }}> {{ $method['name'] }}
                            </label>
                            <span style="font-weight:700;">{{ amount_with_currency_symbol($method['options']['cost']) }}</span>
                        </div>
                        @endforeach
                        @endif
                        <div style="{{ $rowStyle }}" class="price-shipping"><span style="color:var(--tr-stone);">{{ __('Shipping Cost (+)') }}</span><span style="font-weight:700;" class="pf-price">{{ isset($default_shipping) ? amount_with_currency_symbol($default_shipping['options']['cost']) : '--' }}</span></div>
                        <div style="display:flex;justify-content:space-between;padding:14px 0;border-top:1px solid var(--tr-border);margin-top:4px;" class="price-total" data-total="{{ $subtotal + $taxed_price }}">
                            <span style="font-size:15px;font-weight:900;color:var(--tr-bark);">{{ __('Total Amount') }}</span>
                            <span class="tr-price pf-price" style="font-size:18px;">{{ site_currency_symbol() }}{{ $total }}</span>
                        </div>
                    </div>

                    {!! apply_filters('nazmart:cart_summary', '') !!}
                    <div class="mt-3 d-flex align-items-center gap-2">
                        <input type="checkbox" id="agree" checked style="accent-color:var(--tr-olive);width:15px;height:15px;">
                        <label for="agree" style="font-size:12px;color:var(--tr-stone);cursor:pointer;">{{ __('I agree to the') }} <a href="javascript:void(0)" style="color:var(--tr-olive);">{{ __('Terms & Conditions') }}</a></label>
                    </div>

                    <button type="button" class="tr-btn tr-btn-primary proceed_checkout_btn checkout_disable" style="width:100%;justify-content:center;margin-top:16px;">
                        <i class="mdi mdi-lock"></i> {{ __('Place Order') }}
                    </button>
                    <a href="{{ theme_cart_url() }}" class="tr-btn" style="width:100%;justify-content:center;margin-top:8px;background:transparent;border:1.5px solid var(--tr-border);color:var(--tr-bark);">{{ __('Return to Cart') }}</a>
                    <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:12px;color:var(--tr-stone);">
                        <i class="mdi mdi-shield-check" style="color:var(--tr-olive);"></i> {{ __('Secure & encrypted checkout') }}
                    </div>
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
<x-custom-js.ajax-login/>
@include(('themes.components.state-city-input-js'))
<script>
(function ($) {
    $(document).ready(function () {
        $(document).on('click', '#login_btn', function (e) {
            e.preventDefault(); var el = $(this), form = $('#login_form_order_page'); el.text('{{ __("Please Wait") }}');
            $.ajax({ type: 'post', url: '{{ theme_ajax_login_url() }}', data: { _token: '{{ theme_csrf() }}', username: form.find('[name=username]').val(), password: form.find('[name=password]').val(), remember: form.find('[name=remember]').val() },
                success: function (data) { if (data.status === 'invalid') { el.text('{{ __("Sign In") }}'); form.find('.error-wrap').html('<div class="alert alert-danger">' + data.msg + '</div>'); } else { el.text('{{ __("Redirecting...") }}'); location.reload(); } },
                error: function (data) { var errors = data.responseJSON?.errors ?? {}; var html = '<div class="alert alert-danger"><ul>'; $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; }); html += '</ul></div>'; form.find('.error-wrap').html(html); el.text('{{ __("Sign In") }}'); }
            });
        });
        $(document).on('click', '.create-accounts', function () { var need_account = $('.create_accounts_input'); if (need_account.val() === '') { need_account.val('on'); $('.checkout-form-open').addClass('active'); } else { need_account.val(''); $('.checkout-form-open').removeClass('active'); } });
        $(document).on('click', '.shift-another-address', function () { if ($(this).hasClass('active')) { $.each($('.shift-address-form input'), function (k, v) { $(v).val(''); }); $('.shift_another_address').val('on'); } else { $('.shift_another_address').val(''); } });
        $(document).on('change', '.billing_address_country[name=country]', function () { var el = $(this); $.ajax({ url: '{{ theme_checkout_state_url() }}', type: 'GET', data: { country: el.val() }, beforeSend: function () { el.closest('.row').find('.billing_address_state').html(''); $('.loader').show(); }, success: function (data) { el.closest('.row').find('.billing_address_state').html(data.markup); $('.loader').hide(); } }); });
        $(document).on('change', 'select[name=shift_country]', function () { var el = $(this); $.ajax({ url: '{{ theme_checkout_state_url() }}', type: 'GET', data: { country: el.val() }, beforeSend: function () { el.closest('.row').find('.shift-another-state').html(''); $('.loader').show(); }, success: function (data) { el.closest('.row').find('.shift-another-state').html(data.markup); $('.loader').hide(); } }); });
        $(document).on('change', '.billing_address_country, .billing_address_state', function () { var country = $('.billing_address_country :selected').val(), state = $('.billing_address_state :selected').val(); $('.coupon-country').val(country); $('.coupon-state').val(state); getCountryStateBasedTotal(country, state); });
        $(document).on('change', '.shift-another-country, .shift-another-state', function () { var country = $('.shift-another-country :selected').val(), state = $('.shift-another-state :selected').val(); $('.coupon-country').val(country); $('.coupon-state').val(state); getCountryStateBasedTotal(country, state); });
        $(document).on('click', 'input.shipping_methods', function () { var shipping_method = $(this).val(), total = $('.price-total').attr('data-total'); $('.shipping-method').val(shipping_method); if (total !== undefined) { getShippingMethodBasedTotal(shipping_method, $('.coupon-country').val(), $('.coupon-state').val(), total); } });
        function getShippingMethodBasedTotal(method, country, state, total) { var btn = $('.checkout_disable'); btn.addClass('proceed_checkout_btn').css({'opacity':'1','cursor':'pointer'}); $.ajax({ url: '{{ theme_checkout_shipping_ajax_url() }}', type: 'GET', data: { shipping_method: method, country: country, state: state, total: total }, beforeSend: function () { $('.loader').show(); }, success: function (data) { if (data.type === 'success') { var c = '{{ site_currency_symbol() }}'; $('.price-shipping .pf-price').html(c + data.selected_shipping_method.options.cost); $('.price-total .pf-price').html(c + data.total); $('.coupon-shipping-method').val(method); } else { toastr.error(data.msg); btn.css({'opacity':'.5','cursor':'not-allowed'}).removeClass('proceed_checkout_btn'); } $('.loader').hide(); } }); }
        function getCountryStateBasedTotal(country, state) { $.ajax({ url: '{{ theme_checkout_total_ajax_url() }}', type: 'GET', data: { country: country, state: state }, beforeSend: function () { $('.loader').show(); }, success: function (data) { $('.shipping_method_wrapper').html(data.sync_price_total_markup); $('.coupon-country').val(country); $('.coupon-state').val(state); $('.loader').hide(); } }); }
        $(document).on('click', '.coupon-btn', function (e) { e.preventDefault(); var coupon = $('.coupon-code').val(); $.ajax({ url: '{{ theme_checkout_coupon_ajax_url() }}', type: 'GET', data: { coupon: coupon, country: $('.coupon-country').val(), state: $('.coupon-state').val(), shipping_method: $('.coupon-shipping-method').val() }, beforeSend: function () { $('.used_coupon').val(''); $('.loader').show(); }, success: function (data) { $('.loader').hide(); if (data.type === 'error') { toastr.error(data.msg); } else if (data.type === 'success') { var c = '{{ site_currency_symbol() }}'; $('.price-total').attr('data-total', data.coupon_amount); $('.price-total .pf-price').text(c + data.coupon_amount); $('.coupon-price .pf-price').text(c + data.coupon_price); $('.used_coupon').val(coupon); toastr.success(data.msg); } }, error: function (xhr) { $.each(xhr.responseJSON?.errors ?? {}, function (k, v) { toastr.error(v); }); $('.loader').hide(); } }); });
        var defaultGateway = $('#site_global_payment_gateway').val();
        if (defaultGateway && defaultGateway !== 'cash_on_delivery') { $('.payment-gateway-wrapper ul li[data-gateway="' + defaultGateway + '"]').addClass('selected'); $('.payment_gateway_passing_clicking_name').val(defaultGateway); } else { $('#cash').prop('checked', true); $('.payment_gateway_passing_clicking_name').val('cash_on_delivery'); }
        var customFormParent = $('.payment_gateway_extra_field_information_wrap'); customFormParent.children().hide();
        $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) { e.preventDefault(); $('#cash').prop('checked', false); $('.cash_on_delivery').val(''); var gateway = $(this).data('gateway'); customFormParent.children().hide(); if (gateway === 'manual_payment') { $('.manual_transaction_id').fadeIn().removeClass('d-none'); $('.summernot_wrap').fadeIn(); $('.manual_description').text($(this).data('description')); } else { $('.manual_transaction_id').addClass('d-none').fadeOut(); $('.summernot_wrap').fadeOut(); var wrapper = customFormParent.find('#' + gateway + '-parent-wrapper'); if (wrapper.length) wrapper.fadeIn(); } $(this).addClass('selected').siblings().removeClass('selected'); $('.payment_gateway_passing_clicking_name').val(gateway); });
        $(document).on('keyup', '.manual_transaction_id input[name=trasaction_id]', function () { $('input[name=manual_trasaction_id]').val($(this).val()); });
        $(document).on('change', '#cash', function () { if ($(this).is(':checked')) { $('.payment-gateway-wrapper ul li').removeClass('selected'); $('.payment_gateway_passing_clicking_name').val('cash_on_delivery'); } else { $('.payment_gateway_passing_clicking_name').val(''); } });
        $(document).on('click', '.proceed_checkout_btn', function (e) { e.preventDefault(); if ($('#agree:checked').length !== 0) { $('form.checkout-form').trigger('submit'); } else { toastr.error('{{ __('You need to agree to our Terms & Conditions to complete the order') }}'); } });
        $(document).on('keyup', 'input[name=postal_code]', function () { if (isNaN($(this).val())) $(this).val(''); });
    });
})(jQuery);
</script>
@endsection
