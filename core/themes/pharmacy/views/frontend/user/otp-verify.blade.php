@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div style="background:var(--pf-teal-light);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('Verify OTP') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--pf-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div style="background:#fff;border:1px solid var(--pf-border);border-radius:var(--pf-radius-xl);padding:40px 36px;box-shadow:var(--pf-shadow);">
                <div style="text-align:center;margin-bottom:28px;">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--pf-teal-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="las la-shield-alt" style="font-size:32px;color:var(--pf-teal);"></i>
                    </div>
                    <h2 style="font-size:22px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('Enter OTP') }}</h2>
                    <p style="font-size:13px;color:var(--pf-muted);">
                        {{ __('We sent a code to your phone. It expires in') }}
                        <strong id="pf-otp-timer" style="color:var(--pf-teal);">05:00</strong>.
                    </p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_otp_verify_url() }}" method="post">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">
                            {{ __('OTP Code') }} <span style="color:var(--pf-teal);">*</span>
                        </label>
                        <input type="text" name="otp" placeholder="{{ __('Enter 6-digit code') }}"
                               maxlength="6" inputmode="numeric" autocomplete="one-time-code"
                               style="width:100%;padding:14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:22px;font-weight:800;text-align:center;letter-spacing:.3em;font-family:var(--pf-font);outline:none;"
                               onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
                    </div>
                    <button type="submit" id="otp_verify_btn" class="pf-btn pf-btn-teal w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="las la-check-circle"></i> {{ __('Verify & Sign In') }}
                    </button>
                </form>

                <div style="text-align:center;margin-top:16px;">
                    <button id="pf-otp-resend" class="pf-btn pf-btn-ghost pf-btn-sm" disabled>
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
        $('#pf-otp-timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
        if(seconds <= 0){ clearInterval(timer); $('#pf-otp-resend').prop('disabled', false); }
    }, 1000);

    $('#pf-otp-resend').on('click', function(){
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
                    $('#pf-otp-timer').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
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
