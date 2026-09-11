@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Checkout') }} @endsection
@section('page-title') {{ __('Checkout') }} @endsection

@section('style')
<style>
.checkout-form .single-input { position: relative; margin-bottom: 0; }
.checkout-form .single-input .label-title { font-size:11px; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:var(--ms-muted); margin-bottom:7px; display:block; line-height:1.4; }
.checkout-form .single-input .form--control { width:100%; height:44px !important; line-height:1; padding:0 14px; border:1px solid var(--ms-border) !important; border-radius:var(--ms-radius); background:#fff; color:var(--ms-dark) !important; font-size:14px; font-family:inherit; outline:none; box-shadow:none !important; transition:border-color .2s; box-sizing:border-box; }
.checkout-form .single-input .form--control:focus { border-color:var(--ms-linen) !important; box-shadow:none !important; }
.live-dropdown { position:absolute; left:0; right:0; background:#fff; border:1px solid var(--ms-border); border-top:0; border-radius:0 0 var(--ms-radius) var(--ms-radius); max-height:220px; overflow-y:auto; z-index:999; display:none; box-shadow:var(--ms-shadow); }
</style>
@endsection

@section('content')
@if(Cart::count() > 0)

<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:36px 0 24px;">
    <div class="container">
        <h1 style="font-size:13px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Almost There') }}</h1>
        <h2 style="font-size:28px;font-weight:300;color:var(--ms-dark);margin:0 0 10px;">{{ __('Checkout') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <a href="{{ theme_cart_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;">{{ __('Cart') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('Checkout') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:32px;">
    {{-- Step Bar --}}
    <div style="display:flex;align-items:center;margin-bottom:36px;">
        <div style="display:flex;align-items:center;gap:8px;background:var(--ms-warm);color:var(--ms-muted);padding:10px 22px;border-radius:var(--ms-radius) 0 0 var(--ms-radius);font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">
            <span style="width:24px;height:24px;border-radius:50%;background:var(--ms-linen);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:11px;"><i class="mdi mdi-check"></i></span>
            {{ __('Cart') }}
        </div>
        <div style="flex:1;height:1px;background:var(--ms-linen);"></div>
        <div style="display:flex;align-items:center;gap:8px;background:var(--ms-linen);color:#fff;padding:10px 22px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">
            <span style="width:24px;height:24px;border-radius:50%;background:#fff;color:var(--ms-linen-d);display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;">2</span>
            {{ __('Checkout') }}
        </div>
        <div style="flex:1;height:1px;background:var(--ms-border);"></div>
        <div style="display:flex;align-items:center;gap:8px;color:var(--ms-muted);padding:10px 22px;border-radius:0 var(--ms-radius) var(--ms-radius) 0;font-size:11px;font-weight:600;letter-spacing:.08em;text-transform:uppercase;">
            <span style="width:24px;height:24px;border-radius:50%;border:1.5px solid var(--ms-border);display:inline-flex;align-items:center;justify-content:center;font-size:11px;">3</span>
            {{ __('Confirmed') }}
        </div>
    </div>

    <x-error-msg/>

    <div class="row g-4" style="padding-bottom:80px;">

        <div class="col-lg-7">

            {{-- Guest Login Panel --}}
            @if(!empty(get_static_option('guest_order_system_status')) && !theme_is_logged_in())
            <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:24px;margin-bottom:20px;box-shadow:var(--ms-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:16px;">
                    {{ __('Sign In to Continue') }}
                </div>
                <x-flash-msg/>
                <form id="login_form_order_page">
                    <div class="error-wrap mb-3"></div>
                    @php $iS = 'width:100%;padding:10px 14px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:14px;font-family:inherit;outline:none;background:#fff;color:var(--ms-dark);transition:border-color .2s;'; @endphp
                    <div class="mb-3">
                        <label style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;display:block;">{{ __('Username') }}</label>
                        <input type="text" name="username" placeholder="{{ __('Type your username') }}" style="{{ $iS }}"
                               onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                    </div>
                    <div class="mb-3">
                        <label style="font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;display:block;">{{ __('Password') }}</label>
                        <input type="password" name="password" placeholder="{{ __('Password') }}" style="{{ $iS }}"
                               onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;color:var(--ms-charcoal);">
                            <input type="checkbox" name="remember" style="accent-color:var(--ms-linen-d);"> {{ __('Remember me') }}
                        </label>
                        <a href="{{ theme_forget_password_url() }}" style="font-size:13px;color:var(--ms-linen-d);font-weight:600;text-decoration:none;">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn"
                            style="display:inline-flex;align-items:center;gap:6px;padding:10px 22px;background:var(--ms-dark);color:#fff;border:none;border-radius:var(--ms-radius);font-size:12px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='var(--ms-linen-d)'"
                            onmouseout="this.style.background='var(--ms-dark)'">
                        {{ __('Sign In') }}
                    </button>
                    <p style="margin-top:14px;font-size:13px;color:var(--ms-muted);">
                        {{ __("Don't have an account?") }}
                        <a href="{{ theme_register_url() }}" style="color:var(--ms-linen-d);font-weight:600;text-decoration:none;">{{ __('Register') }}</a>
                    </p>
                </form>
            </div>
            @endif

            @php
                $readonly   = $billing_info ? 'readonly' : '';
                $iS         = 'width:100%;padding:10px 14px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:14px;font-family:inherit;outline:none;background:#fff;color:var(--ms-dark);transition:border-color .2s;';
                $lS         = 'font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;display:block;';
                $selS       = $iS . 'cursor:pointer;';
            @endphp

            <form action="{{ theme_checkout_url() }}" method="POST" class="checkout-form" enctype="multipart/form-data">
                {!! theme_csrf_field() !!}
                <input type="hidden" name="payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}" class="payment_gateway_passing_clicking_name">
                <input type="hidden" name="manual_trasaction_id" value="" class="form-control">
                <input type="hidden" name="shift_another_address" class="shift_another_address">
                <input type="hidden" name="used_coupon" class="used_coupon">
                <input type="hidden" name="cash_on_delivery" class="cash_on_delivery">
                <input type="hidden" name="shipping_method" class="shipping-method">

                {{-- Delivery Address --}}
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:28px;margin-bottom:20px;box-shadow:var(--ms-shadow);">
                    <div style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--ms-border);">
                        <i class="mdi mdi-map-marker-outline" style="color:var(--ms-linen-d);margin-right:6px;font-size:14px;"></i>
                        {{ __('Delivery Address') }}
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label style="{{ $lS }}">{{ __('Full Name') }}</label>
                            <input type="text" name="name" style="{{ $iS }}" placeholder="{{ __('Full Name') }}"
                                   onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'"
                                   value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lS }}">{{ __('Mobile Number') }}</label>
                            <input type="tel" name="phone" style="{{ $iS }}" placeholder="{{ __('Mobile Number') }}"
                                   onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'"
                                   value="@auth('web'){{ $billing_info ? $billing_info->phone : (auth('web')->user()?->mobile ?? old('phone')) }}@else{{ old('phone') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lS }}">{{ __('Email Address') }}</label>
                            <input type="email" name="email" style="{{ $iS }}" placeholder="{{ __('Email Address') }}"
                                   onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'"
                                   value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lS }}">{{ __('ZIP / Postal Code') }} <span style="color:var(--ms-linen);">*</span></label>
                            <input type="text" name="postal_code" style="{{ $iS }}" maxlength="10" placeholder="{{ __('Postal Code') }}"
                                   onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'"
                                   value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth"
                                   {{ $readonly }}>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lS }}">{{ __('Country') }}</label>
                            <select name="country" style="{{ $selS }}" class="billing_address_country" id="country"
                                    onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                                @if($billing_info == null)
                                    <option value="" selected disabled>{{ __('Select Country') }}</option>
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
                            <label style="{{ $lS }}">{{ __('Address') }}</label>
                            <textarea name="address" style="{{ $iS }}height:80px;resize:vertical;" rows="3"
                                      onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'"
                                      placeholder="{{ __('Street address, apartment, etc.') }}"
                                      {{ $readonly }}>@auth('web'){{ $billing_info ? $billing_info->address : (auth('web')->user()?->address ?? old('address')) }}@else{{ old('address') }}@endauth</textarea>
                        </div>
                        <div class="col-12">
                            <label style="{{ $lS }}">{{ __('Order Notes') }} <span style="color:var(--ms-muted);font-weight:400;text-transform:none;">({{ __('optional') }})</span></label>
                            <textarea name="message" style="{{ $iS }}height:70px;resize:vertical;" rows="2"
                                      onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'"
                                      placeholder="{{ __('Special requests, delivery instructions…') }}">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    @if(!theme_is_logged_in())
                    <div style="margin-top:20px;padding-top:16px;border-top:1px dashed var(--ms-border);">
                        <a href="javascript:void(0)" class="create-accounts"
                           style="font-size:13px;color:var(--ms-linen-d);font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                            <i class="mdi mdi-plus-circle-outline"></i> {{ __('Create an Account') }}
                        </a>
                        <input type="hidden" name="create_accounts_input" class="create_accounts_input">
                        <div class="checkout-form-open mt-3">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label style="{{ $lS }}">{{ __('Username') }}</label>
                                    <input type="text" name="create_username" style="{{ $iS }}" placeholder="{{ __('Choose a username') }}"
                                           onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                                </div>
                                <div class="col-md-6">
                                    <label style="{{ $lS }}">{{ __('Password') }}</label>
                                    <input type="password" name="create_password" style="{{ $iS }}" placeholder="{{ __('Strong password') }}"
                                           onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                                </div>
                                <div class="col-md-6">
                                    <label style="{{ $lS }}">{{ __('Confirm Password') }}</label>
                                    <input type="password" name="create_password_confirmation" style="{{ $iS }}" placeholder="{{ __('Confirm') }}"
                                           onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
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
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:28px;box-shadow:var(--ms-shadow);">
                    <div style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--ms-border);">
                        <i class="mdi mdi-credit-card-outline" style="color:var(--ms-linen-d);margin-right:6px;font-size:14px;"></i>
                        {{ __('Payment Method') }}
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:flex;align-items:center;gap:10px;padding:12px 16px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);cursor:pointer;transition:border-color .2s;"
                               onmouseover="this.style.borderColor='var(--ms-linen)'" onmouseout="this.style.borderColor='var(--ms-border)'">
                            <input type="checkbox" id="cash" style="accent-color:var(--ms-linen-d);width:15px;height:15px;">
                            <i class="mdi mdi-cash" style="font-size:20px;color:var(--ms-olive);"></i>
                            <span style="font-size:14px;color:var(--ms-dark);font-weight:500;">{{ __('Cash On Delivery') }}</span>
                        </label>
                    </div>

                    <div class="payment-gateway-wrapper">
                        {!! (new \App\Helpers\PaymentGatewayRenderHelper())->renderPaymentGatewayForForm() !!}
                    </div>

                    <div class="payment_gateway_extra_field_information_wrap mt-3"></div>

                    <div class="form-group d-none manual_transaction_id mt-3">
                        <label style="{{ $lS }}">{{ __('Transaction ID') }}</label>
                        <input type="text" name="trasaction_id" style="{{ $iS }}" placeholder="{{ __('Transaction ID') }}"
                               onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                    </div>
                    <div class="summernot_wrap" style="display:none;">
                        <div class="manual_description mt-2" style="font-size:13px;color:var(--ms-muted);padding:10px 14px;background:var(--ms-warm);border-radius:var(--ms-radius);"></div>
                    </div>

                    <input type="hidden" id="site_global_payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}">
                </div>
            </form>
        </div>

        {{-- Order Summary Sidebar --}}
        <div class="col-lg-5">
            <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:28px;box-shadow:var(--ms-shadow);position:sticky;top:100px;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:20px;padding-bottom:14px;border-bottom:1px solid var(--ms-border);">
                    {{ __('Your Order') }}
                </div>

                {{-- Coupon --}}
                <div style="margin-bottom:20px;">
                    <div style="display:flex;gap:8px;">
                        <input type="hidden" class="coupon-country" name="coupon_country" value="{{ $billing_info ? $billing_info->country_id : '' }}">
                        <input type="hidden" class="coupon-state" name="coupon_state" value="{{ $billing_info ? $billing_info->state_id : '' }}">
                        <input type="hidden" class="coupon-shipping-method" name="coupon_shipping_method" value="">
                        <input type="text" class="coupon-code" style="flex:1;padding:9px 14px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:13px;font-family:inherit;outline:none;color:var(--ms-dark);transition:border-color .2s;" placeholder="{{ __('Coupon Code') }}" name="used_coupon_display"
                               onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                        <button type="button" class="coupon-btn"
                                style="padding:9px 18px;background:var(--ms-linen);color:#fff;border:none;border-radius:var(--ms-radius);font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;cursor:pointer;white-space:nowrap;transition:background .2s;"
                                onmouseover="this.style.background='var(--ms-linen-d)'"
                                onmouseout="this.style.background='var(--ms-linen)'">
                            {{ __('Apply') }}
                        </button>
                    </div>
                </div>

                {{-- Cart Items --}}
                @foreach(theme_cart_items() as $data)
                <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px dashed var(--ms-border);">
                    <div style="width:52px;height:52px;border-radius:var(--ms-radius);overflow:hidden;border:1px solid var(--ms-border);flex-shrink:0;background:var(--ms-warm);">
                        {!! render_image_markup_by_attachment_id($data?->options?->image) !!}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:500;color:var(--ms-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $data->name }} ×{{ $data->qty }}</div>
                        @if($data?->options?->color_name || $data?->options?->size_name || !empty($data?->options?->attributes))
                        <div style="font-size:11px;color:var(--ms-muted);">
                            @if($data?->options?->color_name) {{ $data->options->color_name }} @endif
                            @if($data?->options?->size_name) · {{ $data->options->size_name }} @endif
                            @foreach((array)($data?->options?->attributes ?? []) as $attrK => $attrV)
                                @if($attrV !== null && $attrV !== '') · {{ ucfirst($attrK) }}: {{ $attrV }} @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <span style="font-size:13px;font-weight:600;color:var(--ms-linen-d);white-space:nowrap;">{{ amount_with_currency_symbol($data->price * $data->qty) }}</span>
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
                                if ($default_shipping?->options?->tax_status) { $shipping_tax = ($default_shipping['options']['cost'] * $product_tax) / 100; }
                                $shipping = $default_shipping['options']['cost'] ?? 0;
                            }
                        }
                        $total = $subtotal + $taxed_price + $shipping_tax + $shipping;
                        $rowS  = 'display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px dashed var(--ms-border);font-size:13px;';
                    @endphp

                    <div style="{{ $rowS }}"><span style="color:var(--ms-muted);">{{ __('Subtotal') }}</span><span style="font-weight:600;color:var(--ms-dark);">{{ site_currency_symbol() }}{{ Cart::subtotal() }}</span></div>
                    <div style="{{ $rowS }}" class="coupon-price"><span style="color:var(--ms-muted);">{{ __('Coupon Discount (−)') }}</span><span style="font-weight:600;color:var(--ms-olive);" class="ms-price">{{ amount_with_currency_symbol(0.00) }}</span></div>
                    <div style="{{ $rowS }}"><span style="color:var(--ms-muted);">{{ __('Tax') }}</span><span style="font-weight:500;color:var(--ms-charcoal);">{{ $product_tax }}%</span></div>

                    @if(theme_is_logged_in() && !empty($shipping_methods ?? []))
                    <div style="margin:10px 0 4px;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);">{{ __('Shipping') }}</div>
                    @foreach($shipping_methods ?? [] as $key => $method)
                    <div style="{{ $rowS }}" data-country="{{ $country ?? '' }}" data-state="{{ $state ?? '' }}">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;margin:0;color:var(--ms-dark);">
                            <input type="radio" class="shipping_methods" id="shipping-option-{{ $method['id'] }}" name="shipping_method_display"
                                   value="{{ $method['id'] }}" style="accent-color:var(--ms-linen-d);" {{ $key === 0 ? 'checked' : '' }}>
                            {{ $method['name'] }}
                        </label>
                        <span style="font-weight:600;color:var(--ms-dark);">{{ amount_with_currency_symbol($method['options']['cost']) }}</span>
                    </div>
                    @endforeach
                    @endif

                    <div style="{{ $rowS }}" class="price-shipping"><span style="color:var(--ms-muted);">{{ __('Shipping Cost (+)') }}</span><span style="font-weight:600;color:var(--ms-dark);" class="ms-price">{{ isset($default_shipping) ? amount_with_currency_symbol($default_shipping['options']['cost']) : '--' }}</span></div>
                    <div style="display:flex;justify-content:space-between;padding:16px 0;font-size:16px;font-weight:700;color:var(--ms-dark);" class="price-total" data-total="{{ $subtotal + $taxed_price }}">
                        <span>{{ __('Total Amount') }}</span>
                        <span style="color:var(--ms-linen-d);" class="ms-price">{{ site_currency_symbol() }}{{ $total }}</span>
                    </div>
                </div>

                {!! apply_filters('nazmart:cart_summary', '') !!}

                <div style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
                    <input type="checkbox" id="ms_agree" checked style="accent-color:var(--ms-linen-d);width:14px;height:14px;">
                    <label for="ms_agree" style="font-size:12px;color:var(--ms-muted);cursor:pointer;line-height:1.4;">
                        {{ __('I agree to the') }}
                        <a href="javascript:void(0)" style="color:var(--ms-linen-d);font-weight:600;text-decoration:none;">{{ __('Terms & Conditions') }}</a>
                    </label>
                </div>

                <button type="button" class="proceed_checkout_btn checkout_disable"
                        style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px;background:var(--ms-dark);color:#fff;border:none;border-radius:var(--ms-radius);font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:background .2s;margin-bottom:10px;"
                        onmouseover="this.style.background='var(--ms-linen-d)'"
                        onmouseout="this.style.background='var(--ms-dark)'">
                    <i class="mdi mdi-lock-outline" style="font-size:16px;"></i>
                    {{ __('Place Order') }}
                </button>

                <a href="{{ theme_cart_url() }}"
                   style="display:flex;align-items:center;justify-content:center;gap:6px;padding:12px;background:transparent;color:var(--ms-charcoal);border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;text-decoration:none;transition:all .2s;"
                   onmouseover="this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)'"
                   onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-charcoal)'">
                    {{ __('Return to Cart') }}
                </a>

                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:14px;font-size:11px;color:var(--ms-muted);letter-spacing:.04em;">
                    <i class="mdi mdi-shield-check-outline" style="color:var(--ms-olive);"></i>
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
            el.text('{{ __("Please Wait") }}…');
            $.ajax({
                type: 'post', url: '{{ theme_ajax_login_url() }}',
                data: { _token: '{{ theme_csrf() }}', username: form.find('[name=username]').val(), password: form.find('[name=password]').val(), remember: form.find('[name=remember]').val() },
                success: function (data) {
                    if (data.status === 'invalid') { el.text('{{ __("Sign In") }}'); form.find('.error-wrap').html('<div style="padding:10px 14px;background:#FEF2F2;border:1px solid #FECACA;border-radius:4px;color:#C0392B;font-size:13px;">' + data.msg + '</div>'); }
                    else { el.text('{{ __("Redirecting…") }}'); location.reload(); }
                },
                error: function (data) {
                    var errors = data.responseJSON?.errors ?? {}, html = '<div style="padding:10px 14px;background:#FEF2F2;border:1px solid #FECACA;border-radius:4px;color:#C0392B;font-size:13px;"><ul style="margin:0;padding-left:16px;">';
                    $.each(errors, function (k, v) { html += '<li>' + v + '</li>'; }); html += '</ul></div>';
                    form.find('.error-wrap').html(html); el.text('{{ __("Sign In") }}');
                }
            });
        });

        $(document).on('click', '.create-accounts', function () {
            var need = $('.create_accounts_input');
            if (need.val() === '') { need.val('on'); $('.checkout-form-open').addClass('active'); }
            else { need.val(''); $('.checkout-form-open').removeClass('active'); }
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
            btn.addClass('proceed_checkout_btn').css({'opacity':'1','cursor':'pointer'});
            $.ajax({ url: '{{ theme_checkout_shipping_ajax_url() }}', type: 'GET',
                data: { shipping_method: method, country: country, state: state, total: total },
                beforeSend: function () { $('.loader').show(); },
                success: function (data) {
                    if (data.type === 'success') {
                        var c = '{{ site_currency_symbol() }}';
                        $('.price-shipping .ms-price').html(c + data.selected_shipping_method.options.cost);
                        $('.price-total .ms-price').html(c + data.total);
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
                        $('.price-total .ms-price').text(c + data.coupon_amount);
                        $('.coupon-price .ms-price').text(c + data.coupon_price);
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
            if ($('#ms_agree:checked').length !== 0) { $('form.checkout-form').trigger('submit'); }
            else { toastr.error('{{ __('You need to agree to our Terms & Conditions to complete the order') }}'); }
        });

        $(document).on('keyup', 'input[name=postal_code]', function () { if (isNaN($(this).val())) $(this).val(''); });
    });
})(jQuery);
</script>
@endsection
