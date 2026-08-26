@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="fn-page-banner">
    <div class="container">
        <h1>{{ __('Forgot Password') }}</h1>
        <div class="fn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="mx-auto fn-auth-centered">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}
        <div class="fn-auth-card">
            <div class="fn-auth-icon-wrap">
                <i class="las la-lock"></i>
            </div>
            <h2 class="fn-auth-title">{{ __('Reset Your Password') }}</h2>
            <p class="fn-auth-sub">{{ __('Enter your email and we\'ll send you a reset link.') }}</p>

            <form action="{{ route('tenant.user.forget.password') }}" method="post">
                @csrf
                <div class="mb-4">
                    <label class="fn-label">{{ __('Email Address') }} <span class="fn-required">*</span></label>
                    <input type="email" name="email" class="fn-input" placeholder="{{ __('your@email.com') }}" required>
                </div>
                <button type="submit" class="fn-btn fn-btn-gold w-100">
                    <i class="las la-paper-plane"></i> {{ __('Send Reset Link') }}
                </button>
            </form>

            <p class="fn-auth-switch mt-3">
                <a href="{{ route('tenant.user.login') }}"><i class="las la-arrow-left"></i> {{ __('Back to Sign In') }}</a>
            </p>
        </div>
    </div>
</div>
@endsection
