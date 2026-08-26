@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div class="hf-auth-hero">
    <div class="container">
        <h2>{{ __('Verify OTP') }}</h2>
        <div class="hf-dash-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="las la-angle-right" style="font-size:11px;"></i>
            <span>{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="hf-auth-card">
                <div style="text-align:center;margin-bottom:28px;">
                    <div class="hf-auth-icon">
                        <i class="las la-shield-alt" style="font-size:32px;color:#E8603C;"></i>
                    </div>
                    <h2 style="font-size:22px;font-weight:700;color:#1a1a1a;margin-bottom:8px;">{{ __('Enter OTP') }}</h2>
                    <p style="font-size:13px;color:#888;">
                        {{ __('We sent a code to your phone. It expires in') }}
                        <strong id="hf-otp-timer" style="color:#E8603C;">05:00</strong>.
                    </p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_otp_verify_url() }}" method="post">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label class="hf-form-label">{{ __('OTP Code') }} <span class="hf-form-required">*</span></label>
                        <input type="text" name="otp" class="hf-form-input" placeholder="{{ __('Enter 6-digit code') }}"
                               maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                               style="font-size:22px;font-weight:800;text-align:center;letter-spacing:.3em;">
                    </div>
                    <button type="submit" id="otp_verify_btn" class="hf-btn hf-btn-primary hf-btn-block" style="padding:13px;font-size:15px;">
                        <i class="las la-check-circle"></i> {{ __('Verify & Sign In') }}
                    </button>
                </form>

                <div style="text-align:center;margin-top:16px;">
                    <button id="hf-otp-resend" class="hf-btn hf-btn-ghost hf-btn-sm" disabled>
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
(function($){
    'use strict';
    @php
        $expireTime = isset($userOtp) ? \Carbon\Carbon::now()->diffInSeconds($userOtp->expire_time, false) : 300;
        $expireTime = max(0, $expireTime);
    @endphp
    var seconds = {{ $expireTime }};
    var timer = setInterval(function(){
        seconds--;
        var m = Math.floor(seconds / 60), s = seconds % 60;
        $('#hf-otp-timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
        if(seconds <= 0){ clearInterval(timer); $('#hf-otp-resend').prop('disabled', false); }
    }, 1000);

    $('#hf-otp-resend').on('click', function(){
        var btn = $(this);
        btn.prop('disabled', true).text('{{ __("Sending…") }}');
        $.ajax({
            url: '{{ theme_otp_resend_url() }}', type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(data){
                toastr.success(data.msg ?? '{{ __("OTP sent") }}');
                seconds = 300; clearInterval(timer);
                timer = setInterval(function(){
                    seconds--;
                    var m = Math.floor(seconds / 60), s = seconds % 60;
                    $('#hf-otp-timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
                    if(seconds <= 0){ clearInterval(timer); btn.prop('disabled', false).html('<i class="las la-redo"></i> {{ __("Resend OTP") }}'); }
                }, 1000);
                btn.prop('disabled', true).html('<i class="las la-redo"></i> {{ __("Resend OTP") }}');
            },
            error: function(){ btn.prop('disabled', false).html('<i class="las la-redo"></i> {{ __("Resend OTP") }}'); }
        });
    });
})(jQuery);
</script>
@endsection
