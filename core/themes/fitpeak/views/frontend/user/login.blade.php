@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Sign In') }} @endsection
@section('page-title') {{ __('Sign In') }} @endsection

@section('content')
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{{ __('Sign In') }}</h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li>{{ __('Sign In') }}</li>
        </ul>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="fp-auth-card">
                <div class="fp-auth-header">
                    {!! theme_logo_html('fp-auth-logo') !!}
                    <h2 class="fp-auth-title">{{ __('Welcome Back') }}</h2>
                    <p class="fp-auth-sub">{{ __('Sign in to your account to continue') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="fp-auth-form" id="login_form_order_page">
                    @csrf
                    <div class="fp-form-group">
                        <label class="fp-auth-label">{{ __('Email or Username') }} <span class="fp-required">*</span></label>
                        <input type="text" name="username" class="fp-auth-input" placeholder="{{ __('Enter your email or username') }}">
                    </div>
                    <div class="fp-form-group">
                        <label class="fp-auth-label">{{ __('Password') }} <span class="fp-required">*</span></label>
                        <div class="fp-input-wrap">
                            <input type="password" name="password" class="fp-auth-input" id="fp_login_password" placeholder="{{ __('Enter your password') }}">
                            <button type="button" class="fp-input-eye" onclick="fpTogglePwd('fp_login_password', this)">
                                <i class="mdi mdi-eye-outline"></i>
                            </button>
                        </div>
                    </div>
                    <div class="fp-form-row-between">
                        <label class="fp-remember">
                            <input type="checkbox" name="remember" style="accent-color:var(--fp-green);"> {{ __('Remember Me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" class="fp-forgot">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="fp-btn fp-btn-primary w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" class="fp-btn fp-btn-outline w-100 justify-content-center mt-3" style="padding:12px;font-size:14px;">
                        {{ __('Login with OTP') }}
                    </a>
                    @endif
                </form>

                <p class="fp-auth-switch">
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
function fpTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'mdi mdi-eye-off-outline';
    } else {
        input.type = 'password';
        icon.className = 'mdi mdi-eye-outline';
    }
}
</script>
@endsection
