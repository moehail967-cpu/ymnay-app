@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="ph-page-banner">
    <div class="container">
        <h1>{{ __('Forgot Password') }}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="ph-auth-card">
                <div class="ph-auth-header">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--ph-terra-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;border:2px solid var(--ph-border);">
                        <i class="las la-lock-open" style="font-size:32px;color:var(--ph-terra);"></i>
                    </div>
                    <h2 class="ph-auth-title">{{ __('Reset Password') }}</h2>
                    <p class="ph-auth-sub">{{ __('Enter your username or email to receive reset instructions.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="ph-auth-form">
                    @csrf
                    <div class="ph-form-group">
                        <label class="ph-label">{{ __('Username or Email') }} <span class="ph-required">*</span></label>
                        <input type="text" name="username" class="ph-input" placeholder="{{ __('Enter your username or email') }}">
                    </div>
                    <button type="submit" id="forget_btn" class="ph-btn ph-btn-terra w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        {{ __('Send Reset Link') }}
                    </button>
                </form>

                <p class="ph-auth-switch">
                    <a href="{{ route('tenant.user.login') }}"><i class="las la-arrow-left"></i> {{ __('Back to Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_btn_loading_js('forget_btn', __('Sending…')) !!}
@endsection
