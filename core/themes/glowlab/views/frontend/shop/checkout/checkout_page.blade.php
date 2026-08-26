@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Checkout') }} @endsection
@section('page-title') {{ __('Checkout') }} @endsection

@section('content')
@if(Cart::count() > 0)

<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:32px 0 24px;">
    <div class="container">
        <h2 style="font-size:22px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ __('Checkout') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ __('Checkout') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:32px;">
    <div style="display:flex;align-items:center;margin-bottom:32px;">
        <div style="display:flex;align-items:center;gap:8px;background:var(--gl-gold-pale);color:var(--gl-dark);padding:10px 20px;border-radius:var(--gl-radius) 0 0 var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">
            <span style="width:20px;height:20px;border-radius:50%;background:var(--gl-gold);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:10px;"><i class="mdi mdi-check"></i></span>
            {{ __('Cart') }}
        </div>
        <div style="flex:1;height:2px;background:var(--gl-gold);"></div>
        <div style="display:flex;align-items:center;gap:8px;background:var(--gl-dark);color:#fff;padding:10px 20px;font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">
            <span style="width:20px;height:20px;border-radius:50%;background:#fff;color:var(--gl-dark);display:inline-flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;">2</span>
            {{ __('Checkout') }}
        </div>
        <div style="flex:1;height:2px;background:var(--gl-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;color:var(--gl-muted);padding:10px 20px;font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;border-radius:0 var(--gl-radius) var(--gl-radius) 0;">
            <span style="width:20px;height:20px;border-radius:50%;border:1.5px solid var(--gl-border);display:inline-flex;align-items:center;justify-content:center;font-size:10px;">3</span>
            {{ __('Confirmed') }}
        </div>
    </div>

    <x-error-msg/>

    <div class="row g-4" style="padding-bottom:72px;">

        <div class="col-lg-7">

            @if(!empty(get_static_option('guest_order_system_status')) && !theme_is_logged_in())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:20px;box-shadow:var(--gl-shadow);">
                <div style="font-size:13px;font-weight:700;color:var(--gl-dark);margin-bottom:16px;display:flex;align-items:center;gap:8px;letter-spacing:.5px;">
                    <i class="mdi mdi-account-outline" style="color:var(--gl-gold);font-size:18px;"></i> {{ __('Sign In to Continue') }}
                </div>
                <x-flash-msg/>
                <form id="login_form_order_page">
                    <div class="error-wrap mb-3"></div>
                    @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;'; @endphp
                    <div class="mb-3">
                        <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Username') }}</label>
                        <input type="text" name="username" placeholder="{{ __('Type your username') }}" style="{{ $inp }}"
                               onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                    </div>
                    <div class="mb-3">
                        <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Password') }}</label>
                        <input type="password" name="password" placeholder="{{ __('Password') }}" style="{{ $inp }}"
                               onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                    </div>
                    <button type="submit" id="login_btn"
                            style="display:inline-flex;align-items:center;gap:8px;padding:11px 24px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;">
                        {{ __('Sign In') }}
                    </button>
                </form>
            </div>
            @endif

            @php
                $readonly = $billing_info ? 'readonly' : '';
                $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:14px;font-family:inherit;outline:none;background:#fff;transition:border-color .2s;';
                $lbl = 'font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;';
            @endphp

            <form action="{{ theme_checkout_url() }}" method="POST" class="checkout-form" enctype="multipart/form-data">
                {!! theme_csrf_field() !!}
                <input type="hidden" name="payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}" class="payment_gateway_passing_clicking_name">
                <input type="hidden" name="manual_trasaction_id" value="" class="form-control">
                <input type="hidden" name="shift_another_address" class="shift_another_address">
                <input type="hidden" name="used_coupon" class="used_coupon">
                <input type="hidden" name="cash_on_delivery" class="cash_on_delivery">
                <input type="hidden" name="shipping_method" class="shipping-method">

                <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:20px;box-shadow:var(--gl-shadow);">
                    <div style="font-size:13px;font-weight:700;color:var(--gl-dark);margin-bottom:16px;display:flex;align-items:center;gap:8px;letter-spacing:.5px;">
                        <i class="mdi mdi-map-marker" style="color:var(--gl-gold);font-size:18px;"></i> {{ __('Delivery Address') }}
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label style="{{ $lbl }}">{{ __('Full Name') }}</label>
                            <input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Type Full Name') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth"
                                   {{ $readonly }} onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Mobile Number') }}</label>
                            <input type="tel" name="phone" style="{{ $inp }}" placeholder="{{ __('Type Mobile Number') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->phone : (auth('web')->user()?->mobile ?? old('phone')) }}@else{{ old('phone') }}@endauth"
                                   {{ $readonly }} onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Email Address') }}</label>
                            <input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('Type Email') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth"
                                   {{ $readonly }} onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('ZIP / PIN Code') }} <span style="color:var(--gl-gold);">*</span></label>
                            <input type="text" name="postal_code" style="{{ $inp }}" maxlength="6" placeholder="{{ __('ZIP / PIN Code') }}"
                                   value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth"
                                   {{ $readonly }} onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Country') }}</label>
                            <select name="country" style="{{ $inp }}cursor:pointer;" class="billing_address_country" id="country">
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
                            <label style="{{ $lbl }}">{{ __('Address') }}</label>
                            <textarea name="address" style="{{ $inp }}height:80px;resize:vertical;" rows="3" placeholder="{{ __('Type Address') }}"
                                      {{ $readonly }} onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">@auth('web'){{ $billing_info ? $billing_info->address : (auth('web')->user()?->address ?? old('address')) }}@else{{ old('address') }}@endauth</textarea>
                        </div>
                        <div class="col-12">
                            <label style="{{ $lbl }}">{{ __('Order Notes') }} <span style="color:var(--gl-muted);font-weight:400;">({{ __('optional') }})</span></label>
                            <textarea name="message" style="{{ $inp }}height:70px;resize:vertical;" rows="2"
                                      placeholder="{{ __('e.g. Gift wrapping, special skin concerns…') }}"
                                      onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    @if(!theme_is_logged_in())
                    <div class="mt-4">
                        <a href="javascript:void(0)" class="create-accounts" style="font-size:13px;color:var(--gl-gold);font-weight:600;text-decoration:none;">
                            + {{ __('Create an Account') }}
                        </a>
                        <input type="hidden" name="create_accounts_input" class="create_accounts_input">
                        <div class="checkout-form-open mt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label style="{{ $lbl }}">{{ __('Username') }}</label>
                                    <input type="text" name="create_username" style="{{ $inp }}" placeholder="{{ __('Type a unique username') }}">
                                </div>
                                <div class="col-md-6">
                                    <label style="{{ $lbl }}">{{ __('Password') }}</label>
                                    <input type="password" name="create_password" style="{{ $inp }}" placeholder="{{ __('Type a strong password') }}">
                                </div>
                                <div class="col-md-6">
                                    <label style="{{ $lbl }}">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="create_password_confirmation" style="{{ $inp }}" placeholder="{{ __('Confirm password') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($billing_info != null)
                        @include(include_theme_path('shop.checkout.partials.shift_another_address'))
                    @endif
                </div>

                <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;box-shadow:var(--gl-shadow);">
                    <div style="font-size:13px;font-weight:700;color:var(--gl-dark);margin-bottom:16px;display:flex;align-items:center;gap:8px;letter-spacing:.5px;">
                        <i class="mdi mdi-credit-card" style="color:var(--gl-gold);font-size:18px;"></i> {{ __('Payment Method') }}
                    </div>
                    <div>
                        <div class="mb-3">
                            <label class="d-flex align-items-center gap-2" style="font-size:14px;cursor:pointer;">
                                <input type="checkbox" id="cash" style="accent-color:var(--gl-gold);width:15px;height:15px;">
                                <i class="mdi mdi-cash" style="font-size:20px;color:var(--gl-sage);"></i>
                                {{ __('Cash On Delivery') }}
                            </label>
                        </div>
                        <div class="payment-gateway-wrapper">
                            {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                        </div>
                        <div class="payment_gateway_extra_field_information_wrap mt-3"></div>
                        <div class="form-group d-none manual_transaction_id mt-3">
                            <label style="{{ $lbl }}">{{ __('Transaction ID') }}</label>
                            <input type="text" name="trasaction_id" style="{{ $inp }}" placeholder="{{ __('Transaction ID') }}">
                        </div>
                        <div class="summernot_wrap" style="display:none;">
                            <div class="manual_description mt-2" style="font-size:13px;color:var(--gl-muted);"></div>
                        </div>
                        <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:28px;box-shadow:var(--gl-shadow);position:sticky;top:100px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--gl-border);">
                    {{ __('Your Order') }}
                </div>

                <div class="mb-3">
                    <div class="d-flex gap-2">
                        <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
                        <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
                        <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
                        <input type="text" class="coupon-code flex-grow-1"
                               style="padding:10px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:13px;font-family:inherit;outline:none;transition:border-color .2s;"
                               placeholder="{{ __('Coupon Code') }}" name="used_coupon_display"
                               onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                        <button type="button" class="coupon-btn"
                                style="flex-shrink:0;padding:10px 18px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;cursor:pointer;white-space:nowrap;transition:background .2s;"
                                onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                            {{ __('Apply') }}
                        </button>
                    </div>
                </div>

                @foreach(theme_cart_items() as $data)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px dashed var(--gl-border);">
                    <div style="width:52px;height:52px;border-radius:var(--gl-radius);overflow:hidden;border:1px solid var(--gl-border);flex-shrink:0;background:var(--gl-blush);">
                        {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--gl-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $data->name }} ×{{ $data->qty }}</div>
                        @php $gl_meta = theme_cart_item_meta($data); @endphp
                        @if($gl_meta)<div style="font-size:11px;color:var(--gl-muted);">{{ $gl_meta }}</div>@endif
                    </div>
                    <span style="font-size:13px;font-weight:700;color:var(--gl-dark);white-space:nowrap;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
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
                            $is_default = false;
                            foreach (($shipping_methods ?? []) as $key => $m) {
                                if ($key == 0) { $is_default = true; $default_shipping = $m; }
                            }
                            if (isset($default_shipping)) {
                                if ($default_shipping?->options?->tax_status) { $shipping_tax = ($default_shipping['options']['cost'] * $product_tax) / 100; }
                                $shipping = $default_shipping['options']['cost'] ?? 0;
                            }
                        }
                        $total = $subtotal + $taxed_price + $shipping_tax + $shipping;
                    @endphp

                    @php $rowStyle = 'display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px dashed var(--gl-border);font-size:13px;'; @endphp
                    <div style="{{ $rowStyle }}"><span style="color:var(--gl-muted);">{{ __('Subtotal') }}</span><span style="font-weight:600;">{{ site_currency_symbol() }}{{ Cart::subtotal() }}</span></div>
                    <div style="{{ $rowStyle }}" class="coupon-price"><span style="color:var(--gl-muted);">{{ __('Coupon Discount (-)') }}</span><span style="font-weight:600;color:var(--gl-sage);" class="gl-price">{{ amount_with_currency_symbol(0.00) }}</span></div>
                    <div style="{{ $rowStyle }}"><span style="color:var(--gl-muted);">{{ __('Tax (Incl)') }}</span><span style="font-weight:600;">{{ $product_tax }}%</span></div>

                    @if(theme_is_logged_in() && !empty($shipping_methods ?? []))
                    <div style="margin:10px 0 4px;font-size:11px;font-weight:700;color:var(--gl-dark);letter-spacing:.5px;text-transform:uppercase;">{{ __('Shipping') }}</div>
                    @foreach($shipping_methods ?? [] as $key => $method)
                    <div style="{{ $rowStyle }}">
                        <label class="d-flex align-items-center gap-2" style="cursor:pointer;font-size:13px;margin:0;">
                            <input type="radio" class="shipping_methods" id="shipping-option-{{ $method['id'] }}" name="shipping_method_display"
                                   value="{{ $method['id'] }}" style="accent-color:var(--gl-gold);" {{ $key === 0 ? 'checked' : '' }}>
                            {{ $method['name'] }}
                        </label>
                        <span style="font-weight:600;">{{ amount_with_currency_symbol($method['options']['cost']) }}</span>
                    </div>
                    @endforeach
                    @endif

                    <div style="{{ $rowStyle }}" class="price-shipping"><span style="color:var(--gl-muted);">{{ __('Shipping Cost (+)') }}</span><span style="font-weight:600;" class="gl-price">{{ isset($default_shipping) ? amount_with_currency_symbol($default_shipping['options']['cost']) : '--' }}</span></div>
                    <div style="display:flex;justify-content:space-between;padding:16px 0;font-size:15px;font-weight:700;color:var(--gl-dark);" class="price-total" data-total="{{ $subtotal + $taxed_price }}">
                        <span>{{ __('Total Amount') }}</span>
                        <span class="gl-price">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                <div class="mt-3 d-flex align-items-center gap-2">
                    <input type="checkbox" id="agree" checked style="accent-color:var(--gl-gold);width:15px;height:15px;">
                    <label for="agree" style="font-size:12px;color:var(--gl-muted);cursor:pointer;">
                        {{ __('I agree to the') }}
                        <a href="javascript:void(0)" style="color:var(--gl-gold);">{{ __('Terms & Conditions') }}</a>
                    </label>
                </div>

                <button type="button" class="proceed_checkout_btn checkout_disable"
                        style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;margin-top:16px;transition:background .2s;"
                        onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    <i class="mdi mdi-lock"></i> {{ __('Place Order') }}
                </button>

                <a href="{{ theme_cart_url() }}"
                   style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;background:transparent;color:var(--gl-dark);border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;margin-top:10px;transition:border-color .2s;"
                   onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
                    {{ __('Return to Cart') }}
                </a>

                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:11px;color:var(--gl-muted);">
                    <i class="mdi mdi-shield-check" style="color:var(--gl-sage);"></i>
                    {{ __('Secure & encrypted checkout') }}
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
                    var errors = data.responseJSON?.errors ?? {}; var html = '<div class="alert alert-danger"><ul>';
                    $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; }); html += '</ul></div>';
                    form.find('.error-wrap').html(html); el.text('{{ __("Sign In") }}');
                }
            });
        });

        $(document).on('click', '.create-accounts', function () {
            var need_account = $('.create_accounts_input');
            if (need_account.val() === '') { need_account.val('on'); $('.checkout-form-open').addClass('active'); }
            else { need_account.val(''); $('.checkout-form-open').removeClass('active'); }
        });

        $(document).on('click', '.shift-another-address', function () {
            if ($(this).hasClass('active')) { $('.shift_another_address').val('on'); }
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
                        $('.price-shipping .gl-price').html(c + data.selected_shipping_method.options.cost);
                        $('.price-total .gl-price').html(c + data.total);
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
                        $('.price-total .gl-price').text(c + data.coupon_amount);
                        $('.coupon-price .gl-price').text(c + data.coupon_price);
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
            else { toastr.error('{{ __('You need to agree to our Terms & Conditions to complete the order') }}'); }
        });

        $(document).on('keyup', 'input[name=postal_code]', function () { if (isNaN($(this).val())) $(this).val(''); });
    });
})(jQuery);
</script>
@endsection
