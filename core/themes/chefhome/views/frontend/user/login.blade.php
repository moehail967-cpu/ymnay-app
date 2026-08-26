@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Sign In') }} @endsection
@section('page-title') {{ __('Sign In') }} @endsection

@section('content')
<div class="ch-page-banner">
    <div class="container ch-page-banner-content">
        <h1>{{ __('Sign In') }}</h1>
        <div class="ch-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="ch-auth-card">
                <div class="ch-auth-header">
                    {!! theme_logo_html('ch-auth-logo') !!}
                    <h2 class="ch-auth-title">{{ __('Welcome Back') }}</h2>
                    <p class="ch-auth-sub">{{ __('Sign in to your account to continue') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="ch-auth-form" id="login_form_order_page">
                    @csrf
                    <div class="ch-form-group">
                        <label class="ch-label">{{ __('Email or Username') }} <span class="ch-required">*</span></label>
                        <input type="text" name="username" class="ch-input" placeholder="{{ __('Enter your email or username') }}">
                    </div>
                    <div class="ch-form-group">
                        <label class="ch-label">{{ __('Password') }} <span class="ch-required">*</span></label>
                        <div class="ch-input-wrap">
                            <input type="password" name="password" class="ch-input" id="ch_login_password" placeholder="{{ __('Enter your password') }}">
                            <button type="button" class="ch-input-eye" onclick="chTogglePwd('ch_login_password', this)">
                                <i class="las la-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="ch-form-row-between">
                        <label class="ch-remember">
                            <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" class="ch-forgot">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="ch-btn ch-btn-red w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" class="ch-btn ch-btn-outline w-100 justify-content-center mt-3" style="padding:12px;font-size:14px;">
                        {{ __('Login with OTP') }}
                    </a>
                    @endif
                </form>

                <p class="ch-auth-switch">
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
function chTogglePwd(id, btn) {
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
