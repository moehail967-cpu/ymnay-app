@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')

{{-- Page Banner --}}
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:44px 0 32px;text-align:center;">
    <div class="container">
        <div style="font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('Account') }}</div>
        <h1 style="font-size:clamp(22px,4vw,36px);font-weight:300;color:var(--ms-dark);margin:0 auto 12px;line-height:1.2;">{{ __('Verify OTP') }}</h1>
        <div style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;"
               onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-linen-d)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<section style="background:var(--ms-cream);padding:64px 0 80px;">
    <div class="container">
        <div style="max-width:440px;margin:0 auto;">

            <x-flash-msg/>
            <x-error-msg/>

            <div class="ms-auth-card">
                {{-- Icon --}}
                <div style="text-align:center;margin-bottom:24px;">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--ms-warm);border:1px solid var(--ms-border);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="mdi mdi-shield-check-outline" style="font-size:28px;color:var(--ms-olive);"></i>
                    </div>
                    <h2 style="font-size:20px;font-weight:400;color:var(--ms-dark);margin-bottom:8px;">{{ __('Enter Your OTP') }}</h2>
                    <p style="font-size:13px;color:var(--ms-muted);line-height:1.6;">
                        {{ __('We\'ve sent a 6-digit code to your phone. Enter it below to sign in.') }}
                    </p>
                </div>

                <div class="ms-auth-divider"></div>

                <form action="{{ theme_otp_verify_url() }}" method="POST" style="margin-top:24px;">
                    @csrf
                    <div class="ms-form-group">
                        <label class="ms-form-label">{{ __('6-Digit OTP Code') }}</label>
                        <input type="text" name="otp" class="ms-form-input"
                               placeholder="{{ __('000000') }}"
                               maxlength="6" inputmode="numeric" pattern="[0-9]*"
                               style="text-align:center;font-size:22px;letter-spacing:.3em;font-weight:600;"
                               required>
                    </div>

                    {{-- Countdown --}}
                    <div style="text-align:center;margin:12px 0;font-size:13px;color:var(--ms-muted);">
                        <span id="ms-otp-timer-wrap">
                            {{ __('Resend available in') }}
                            <strong id="ms-otp-countdown" style="color:var(--ms-linen-d);">60</strong>s
                        </span>
                        <button type="button" id="ms-resend-otp"
                                style="display:none;background:transparent;border:0;font-size:13px;font-weight:600;color:var(--ms-linen-d);cursor:pointer;padding:0;transition:color .2s;"
                                onmouseover="this.style.color='var(--ms-olive)'"
                                onmouseout="this.style.color='var(--ms-linen-d)'">
                            <i class="mdi mdi-refresh" style="margin-right:4px;"></i> {{ __('Resend OTP') }}
                        </button>
                    </div>

                    <button type="submit" class="ms-btn-full ms-btn-dark" style="margin-top:12px;">
                        <i class="mdi mdi-check-circle-outline" style="margin-right:8px;font-size:16px;"></i>
                        {{ __('Verify & Sign In') }}
                    </button>
                </form>

                <div style="text-align:center;margin-top:20px;">
                    <a href="{{ route('tenant.user.login') }}"
                       style="font-size:13px;color:var(--ms-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color .2s;"
                       onmouseover="this.style.color='var(--ms-linen-d)'"
                       onmouseout="this.style.color='var(--ms-muted)'">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Sign In') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection

@section('scripts')
<script>
(function($){
    'use strict';
    var seconds = 60;
    var timer = setInterval(function(){
        seconds--;
        $('#ms-otp-countdown').text(seconds);
        if(seconds <= 0){
            clearInterval(timer);
            $('#ms-otp-timer-wrap').hide();
            $('#ms-resend-otp').show();
        }
    }, 1000);

    $('#ms-resend-otp').on('click', function(){
        var btn = $(this);
        btn.prop('disabled', true).text('{{ __("Sending…") }}');
        $.ajax({
            url: '{{ theme_otp_resend_url() }}',
            type: 'GET',
            success: function(data){
                toastr.success(data.msg || '{{ __("OTP resent successfully") }}');
                btn.hide();
                seconds = 60;
                $('#ms-otp-countdown').text(seconds);
                $('#ms-otp-timer-wrap').show();
                timer = setInterval(function(){
                    seconds--;
                    $('#ms-otp-countdown').text(seconds);
                    if(seconds <= 0){ clearInterval(timer); $('#ms-otp-timer-wrap').hide(); btn.prop('disabled', false).text('{{ __("Resend OTP") }}').show(); }
                }, 1000);
            },
            error: function(){ toastr.error('{{ __("Failed to resend OTP. Please try again.") }}'); btn.prop('disabled', false); }
        });
    });
})(jQuery);
</script>
@endsection
