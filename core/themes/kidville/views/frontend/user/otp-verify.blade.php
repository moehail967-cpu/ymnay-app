@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection

@section('content')

<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('Verify OTP') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('OTP Verification') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="kv-auth-card">
                <div class="kv-auth-header">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--kv-light);border:3px solid var(--kv-border);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--kv-green);">
                        <i class="las la-shield-alt"></i>
                    </div>
                    <h2 class="kv-auth-title">{{ __('Enter OTP Code') }}</h2>
                    <p class="kv-auth-sub">{{ __('An OTP has been sent to your phone number.') }}</p>
                </div>

                <div class="kv-otp-countdown" style="text-align:center;font-size:18px;font-weight:800;color:var(--kv-red);margin-bottom:16px;min-height:28px;"></div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_otp_verify_url() }}" method="post" id="login_form_order_page">
                    @csrf
                    <div class="error-wrap mb-3"></div>

                    <div class="kv-form-group">
                        <label class="kv-label">{{ __('OTP Code') }} <span class="kv-required">*</span></label>
                        <input type="number" name="otp" value="{{ old('otp') }}"
                               class="kv-input"
                               placeholder="{{ __('Enter 6-digit OTP') }}"
                               style="font-size:22px;font-weight:800;letter-spacing:8px;text-align:center;">
                    </div>

                    <div class="kv-form-group">
                        <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;color:var(--kv-dark);font-weight:600;">
                            <input type="checkbox" name="remember" style="accent-color:var(--kv-red);width:16px;height:16px;"> {{ __('Remember Me') }}
                        </label>
                    </div>

                    <button type="submit" id="login_btn" class="kv-btn kv-btn-red" style="width:100%;justify-content:center;padding:13px;font-size:15px;">
                        <i class="las la-check-circle"></i> {{ __('Verify OTP') }}
                    </button>
                </form>

                <div style="display:flex;justify-content:space-between;margin-top:16px;font-size:13px;">
                    <a href="{{ route('tenant.user.login.otp') }}" style="color:var(--kv-blue);font-weight:700;text-decoration:none;">{{ __('Update number?') }}</a>
                    <a href="{{ theme_otp_resend_url() }}" style="color:var(--kv-red);font-weight:700;text-decoration:none;">{{ __('Resend OTP?') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@php
    $expire_time = 0;
    if (!now()->isAfter($userOtp->expire_date ?? now())) {
        $expire_time = $userOtp ? now()->diffInRealSeconds($userOtp->expire_date) : 0;
    }
@endphp
<script>
let expire_time = {{ $expire_time }};
let interval = setInterval(function () {
    if (expire_time > 0) expire_time--;
    let cd = $('.kv-otp-countdown');
    if (parseInt(expire_time) === 0) {
        cd.css('color','var(--kv-red)').text('{{ __("The OTP is expired") }}');
        return clearInterval(interval);
    }
    cd.text(expire_time + ' {{ __("Seconds") }}');
}, 1000);
</script>
@endsection
