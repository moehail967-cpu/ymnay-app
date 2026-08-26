@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Order Confirm') }} @endsection
@section('content')
<section class="hf-pay-section">
    <div class="container">
        <div class="hf-order-confirm">
            <div class="hf-dash-card-title"><i class="las la-receipt"></i> {{ __('Order Details') }}</div>
            {!! theme_flash_msg() !!}
            {!! theme_error_msg() !!}
            <form action="{{ theme_payment_form_url() }}" method="post" enctype="multipart/form-data" class="mt-4">
                @csrf
                @php
                    $custom_fields = unserialize($order_details->custom_fields);
                    $payment_gateway = !empty($custom_fields['selected_payment_gateway']) ? $custom_fields['selected_payment_gateway'] : '';
                    $name = auth()->guard('web')->check() ? auth()->guard('web')->user()->name : '';
                    $email = auth()->guard('web')->check() ? auth()->user()->guard('web')->email : '';
                @endphp
                <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                <input type="hidden" name="payment_gateway" value="{{ $payment_gateway }}">

                <div class="hf-confirm-table-wrap">
                    <table class="hf-dash-table">
                        <tr>
                            <td style="width:160px;font-weight:600;">{{ __('Your Name') }}</td>
                            <td>
                                @if(auth()->check())
                                    <input type="text" name="name" value="{{ $name }}" class="hf-form-input" placeholder="{{ __('Enter Your Name') }}" readonly>
                                @else
                                    <input type="text" name="name" id="pkg_user_name" value="{{ $name }}" class="hf-form-input" placeholder="{{ __('Enter Your Name') }}">
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;">{{ __('Your Email') }}</td>
                            <td>
                                @if(auth()->check())
                                    <input type="email" name="email" value="{{ $email }}" class="hf-form-input" placeholder="{{ __('Enter Your Email') }}" readonly>
                                @else
                                    <input type="email" name="email" id="pkg_user_email" value="{{ $email }}" class="hf-form-input" placeholder="{{ __('Enter Your Email') }}">
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;">{{ __('Package Name') }}</td>
                            <td>{{ $order_details->package_name }}</td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;">{{ __('Package Price') }}</td>
                            <td>
                                <strong>{{ amount_with_currency_symbol($final_price) }}</strong>
                                @if (!check_currency_support_by_payment_gateway($payment_gateway))
                                    <br><small>{{ __('You will charge in ' . get_charge_currency($payment_gateway) . ', you have to pay' . ' ') }}
                                    <strong>{{ get_charge_amount($order_details->package_price, $payment_gateway) . get_charge_currency($payment_gateway) }}</strong></small>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:600;">{{ __('Payment Gateway') }}</td>
                            <td class="text-capitalize">
                                @if ($payment_gateway == 'manual_payment')
                                    {{ get_static_option('site_manual_payment_name') }}
                                @else
                                    {{ $payment_gateway }}
                                @endif
                            </td>
                        </tr>
                        @if ($payment_gateway == 'manual_payment')
                            <tr>
                                <td style="font-weight:600;">{{ __('Transaction ID') }}</td>
                                <td>
                                    <input type="text" name="trasaction_id" class="hf-form-input">
                                    <small class="d-block mt-1" style="color:#888;">{!! get_manual_payment_description() !!}</small>
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>
                <div class="mt-4">
                    <button class="hf-btn hf-btn-primary hf-btn-block" id="pay_now" type="submit">{{ __('Pay Now') }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
@section('scripts')
    <script>
        (function($){
        "use strict";
        $(document).ready(function(){
            var name = sessionStorage.pkg_user_name;
            var email = sessionStorage.pkg_user_email;
            $("#pkg_user_name").val(name);
            $("#pkg_user_email").val(email);
            $(document).on('click',"#pay_now",function(){
                sessionStorage.removeItem("pkg_user_name");
                sessionStorage.removeItem("pkg_user_email");
            });
        });
        })(jQuery);
    </script>
@endsection
