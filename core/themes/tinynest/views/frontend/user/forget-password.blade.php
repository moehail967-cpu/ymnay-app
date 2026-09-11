@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ __('Forgot Password') }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ route('tenant.user.login') }}">{{ __('Login') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container tn-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="tn-auth-card">
                <div class="tn-auth-icon"><i class="las la-lock-open"></i></div>
                <h2 class="tn-auth-title">{{ __('Reset Password') }}</h2>
                <p class="tn-auth-sub">{{ __('Enter your email and we\'ll send you a reset link.') }}</p>

                {!! theme_flash_msg() !!}
                {!! theme_error_msg() !!}

                <form action="{{ route('tenant.user.forget.password') }}" method="POST">
                    {!! theme_csrf_field() !!}
                    <div class="mb-4">
                        <label class="tn-label">{{ __('Email Address') }} *</label>
                        <input type="email" name="username" class="tn-input"
                               value="{{ old('username') }}"
                               placeholder="{{ __('you@example.com') }}" required autofocus>
                    </div>
                    <button type="submit" class="tn-btn tn-btn-primary w-100">
                        {{ __('Send Reset Link') }}
                    </button>
                </form>

                <p class="tn-auth-footer text-center mt-4">
                    {{ __('Remembered your password?') }}
                    <a href="{{ route('tenant.user.login') }}" class="tn-auth-footer-link">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
