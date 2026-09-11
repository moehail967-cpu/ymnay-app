<div class="mc-checkout-left mt-4">
    @if(!Auth::guard('web')->check())
        @include(include_theme_path('shop.checkout.partials.sign_in'))
    @endif

    @php $readonly = $billing_info ? 'readonly' : ''; @endphp

    <div class="mc-checkout-box mt-4">
        <div class="mc-dash-card-title"><i class="las la-map-marker"></i> {{__('Billing Details')}}</div>
        <div class="checkout-form-wrapper">
            <form action="{{theme_checkout_url()}}" method="POST" class="checkout-form" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="payment_gateway" value="{{get_static_option('site_default_payment_gateway')}}" class="payment_gateway_passing_clicking_name">
                <input type="hidden" name="manual_trasaction_id" value="">
                <input type="hidden" class="shift_another_address" name="shift_another_address">
                <input type="hidden" class="used_coupon" name="used_coupon">
                <input type="hidden" class="cash_on_delivery" name="cash_on_delivery">
                <input type="hidden" class="shipping-method" name="shipping_method">

                <div class="mc-form-grid mt-3">
                    <div class="mc-form-group">
                        <label class="mc-form-label">{{__('Full Name')}}</label>
                        <input class="mc-form-input" type="text" name="name" placeholder="{{__('Type Full Name')}}" value="@auth('web'){{$billing_info ? $billing_info->full_name:auth('web')->user()?->name}}@else{{old('name')}}@endauth" {{$readonly}}>
                    </div>
                </div>

                <div class="mc-form-grid mt-3">
                    <div class="mc-form-group">
                        <label class="mc-form-label">{{__('Mobile Number')}}</label>
                        <input class="mc-form-input" type="tel" placeholder="{{__('Type Mobile Number')}}" name="phone" value="@auth('web'){{$billing_info ? $billing_info->phone : (!empty(auth('web')->user()?->mobile) ? auth('web')->user()?->mobile : old('phone'))}}@else{{old('phone')}}@endauth" {{$readonly}}>
                    </div>
                    <div class="mc-form-group">
                        <label class="mc-form-label">{{__('Email Address')}}</label>
                        <input class="mc-form-input" type="email" placeholder="{{__('Type Email')}}" name="email" value="@auth('web'){{$billing_info ? $billing_info->email : auth('web')->user()?->email}}@else{{old('email')}}@endauth" {{$readonly}}>
                    </div>
                    <div class="mc-form-group">
                        <label class="mc-form-label">{{__('ZIP / PIN Code')}} <x-fields.mandatory-indicator/></label>
                        <input class="mc-form-input" type="text" placeholder="{{__('ZIP / PIN Code')}}" name="postal_code" maxlength="6" value="@auth('web'){{$billing_info ? $billing_info->postal_code : auth('web')->user()?->postal_code}}@else{{old('postal_code')}}@endauth" {{$readonly}}>
                    </div>
                </div>

                <div class="mc-form-grid mt-3">
                    <div class="mc-form-group">
                        <label class="mc-form-label">{{__('Country')}}</label>
                        <select class="mc-form-select billing_address_country" name="country" id="country">
                            @if($billing_info == null)
                                <option value="" selected disabled>{{__('Select a country')}}</option>
                                @foreach($countries as $country)
                                    <option value="{{$country->id}}">{{$country->name}}</option>
                                @endforeach
                            @else
                                <option {{$readonly}}>{{$billing_info?->country?->name}}</option>
                            @endif
                        </select>
                    </div>
                    @include(('themes.components.state-city-input'))
                </div>

                <div class="mc-form-group mt-3">
                    <label class="mc-form-label">{{__('Address')}}</label>
                    <textarea class="mc-form-textarea" name="address" placeholder="{{__('Type Address')}}" {{$readonly}}>@auth('web'){{$billing_info ? $billing_info->address : (!empty(auth('web')->user()?->address) ? auth('web')->user()?->address : old('address'))}}@else{{old('address')}}@endauth</textarea>
                </div>

                @if(!Auth::guard('web')->check())
                    <div class="mt-4">
                        <a href="javascript:void(0)" class="mc-checkout-link create-accounts click-open-form2">{{__('+ Create An Account')}}</a>
                        <input type="hidden" class="create_accounts_input" name="create_accounts_input">
                        <div class="checkout-form-open mt-3" style="display:none;">
                            <div class="mc-form-grid">
                                <div class="mc-form-group">
                                    <label class="mc-form-label">{{__('Username')}}</label>
                                    <input class="mc-form-input" type="text" name="create_username" placeholder="{{__('Type a unique username')}}">
                                </div>
                            </div>
                            <div class="mc-form-grid mt-3">
                                <div class="mc-form-group">
                                    <label class="mc-form-label">{{__('Password')}}</label>
                                    <input class="mc-form-input" type="password" name="create_password" placeholder="{{__('Type a strong password')}}">
                                </div>
                                <div class="mc-form-group">
                                    <label class="mc-form-label">{{__('Confirm Password')}}</label>
                                    <input class="mc-form-input" type="password" name="create_password_confirmation" placeholder="{{__('Confirm your password')}}">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($billing_info != null)
                    @include(include_theme_path('shop.checkout.partials.shift_another_address'))
                @endif

                <div class="mc-form-group mt-3">
                    <label class="mc-form-label">{{__('Order Notes')}}</label>
                    <textarea class="mc-form-textarea" name="message" placeholder="{{__('Type Messages')}}">{{old('message')}}</textarea>
                </div>
            </form>
        </div>
    </div>
</div>
