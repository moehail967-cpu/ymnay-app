@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Login with OTP') }} @endsection
@section('page-title') {{ __('Login with OTP') }} @endsection

@section('content')
<div class="ph-page-banner">
    <div class="container">
        <h1>{{ __('OTP Login') }}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('OTP Login') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="ph-auth-card">
                <div class="ph-auth-header">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--ph-terra-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border:2px solid var(--ph-border);">
                        <i class="las la-mobile-alt" style="font-size:32px;color:var(--ph-terra);"></i>
                    </div>
                    <h2 class="ph-auth-title">{{ __('Login with OTP') }}</h2>
                    <p class="ph-auth-sub">{{ __('Enter your phone number to receive a one-time password.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="post" class="ph-auth-form">
                    @csrf
                    <div class="ph-form-group">
                        <label class="ph-label">{{ __('Phone Number') }} <span class="ph-required">*</span></label>
                        <input type="tel" name="phone" id="otp_phone" class="ph-input" placeholder="{{ __('Your registered phone number') }}">
                    </div>
                    <button type="submit" id="otp_send_btn" class="ph-btn ph-btn-terra w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="las la-paper-plane"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p class="ph-auth-switch">
                    {{ __('Prefer password?') }}
                    <a href="{{ route('tenant.user.login') }}">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_phone_js('#otp_phone', 'otp_send_btn') !!}
{!! theme_btn_loading_js('otp_send_btn', __('Sending…')) !!}
@endsection
