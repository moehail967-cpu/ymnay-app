@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('Forgot Password') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container bp-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="bp-auth-card">
                <div class="bp-auth-centered">
                    {!! theme_logo_html('bp-auth-logo') !!}
                    <h2 class="bp-auth-title bp-auth-logo-top">{{ __('Reset Password') }}</h2>
                    <p class="bp-auth-sub">{{ __('Enter your email and we will send you a reset link.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.forget.password.email') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="bp-label">{{ __('Email Address') }} <span class="bp-required">*</span></label>
                        <input type="email" name="email" class="bp-input" placeholder="{{ __('your@email.com') }}" required>
                    </div>
                    <button type="submit" class="bp-btn bp-btn-green w-100 justify-content-center">
                        <i class="las la-paper-plane"></i> {{ __('Send Reset Link') }}
                    </button>
                </form>

                <p class="bp-auth-switch">
                    {{ __('Remembered your password?') }}
                    <a href="{{ route('tenant.user.login') }}">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
