@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Sign In') }} @endsection
@section('page-title') {{ __('Sign In') }} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ __('Sign In') }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ __('Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="fm-auth-card">
                <div class="fm-auth-header">
                    {!! theme_logo_html('fm-auth-logo-link', 'fm-auth-logo') !!}
                    <h2 class="fm-auth-title mt-3">{{ __('Welcome Back') }}</h2>
                    <p class="fm-auth-sub">{{ __('Sign in to your account to continue') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="fm-auth-form" id="login_form_order_page">
                    @csrf
                    <div class="fm-form-group">
                        <label class="fm-label">{{ __('Email or Username') }} <span class="fm-required">*</span></label>
                        <input type="text" name="username" class="fm-input" placeholder="{{ __('Enter your email or username') }}">
                    </div>
                    <div class="fm-form-group">
                        <label class="fm-label">{{ __('Password') }} <span class="fm-required">*</span></label>
                        <div class="fm-input-wrap">
                            <input type="password" name="password" class="fm-input" id="fm_login_password" placeholder="{{ __('Enter your password') }}" style="padding-right:44px;">
                            <button type="button" class="fm-input-eye" onclick="fmTogglePwd('fm_login_password', this)">
                                <i class="las la-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fm-remember-row">
                        <label class="fm-remember-label">
                            <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" class="fm-forgot">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="fm-btn fm-btn-green w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" class="fm-btn fm-btn-outline w-100 justify-content-center mt-3" style="padding:12px;font-size:14px;">
                        {{ __('Login with OTP') }}
                    </a>
                    @endif
                </form>

                <p class="fm-auth-switch">
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
function fmTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
