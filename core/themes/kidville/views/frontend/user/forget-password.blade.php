@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection

@section('content')

<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('Forgot Password') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="kv-auth-card">
                <div class="kv-auth-header">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--kv-light);border:3px solid var(--kv-border);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--kv-red);">
                        <i class="las la-key"></i>
                    </div>
                    <h2 class="kv-auth-title">{{ __('Reset Your Password') }}</h2>
                    <p class="kv-auth-sub">{{ __("Enter your username or email and we'll send you a reset link.") }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.forget.password') }}" method="post">
                    @csrf
                    <div class="kv-form-group">
                        <label class="kv-label">{{ __('Username or Email') }} <span class="kv-required">*</span></label>
                        <input type="text" name="username" class="kv-input" placeholder="{{ __('Enter your username or email') }}">
                    </div>

                    <button type="submit" id="send" class="kv-btn kv-btn-red" style="width:100%;justify-content:center;padding:13px;font-size:15px;">
                        <i class="las la-envelope-open-text"></i> {{ __('Send Reset Mail') }}
                    </button>
                </form>

                <p class="kv-auth-switch" style="margin-top:20px;">
                    {{ __('Remembered your password?') }}
                    <a href="{{ route('tenant.user.login') }}">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).ready(function () {
        {!! theme_btn_loading_js('send', __('Sending…')) !!}
    });
})(jQuery);
</script>
@endsection
