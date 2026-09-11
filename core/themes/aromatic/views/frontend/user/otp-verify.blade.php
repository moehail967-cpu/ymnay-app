@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('Verify OTP') }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span>{{ __('OTP Verify') }}</span>
        </div>
    </div>
</div>

<div class="container ar-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="ar-auth-card">
                <div class="ar-auth-centered">
                    <i class="mdi mdi-shield-check-outline" style="font-size:48px;color:var(--ar-red);"></i>
                    <h2 class="ar-auth-title" style="margin-top:12px;">{{ __('Enter OTP Code') }}</h2>
                    <p class="ar-auth-sub">{{ __('We sent a verification code to your phone.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route(route_prefix().'user.login.otp.verify') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="ar-auth-label">{{ __('OTP Code') }} <span class="ar-required">*</span></label>
                        <input type="number" name="otp_code" class="ar-auth-input" placeholder="{{ __('Enter 6-digit code') }}" required
                               style="letter-spacing:4px;font-size:20px;text-align:center;">
                    </div>

                    @if(isset($userOtp))
                    <div class="text-center mb-3" id="ar-otp-countdown" style="font-size:13px;color:var(--ar-muted);">
                        {{ __('Code expires in') }} <strong id="ar-otp-timer">--:--</strong>
                    </div>
                    @endif

                    <button type="submit" class="ar-btn ar-btn-red w-100 justify-content-center">
                        <i class="mdi mdi-check-bold"></i> {{ __('Verify') }}
                    </button>
                </form>

                <p class="ar-auth-switch">
                    {{ __("Didn't receive the code?") }}
                    <a href="{{ route(route_prefix().'user.login.otp.resend') }}">{{ __('Resend') }}</a>
                </p>
                <p class="ar-auth-switch">
                    <a href="{{ route('tenant.user.login.otp') }}"><i class="mdi mdi-arrow-left"></i> {{ __('Change Number') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($userOtp) && $userOtp->expire_time)
<script>
(function () {
    var expireTime = new Date('{{ $userOtp->expire_time }}').getTime();
    var timerEl = document.getElementById('ar-otp-timer');

    var interval = setInterval(function () {
        var now  = new Date().getTime();
        var diff = Math.max(0, expireTime - now);
        var mins = Math.floor(diff / 60000);
        var secs = Math.floor((diff % 60000) / 1000);
        timerEl.textContent = (mins < 10 ? '0' + mins : mins) + ':' + (secs < 10 ? '0' + secs : secs);
        if (diff <= 0) {
            clearInterval(interval);
            timerEl.textContent = '00:00';
            timerEl.style.color = 'var(--ar-red)';
        }
    }, 1000);
})();
</script>
@endif
@endsection
