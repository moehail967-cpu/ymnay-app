@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Login') }} @endsection
@section('page-title') {{ __('Login') }} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ __('Sign In') }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Login') }}</span>
        </div>
    </div>
</div>

<div class="container tn-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="tn-auth-card">
                <div class="tn-auth-icon"><i class="las la-baby"></i></div>
                <h2 class="tn-auth-title">{{ __('Welcome Back') }}</h2>
                <p class="tn-auth-sub">{{ __('Sign in to continue shopping for your little ones.') }}</p>

                {!! theme_flash_msg() !!}
                {!! theme_error_msg() !!}

                <form action="{{ route('tenant.user.login') }}" method="POST" id="tn_login_form">
                    {!! theme_csrf_field() !!}
                    <div class="mb-3">
                        <label class="tn-label">{{ __('Email Address') }} *</label>
                        <input type="email" name="email" class="tn-input" value="{{ old('email') }}"
                               placeholder="{{ __('you@example.com') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="tn-label">{{ __('Password') }} *</label>
                        <div class="tn-input-wrap">
                            <input type="password" name="password" class="tn-input" id="tn_pwd"
                                   placeholder="{{ __('••••••••') }}" required>
                            <button type="button" class="tn-pwd-toggle" onclick="tnTogglePwd()">
                                <i class="las la-eye" id="tn_pwd_icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <label class="tn-remember-label d-flex align-items-center gap-2">
                            <input type="checkbox" name="remember"> {{ __('Remember me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" class="tn-forgot-link">
                            {{ __('Forgot Password?') }}
                        </a>
                    </div>
                    <button type="submit" class="tn-btn tn-btn-primary w-100">
                        {{ __('Sign In') }}
                    </button>
                </form>

                <p class="tn-auth-footer text-center mt-2">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('tenant.user.register') }}" class="tn-auth-footer-link">{{ __('Register') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function tnTogglePwd() {
    var inp  = document.getElementById('tn_pwd');
    var icon = document.getElementById('tn_pwd_icon');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.className = 'las la-eye-slash';
    } else {
        inp.type = 'password';
        icon.className = 'las la-eye';
    }
}
</script>
{!! theme_ajax_login_js() !!}
@endsection
