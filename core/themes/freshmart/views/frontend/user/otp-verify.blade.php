@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ __('Verify OTP') }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ __('OTP Verification') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="fm-auth-card">
                <div class="fm-auth-header">
                    {!! theme_logo_html('fm-auth-logo-link', 'fm-auth-logo') !!}
                    <h2 class="fm-auth-title mt-3">{{ __('Enter OTP Code') }}</h2>
                    <p class="fm-auth-sub">{{ __('An OTP has been sent to your phone number.') }}</p>
                </div>

                <div class="countdown" style="text-align:center;font-size:16px;font-weight:700;color:var(--fm-green);margin-bottom:16px;min-height:24px;"></div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_otp_verify_url() }}" method="post"
                      enctype="multipart/form-data" class="fm-auth-form" id="login_form_order_page">
                    @csrf
                    <div class="error-wrap mb-3"></div>

                    <div class="fm-form-group">
                        <label class="fm-label">{{ __('OTP Code') }} <span class="fm-required">*</span></label>
                        <input class="fm-input" type="number" name="otp" value="{{ old('otp') }}"
                               placeholder="{{ __('Enter 6-digit OTP') }}" style="letter-spacing:6px;font-size:18px;font-weight:700;text-align:center;">
                    </div>

                    <div class="fm-form-group">
                        <label class="d-flex align-items-center gap-2" style="font-size:13px;cursor:pointer;">
                            <input type="checkbox" name="remember" style="accent-color:var(--fm-green);"> {{ __('Remember Me') }}
                        </label>
                    </div>

                    <button type="submit" id="login_btn" class="fm-btn fm-btn-green w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="las la-check-circle"></i> {{ __('Verify OTP') }}
                    </button>
                </form>

                <div class="d-flex justify-content-between mt-3" style="font-size:13px;">
                    <a href="{{ route('tenant.user.login.otp') }}" style="color:var(--fm-green);">{{ __('Update number?') }}</a>
                    <a href="{{ theme_otp_resend_url() }}" style="color:var(--fm-green);">{{ __('Resend OTP?') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@php
    $expire_time = 0;
    if (!now()->isAfter($userOtp->expire_date)) {
        $expire_time = $userOtp ? now()->diffInRealSeconds($userOtp->expire_date) : 0;
    }
@endphp
<script>
let expire_time = {{ $expire_time }};
let interval = setInterval(function () {
    if (expire_time > 0) expire_time--;
    let cd = $('.countdown');
    if (parseInt(expire_time) === 0) {
        cd.css('color','#e74c3c').text('{{ __("The OTP is expired") }}');
        return clearInterval(interval);
    }
    cd.css('color','var(--fm-green)').text(expire_time + ' {{ __("Seconds") }}');
}, 1000);
</script>
@endsection
