@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div class="ph-page-banner">
    <div class="container">
        <h1>{{ __('Verify OTP') }}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="ph-auth-card">
                <div class="ph-auth-header">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--ph-terra-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border:2px solid var(--ph-border);">
                        <i class="las la-shield-alt" style="font-size:32px;color:var(--ph-terra);"></i>
                    </div>
                    <h2 class="ph-auth-title">{{ __('Enter OTP') }}</h2>
                    <p class="ph-auth-sub">{{ __('We sent a code to your phone. It expires in') }} <strong id="ph-otp-timer">05:00</strong>.</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_otp_verify_url() }}" method="post" class="ph-auth-form">
                    @csrf
                    <div class="ph-form-group">
                        <label class="ph-label">{{ __('OTP Code') }} <span class="ph-required">*</span></label>
                        <input type="text" name="otp" class="ph-input" placeholder="{{ __('Enter 6-digit code') }}"
                               maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                               style="letter-spacing:.3em;font-size:22px;font-weight:800;text-align:center;">
                    </div>
                    <button type="submit" id="otp_verify_btn" class="ph-btn ph-btn-terra w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="las la-check-circle"></i> {{ __('Verify & Sign In') }}
                    </button>
                </form>

                <div style="text-align:center;margin-top:16px;">
                    <button id="ph-otp-resend" class="ph-btn ph-btn-ghost ph-btn-sm" disabled>
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
    var seconds = 300;
    var timer = setInterval(function(){
        seconds--;
        var m = Math.floor(seconds / 60), s = seconds % 60;
        $('#ph-otp-timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
        if(seconds <= 0){ clearInterval(timer); $('#ph-otp-resend').prop('disabled', false); }
    }, 1000);

    $('#ph-otp-resend').on('click', function(){
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
                    $('#ph-otp-timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
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
