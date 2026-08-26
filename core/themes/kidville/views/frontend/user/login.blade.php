@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Sign In') }} @endsection

@section('content')
<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('Sign In') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="kv-auth-card">
                <div class="kv-auth-header">
                    <div class="kv-auth-logo">
                        {!! theme_logo_html('kv-auth-logo-link', 'kv-logo-img', 'kv-brand') !!}
                    </div>
                    <h2 class="kv-auth-title">{{ __('Welcome Back') }}</h2>
                    <p class="kv-auth-sub">{{ __('Sign in to your account to continue') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="kv-auth-form" id="login_form_order_page">
                    @csrf
                    <div class="kv-form-group">
                        <label class="kv-label">{{ __('Email or Username') }} <span class="kv-required">*</span></label>
                        <input type="text" name="username" class="kv-input" placeholder="{{ __('Enter your email or username') }}">
                    </div>
                    <div class="kv-form-group">
                        <label class="kv-label">{{ __('Password') }} <span class="kv-required">*</span></label>
                        <div class="kv-input-wrap">
                            <input type="password" name="password" class="kv-input" id="kv_login_password" placeholder="{{ __('Enter your password') }}">
                            <button type="button" class="kv-input-eye" onclick="kvTogglePwd('kv_login_password', this)">
                                <i class="las la-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="kv-form-row-between">
                        <label class="kv-remember">
                            <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" class="kv-forgot">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="kv-btn kv-btn-red w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" class="kv-btn kv-btn-outline w-100 justify-content-center mt-3" style="padding:12px;font-size:14px;">
                        {{ __('Login with OTP') }}
                    </a>
                    @endif
                </form>

                <p class="kv-auth-switch">
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
function kvTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'las la-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'las la-eye';
    }
}
</script>
@endsection
