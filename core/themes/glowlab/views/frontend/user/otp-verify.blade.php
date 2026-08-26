@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ __('Verify OTP') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ __('OTP Verification') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:44px;box-shadow:var(--gl-shadow);">

                <div style="text-align:center;margin-bottom:24px;">
                    <span style="display:inline-flex;width:56px;height:56px;background:var(--gl-gold-pale);border-radius:50%;align-items:center;justify-content:center;color:var(--gl-gold);font-size:24px;margin-bottom:16px;">
                        <i class="mdi mdi-shield-check-outline"></i>
                    </span>
                    <h2 style="font-size:22px;font-weight:300;color:var(--gl-dark);margin:0 0 8px;letter-spacing:-.3px;">{{ __('Enter OTP Code') }}</h2>
                    <p style="font-size:13px;color:var(--gl-muted);margin:0;line-height:1.6;">{{ __('An OTP has been sent to your phone number.') }}</p>
                </div>

                <div class="gl-otp-countdown" style="text-align:center;font-size:16px;font-weight:600;color:var(--gl-gold);margin-bottom:16px;min-height:24px;"></div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_otp_verify_url() }}" method="post" id="login_form_order_page">
                    @csrf
                    <div class="error-wrap mb-3"></div>

                    <div class="mb-3">
                        <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('OTP Code') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input type="number" name="otp" value="{{ old('otp') }}"
                               placeholder="{{ __('Enter 6-digit OTP') }}"
                               style="width:100%;padding:14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:22px;font-weight:700;letter-spacing:8px;text-align:center;font-family:inherit;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                    </div>

                    <div class="mb-4">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;color:var(--gl-dark);">
                            <input type="checkbox" name="remember" style="accent-color:var(--gl-gold);width:14px;height:14px;"> {{ __('Remember Me') }}
                        </label>
                    </div>

                    <button type="submit" id="login_btn"
                            style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                        <i class="mdi mdi-check-circle-outline"></i> {{ __('Verify OTP') }}
                    </button>
                </form>

                <div style="display:flex;justify-content:space-between;margin-top:16px;font-size:13px;">
                    <a href="{{ route('tenant.user.login.otp') }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Update number?') }}</a>
                    <a href="{{ theme_otp_resend_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Resend OTP?') }}</a>
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
    let cd = $('.gl-otp-countdown');
    if (parseInt(expire_time) === 0) {
        cd.css('color','#e53e3e').text('{{ __("The OTP is expired") }}');
        return clearInterval(interval);
    }
    cd.css('color','var(--gl-gold)').text(expire_time + ' {{ __("Seconds") }}');
}, 1000);
</script>
@endsection
