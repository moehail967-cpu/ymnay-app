@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Login with OTP') }} @endsection

@section('content')
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ __('OTP Login') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="lg-auth-card">
                <div class="lg-auth-header">
                    <i class="las la-mobile-alt" style="font-size:40px;color:var(--lx-gold);display:block;margin-bottom:16px;"></i>
                    <h2 class="lg-auth-title">{{ __('Login with OTP') }}</h2>
                    <p class="lg-auth-sub">{{ __('Enter your phone number to receive a one-time passcode.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="lg-auth-form" id="otp_form">
                    @csrf
                    <div class="lg-form-group">
                        <label class="lg-form-label">{{ __('Phone Number') }} <span class="lg-required">*</span></label>
                        <input type="tel" name="phone" id="phone_number"
                               class="lg-form-control" placeholder="{{ __('Enter phone number') }}">
                    </div>
                    <button type="submit" id="otp_btn" class="lx-btn lx-btn-primary w-100 justify-content-center" style="padding:13px;">
                        {{ __('Send OTP') }}
                    </button>
                </form>

                <p class="lg-auth-switch">
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--lx-gold);">
                        <i class="las la-arrow-left"></i> {{ __('Back to Sign In') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_phone_js('#phone_number', 'otp') !!}
<script>
(function($){
    {!! theme_btn_loading_js('otp_btn', __('Sending…')) !!}
})(jQuery);
</script>
@endsection
