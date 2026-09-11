@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Order Confirm') }} @endsection
@section('page-title') {{ __('Order Confirm') }} @endsection
@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('Confirm Your Order') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ __('Order Confirm') }}</span>
        </div>
    </div>
</div>
<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:10px;overflow:hidden;">
                <div style="background:#f0faf6;padding:20px 28px;border-bottom:1px solid #d0e8e0;">
                    <h4 style="margin:0;font-weight:700;color:#1a1a1a;">{{ __('Order Details') }}</h4>
                </div>
                <div style="padding:28px;">
                    {!! theme_flash_msg() !!}
                    {!! theme_error_msg() !!}
                    <form action="{{ theme_payment_form_url() }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @php
                            $custom_fields = unserialize($order_details->custom_fields);
                            $payment_gateway = !empty($custom_fields['selected_payment_gateway']) ? $custom_fields['selected_payment_gateway'] : '';
                            $name = auth()->guard('web')->check() ? auth()->guard('web')->user()->name : '';
                        @endphp
                        <input type="hidden" name="order_id" value="{{ $order_details->id }}">
                        <input type="hidden" name="payment_gateway" value="{{ $payment_gateway }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="bp-label">{{ __('Your Name') }}</label>
                                @if(auth()->check())
                                    <input type="text" name="name" value="{{ $name }}" class="bp-input" readonly>
                                @else
                                    <input type="text" name="name" id="pkg_user_name" value="{{ $name }}" class="bp-input" placeholder="{{ __('Enter Your Name') }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="bp-label">{{ __('Package Name') }}</label>
                                <input type="text" class="bp-input" value="{{ $order_details->package_name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="bp-label">{{ __('Package Price') }}</label>
                                <input type="text" class="bp-input" value="{{ amount_with_currency_symbol($final_price) }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="bp-label">{{ __('Payment Gateway') }}</label>
                                <input type="text" class="bp-input" value="{{ $payment_gateway == 'manual_payment' ? get_static_option('site_manual_payment_name') : ucfirst($payment_gateway) }}" readonly>
                            </div>
                            @if($payment_gateway == 'manual_payment')
                            <div class="col-12">
                                <label class="bp-label">{{ __('Transaction ID') }}</label>
                                <input type="text" name="trasaction_id" class="bp-input" placeholder="{{ __('Enter Transaction ID') }}">
                                <small class="d-block mt-1" style="color:#888;">{!! get_manual_payment_description() !!}</small>
                            </div>
                            @endif
                        </div>
                        <div class="mt-4">
                            <button class="bp-btn bp-btn-green w-100 justify-content-center" id="pay_now" type="submit" style="padding:14px;font-size:15px;">
                                <i class="las la-lock"></i> {{ __('Pay Now') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
(function($){
    $(document).ready(function(){
        var name = sessionStorage.pkg_user_name;
        var email = sessionStorage.pkg_user_email;
        if(name) $('#pkg_user_name').val(name);
        $(document).on('click','#pay_now',function(){
            sessionStorage.removeItem('pkg_user_name');
            sessionStorage.removeItem('pkg_user_email');
        });
    });
})(jQuery);
</script>
@endsection
