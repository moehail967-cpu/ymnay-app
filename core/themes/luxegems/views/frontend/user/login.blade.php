@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Sign In') }} @endsection

@section('content')
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ __('Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="lg-auth-card">
                <div class="lg-auth-header">
                    {!! theme_logo_html('lg-auth-logo') !!}
                    <h2 class="lg-auth-title">{{ __('Welcome Back') }}</h2>
                    <p class="lg-auth-sub">{{ __('Sign in to your account to continue') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="lg-auth-form" id="login_form_order_page">
                    @csrf
                    <div class="lg-form-group">
                        <label class="lg-form-label">{{ __('Email or Username') }} <span class="lg-required">*</span></label>
                        <input type="text" name="username" class="lg-form-control" placeholder="{{ __('Enter your email or username') }}">
                    </div>
                    <div class="lg-form-group">
                        <label class="lg-form-label">{{ __('Password') }} <span class="lg-required">*</span></label>
                        <div class="lg-input-wrap">
                            <input type="password" name="password" class="lg-form-control" id="lg_login_password" placeholder="{{ __('Enter your password') }}">
                            <button type="button" class="lg-input-eye" onclick="lgTogglePwd('lg_login_password', this)">
                                <i class="las la-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="lg-form-row-between">
                        <label class="lg-remember">
                            <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" class="lg-forgot">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="lx-btn lx-btn-primary w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" class="lx-btn lx-btn-outline w-100 justify-content-center mt-3" style="padding:12px;font-size:14px;">
                        {{ __('Login with OTP') }}
                    </a>
                    @endif
                </form>

                <p class="lg-auth-switch">
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
function lgTogglePwd(id, btn) {
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
