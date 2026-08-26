<div class="col-lg-8">
    @if(!Auth::guard('web')->check())
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
                <div class="col-12">
                    <button type="submit" id="login_btn" class="bp-btn bp-btn-green">{{ __('Sign In') }}</button>
                </div>
            </div>
        </form>
    </div>
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
