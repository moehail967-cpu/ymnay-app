@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Account') }}</div>
        <h2 style="font-size:28px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('Verify OTP') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--vl-muted);letter-spacing:1px;text-transform:uppercase;">
            <a href="{{ theme_home_url() }}" style="color:var(--vl-champagne);">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:80px 0;">
    <div class="mx-auto" style="max-width:480px;">

        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="border:1px solid var(--vl-border);padding:48px 40px;">
            <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Verification') }}</div>
            <h3 style="font-size:22px;font-weight:400;color:var(--vl-ivory);margin:0 0 10px;font-family:'Cormorant Garamond',serif;letter-spacing:1px;">{{ __('Enter Your OTP') }}</h3>
            <p style="font-size:13px;color:var(--vl-muted);margin-bottom:8px;line-height:1.7;">{{ __('An OTP has been sent to your phone number.') }}</p>

            <div class="countdown" style="font-size:13px;color:var(--vl-champagne);margin-bottom:28px;font-family:'Inter',sans-serif;letter-spacing:1px;"></div>

            <form action="{{ theme_otp_verify_url() }}" method="POST" class="account-form">
                @csrf
                <div class="error-wrap" style="margin-bottom:12px;"></div>

                <div style="margin-bottom:20px;">
                    <label style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:10px;">
                        {{ __('OTP Code') }} <x-fields.mandatory-indicator/>
                    </label>
                    <input type="number" name="otp" value="{{ old('otp') }}"
                           style="width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:20px;letter-spacing:8px;font-family:'Inter',sans-serif;outline:none;text-align:center;">
                </div>

                <div style="margin-bottom:28px;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:12px;cursor:pointer;color:var(--vl-muted);">
                        <input type="checkbox" name="remember" style="accent-color:var(--vl-champagne);"> {{ __('Remember me') }}
                    </label>
                </div>

                <button type="submit" id="login_btn"
                        style="width:100%;background:var(--vl-champagne);color:var(--vl-dark);border:0;padding:16px;font-size:10px;letter-spacing:4px;text-transform:uppercase;cursor:pointer;font-family:inherit;">
                    {{ __('Verify OTP') }}
                </button>
            </form>

            <div style="margin-top:24px;display:flex;justify-content:space-between;font-size:12px;">
                <a href="{{ route('tenant.user.login.otp') }}" style="color:var(--vl-champagne);text-decoration:none;letter-spacing:1px;">{{ __('Update number?') }}</a>
                <a href="{{ theme_otp_resend_url() }}" style="color:var(--vl-champagne);text-decoration:none;letter-spacing:1px;">{{ __('Resend OTP?') }}</a>
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
(function () {
    var expire_time = {{ $expire_time }};
    var countdown = document.querySelector('.countdown');
    var interval = setInterval(function () {
        if (expire_time > 0) { expire_time--; }
        if (parseInt(expire_time) === 0) {
            countdown.style.color = 'var(--vl-muted)';
            countdown.textContent = '{{ __('The OTP has expired') }}';
            return clearInterval(interval);
        }
        countdown.textContent = expire_time + ' {{ __('seconds remaining') }}';
    }, 1000);
})();
</script>
@endsection
