@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Verify OTP') }} @endsection

@section('content')
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="lg-auth-card">
                <div class="lg-auth-header">
                    <i class="las la-lock" style="font-size:40px;color:var(--lx-gold);display:block;margin-bottom:16px;"></i>
                    <h2 class="lg-auth-title">{{ __('Enter OTP') }}</h2>
                    <p class="lg-auth-sub">{{ __('Enter the 6-digit code sent to your phone.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="lg-auth-form" id="verify_form">
                    @csrf
                    <div class="lg-form-group">
                        <label class="lg-form-label">{{ __('OTP Code') }} <span class="lg-required">*</span></label>
                        <input type="text" name="otp" id="otp_input"
                               class="lg-form-control" placeholder="{{ __('6-digit code') }}"
                               maxlength="6" style="text-align:center;font-size:20px;letter-spacing:8px;">
                    </div>

                    <div style="text-align:center;margin-bottom:16px;font-size:13px;color:var(--lx-muted);">
                        {{ __('Code expires in') }}
                        <span id="lg-otp-countdown" style="color:var(--lx-gold);font-weight:600;">05:00</span>
                    </div>

                    <button type="submit" id="verify_btn" class="lx-btn lx-btn-primary w-100 justify-content-center" style="padding:13px;">
                        {{ __('Verify & Sign In') }}
                    </button>
                </form>

                <p class="lg-auth-switch">
                    <a href="{{ route('tenant.user.login.otp') }}" style="color:var(--lx-gold);">
                        <i class="las la-sync"></i> {{ __('Resend OTP') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function($){
    {!! theme_btn_loading_js('verify_btn', __('Verifying…')) !!}

    var secs = 300;
    var timer = setInterval(function(){
        secs--;
        var m = Math.floor(secs/60), s = secs%60;
        $('#lg-otp-countdown').text((m<10?'0':'')+m+':'+(s<10?'0':'')+s);
        if(secs <= 0){
            clearInterval(timer);
            $('#lg-otp-countdown').text('{{ __("Expired") }}').css('color','#f44336');
        }
    }, 1000);
})(jQuery);
</script>
@endsection
