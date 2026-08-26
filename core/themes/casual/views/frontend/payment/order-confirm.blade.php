@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Order Confirm') }} @endsection
@section('page-title') {{ __('Order Confirm') }} @endsection

@section('content')
<div class="cs-result-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="cs-dash-box">
                    <div class="cs-dash-box-head">
                        <i class="las la-receipt"></i> {{ __('Order Details') }}
                    </div>
                    <div class="cs-dash-box-body">
                        {!! theme_flash_msg() !!}
                        {!! theme_error_msg() !!}
                        <form action="{{ theme_payment_form_url() }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @php
                                $custom_fields = unserialize($order_details->custom_fields);
                                $payment_gateway = !empty($custom_fields['selected_payment_gateway']) ? $custom_fields['selected_payment_gateway'] : '';
                                $name  = auth()->guard('web')->check() ? auth()->guard('web')->user()->name  : '';
                                $email = auth()->guard('web')->check() ? auth()->guard('web')->user()->email : '';
                            @endphp
                            <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                            <input type="hidden" name="payment_gateway" value="{{ $payment_gateway }}">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="cs-dash-label">{{ __('Your Name') }}</label>
                                    @if(auth()->check())
                                        <input type="text" name="name" value="{{ $name }}" class="cs-dash-input" readonly>
                                    @else
                                        <input type="text" name="name" id="pkg_user_name" value="{{ $name }}" class="cs-dash-input" placeholder="{{ __('Enter Your Name') }}">
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="cs-dash-label">{{ __('Your Email') }}</label>
                                    @if(auth()->check())
                                        <input type="email" name="email" value="{{ $email }}" class="cs-dash-input" readonly>
                                    @else
                                        <input type="email" name="email" id="pkg_user_email" value="{{ $email }}" class="cs-dash-input" placeholder="{{ __('Enter Your Email') }}">
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="cs-dash-label">{{ __('Package Name') }}</label>
                                    <div class="cs-display-field">{{ $order_details->package_name }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="cs-dash-label">{{ __('Package Price') }}</label>
                                    <div class="cs-display-field cs-display-field-price">
                                        {{ amount_with_currency_symbol($final_price) }}
                                        @if (!check_currency_support_by_payment_gateway($payment_gateway))
                                            <small class="cs-display-field-note">
                                                {{ __('You will charge in ' . get_charge_currency($payment_gateway) . ', you have to pay') }}
                                                <strong>{{ get_charge_amount($order_details->package_price, $payment_gateway) . get_charge_currency($payment_gateway) }}</strong>
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="cs-dash-label">{{ __('Payment Gateway') }}</label>
                                    <div class="cs-display-field cs-field-cap">
                                        @if ($payment_gateway == 'manual_payment')
                                            {{ get_static_option('site_manual_payment_name') }}
                                        @else
                                            {{ $payment_gateway }}
                                        @endif
                                    </div>
                                </div>
                                @if ($payment_gateway == 'manual_payment')
                                <div class="col-md-6">
                                    <label class="cs-dash-label">{{ __('Transaction ID') }}</label>
                                    <input type="text" name="trasaction_id" class="cs-dash-input" placeholder="{{ __('Transaction ID') }}">
                                    <small class="cs-display-field-hint">{!! get_manual_payment_description() !!}</small>
                                </div>
                                @endif
                                <div class="col-12">
                                    <button class="cs-dash-submit-btn" id="pay_now" type="submit">
                                        <i class="las la-credit-card"></i> {{ __('Pay Now') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).ready(function () {
        var name  = sessionStorage.pkg_user_name;
        var email = sessionStorage.pkg_user_email;
        $('#pkg_user_name').val(name);
        $('#pkg_user_email').val(email);
        $(document).on('click', '#pay_now', function () {
            sessionStorage.removeItem('pkg_user_name');
            sessionStorage.removeItem('pkg_user_email');
        });
    });
})(jQuery);
</script>
@endsection
