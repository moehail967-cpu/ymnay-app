@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Login with OTP') }} @endsection
@section('page-title') {{ __('Login with OTP') }} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ __('OTP Login') }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ route('tenant.user.login') }}">{{ __('Login') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('OTP Login') }}</span>
        </div>
    </div>
</div>

<div class="container tn-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="tn-auth-card">
                <div class="tn-auth-icon"><i class="las la-mobile-alt"></i></div>
                <h2 class="tn-auth-title">{{ __('Login with OTP') }}</h2>
                <p class="tn-auth-sub">{{ __('Enter your phone number to receive a one-time password.') }}</p>

                {!! theme_flash_msg() !!}
                {!! theme_error_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="POST">
                    {!! theme_csrf_field() !!}
                    <div class="mb-4">
                        <label class="tn-label">{{ __('Phone Number') }} *</label>
                        <input type="tel" name="phone" id="telephone" class="tn-input"
                               placeholder="{{ __('Your registered phone number') }}" required autofocus>
                    </div>
                    <button type="submit" id="login_btn" class="tn-btn tn-btn-primary w-100">
                        <i class="las la-paper-plane"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p class="tn-auth-footer text-center mt-4">
                    {{ __('Prefer password?') }}
                    <a href="{{ route('tenant.user.login') }}" class="tn-auth-footer-link">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_phone_js('#telephone', 'login_btn') !!}
{!! theme_btn_loading_js('login_btn', __('Sending…')) !!}
@endsection
