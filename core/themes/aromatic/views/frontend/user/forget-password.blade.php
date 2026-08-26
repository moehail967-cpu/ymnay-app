@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('Forgot Password') }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span>{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container ar-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="ar-auth-card">
                <div class="ar-auth-centered">
                    <i class="mdi mdi-lock-reset" style="font-size:48px;color:var(--ar-red);"></i>
                    <h2 class="ar-auth-title" style="margin-top:12px;">{{ __('Reset Password') }}</h2>
                    <p class="ar-auth-sub">{{ __('Enter your email or username and we will send you a password reset link.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.forget.password') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="ar-auth-label">{{ __('Email or Username') }} <span class="ar-required">*</span></label>
                        <input type="text" name="username" class="ar-auth-input" placeholder="{{ __('your@email.com') }}" value="{{ old('username') }}" required>
                    </div>
                    <button type="submit" id="forget_pwd_btn" class="ar-btn ar-btn-red w-100 justify-content-center">
                        {!! theme_btn_loading_js() !!}
                        <i class="mdi mdi-send-outline"></i> {{ __('Send Reset Link') }}
                    </button>
                </form>

                <p class="ar-auth-switch">
                    {{ __('Remember your password?') }}
                    <a href="{{ route('tenant.user.login') }}">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
