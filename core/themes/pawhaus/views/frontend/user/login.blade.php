@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Sign In') }} @endsection
@section('page-title') {{ __('Sign In') }} @endsection

@section('content')
<div class="ph-page-banner">
    <div class="container">
        <h1>{{ __('Sign In') }}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="ph-auth-card">
                <div class="ph-auth-header">
                    {!! theme_logo_html('ph-auth-logo') !!}
                    <h2 class="ph-auth-title">{{ __('Welcome Back') }}</h2>
                    <p class="ph-auth-sub">{{ __('Sign in to your account to continue') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="ph-auth-form" id="login_form_order_page">
                    @csrf
                    <div class="ph-form-group">
                        <label class="ph-label">{{ __('Email or Username') }} <span class="ph-required">*</span></label>
                        <input type="text" name="username" class="ph-input" placeholder="{{ __('Enter your email or username') }}">
                    </div>
                    <div class="ph-form-group">
                        <label class="ph-label">{{ __('Password') }} <span class="ph-required">*</span></label>
                        <div class="ph-input-wrap">
                            <input type="password" name="password" class="ph-input" id="ph_login_password" placeholder="{{ __('Enter your password') }}">
                            <button type="button" class="ph-input-eye" onclick="phTogglePwd('ph_login_password', this)">
                                <i class="las la-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="ph-form-row-between">
                        <label class="ph-remember">
                            <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" class="ph-forgot">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="ph-btn ph-btn-terra w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" class="ph-btn ph-btn-outline w-100 justify-content-center mt-3" style="padding:12px;font-size:14px;">
                        {{ __('Login with OTP') }}
                    </a>
                    @endif
                </form>

                <p class="ph-auth-switch">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('tenant.user.register') }}">{{ __('Sign Up') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_ajax_login_js() !!}
<script>
function phTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
