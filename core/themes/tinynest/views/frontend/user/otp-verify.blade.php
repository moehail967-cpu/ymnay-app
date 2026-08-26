@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ __('Verify OTP') }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ route('tenant.user.login') }}">{{ __('Login') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<div class="container tn-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="tn-auth-card">
                <div class="tn-auth-icon"><i class="las la-shield-alt"></i></div>
                <h2 class="tn-auth-title">{{ __('Enter OTP') }}</h2>
                <p class="tn-auth-sub">
                    {{ __('We sent a code to your phone. It expires in') }}
                    <strong id="tn-otp-timer" class="tn-otp-timer-text">05:00</strong>.
                </p>

                {!! theme_flash_msg() !!}
                {!! theme_error_msg() !!}

                <form action="{{ theme_otp_verify_url() }}" method="POST">
                    {!! theme_csrf_field() !!}
                    <div class="mb-4">
                        <label class="tn-label">{{ __('OTP Code') }} *</label>
                        <input type="text" name="otp" class="tn-input tn-otp-input"
                               placeholder="{{ __('Enter 6-digit code') }}"
                               maxlength="6" inputmode="numeric" autocomplete="one-time-code" required autofocus>
                    </div>
                    <button type="submit" id="otp_verify_btn" class="tn-btn tn-btn-primary w-100">
                        <i class="las la-check-circle"></i> {{ __('Verify & Sign In') }}
                    </button>
                </form>

                <div class="text-center mt-3">
                    <button id="tn-otp-resend" class="tn-btn tn-btn-outline tn-btn-sm" disabled>
                        <i class="las la-redo"></i> {{ __('Resend OTP') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_btn_loading_js('otp_verify_btn', __('Verifying…')) !!}
<script>
(function ($) {
    'use strict';
    @php
        $expireTime = isset($userOtp) ? \Carbon\Carbon::now()->diffInSeconds($userOtp->expire_time, false) : 300;
        $expireTime = max(0, $expireTime);
    @endphp
    var seconds = {{ $expireTime }};

    function tick() {
        var m = Math.floor(seconds / 60), s = seconds % 60;
        $('#tn-otp-timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
        if (seconds <= 0) { clearInterval(timer); $('#tn-otp-resend').prop('disabled', false); }
        seconds--;
    }
    tick();
    var timer = setInterval(tick, 1000);

    $('#tn-otp-resend').on('click', function () {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> {{ __("Sending…") }}');
        $.ajax({
            url: '{{ theme_otp_resend_url() }}',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function (data) {
                toastr.success(data.msg ?? '{{ __("OTP sent") }}');
                seconds = 300;
                clearInterval(timer);
                tick();
                timer = setInterval(tick, 1000);
                btn.prop('disabled', true).html('<i class="las la-redo"></i> {{ __("Resend OTP") }}');
            },
            error: function () {
                btn.prop('disabled', false).html('<i class="las la-redo"></i> {{ __("Resend OTP") }}');
            }
        });
    });
})(jQuery);
</script>
@endsection
