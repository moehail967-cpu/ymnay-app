<div class="col-xl-8 col-lg-7 mt-4">
    @if(!Auth::guard('web')->check())
        @include(include_theme_path('shop.checkout.partials.sign_in'))
    @endif

    @php $readonly = $billing_info ? 'readonly' : ''; @endphp

    <div class="ar-section-box">
        <h4 class="ar-section-box-title">{{ __('Billing Details') }}</h4>

        <form action="{{ theme_checkout_url() }}" method="POST" class="checkout-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="payment_gateway" value="{{ get_static_option('site_default_payment_gateway') }}" class="payment_gateway_passing_clicking_name">
            <input type="hidden" name="manual_trasaction_id" class="form-control" value="">
            <input type="hidden" class="shift_another_address" name="shift_another_address">
            <input type="hidden" class="used_coupon" name="used_coupon">
            <input type="hidden" class="cash_on_delivery" name="cash_on_delivery">
            <input type="hidden" class="shipping-method" name="shipping_method">

            <div class="ar-form-row">
                <div class="ar-form-col">
                    <label class="ar-label">{{ __('Full Name') }}</label>
                    <input class="ar-input" type="text" name="name" placeholder="{{ __('Type Full Name') }}"
                        value="@auth('web'){{ $billing_info ? $billing_info->full_name : auth('web')->user()?->name }}@else{{ old('name') }}@endauth" {{ $readonly }}>
                </div>
            </div>

            <div class="ar-form-row">
                <div class="ar-form-col">
                    <label class="ar-label">{{ __('Mobile Number') }}</label>
                    <input class="ar-input" type="tel" name="phone" placeholder="{{ __('Type Mobile Number') }}"
                        value="@auth('web'){{ $billing_info ? $billing_info->phone : (!empty(auth('web')->user()?->mobile) ? auth('web')->user()?->mobile : old('phone')) }}@else{{ old('phone') }}@endauth" {{ $readonly }}>
                </div>
                <div class="ar-form-col">
                    <label class="ar-label">{{ __('Email Address') }}</label>
                    <input class="ar-input" type="email" name="email" placeholder="{{ __('Type Email') }}"
                        value="@auth('web'){{ $billing_info ? $billing_info->email : auth('web')->user()?->email }}@else{{ old('email') }}@endauth" {{ $readonly }}>
                </div>
                <div class="ar-form-col">
                    <label class="ar-label">{{ __('ZIP / PIN Code') }}<x-fields.mandatory-indicator/></label>
                    <input class="ar-input" type="text" name="postal_code" placeholder="{{ __('ZIP / PIN Code') }}" maxlength="6"
                        value="@auth('web'){{ $billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code }}@else{{ old('postal_code') }}@endauth" {{ $readonly }}>
                </div>
            </div>

            <div class="ar-form-row">
                <div class="ar-form-col">
                    <label class="ar-label">{{ __('Country') }}</label>
                    <select class="ar-select billing_address_country" name="country" id="country">
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
            </div>

            <div class="ar-form-row">
                <div class="ar-form-col">
                    <label class="ar-label">{{ __('Address') }}</label>
                    <textarea class="ar-textarea" name="address" placeholder="{{ __('Type Address') }}" rows="3" {{ $readonly }}>@auth('web'){{ $billing_info ? $billing_info->address : (!empty(auth('web')->user()?->address) ? auth('web')->user()?->address : old('address')) }}@else{{ old('address') }}@endauth</textarea>
                </div>
            </div>

            @if(!Auth::guard('web')->check())
                <div class="create-account-wrapper mt-3">
                    <a href="javascript:void(0)" class="create-accounts ar-create-account-link">
                        <i class="las la-plus-circle"></i> {{ __('Create An Account') }}
                    </a>
                    <input type="hidden" class="create_accounts_input" name="create_accounts_input">
                    <div class="checkout-form-open ar-create-account-form mt-3">
                        <div class="ar-form-row">
                            <div class="ar-form-col">
                                <label class="ar-label">{{ __('Username') }}</label>
                                <input class="ar-input" type="text" name="create_username" placeholder="{{ __('Type a unique username') }}">
                            </div>
                        </div>
                        <div class="ar-form-row">
                            <div class="ar-form-col">
                                <label class="ar-label">{{ __('Password') }}</label>
                                <input class="ar-input" type="password" name="create_password" placeholder="{{ __('Type a strong password') }}">
                            </div>
                            <div class="ar-form-col">
                                <label class="ar-label">{{ __('Confirm Password') }}</label>
                                <input class="ar-input" type="password" name="create_password_confirmation" placeholder="{{ __('Confirm your password') }}">
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($billing_info != null)
                @include(include_theme_path('shop.checkout.partials.shift_another_address'))
            @endif

            <div class="ar-form-row mt-2">
                <div class="ar-form-col">
                    <label class="ar-label">{{ __('Order Notes') }}</label>
                    <textarea class="ar-textarea" name="message" placeholder="{{ __('Type Messages') }}" rows="3">{{ old('message') }}</textarea>
                </div>
            </div>
        </form>
    </div>
</div>
