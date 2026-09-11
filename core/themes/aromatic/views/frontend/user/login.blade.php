@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
{{-- Page Banner --}}
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('My Account') }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

@auth('web')
<div class="container py-5 text-center">
    <i class="mdi mdi-check-circle ar-login-icon"></i>
    <h3 class="ar-login-already-title">{{ __('You are already logged in') }}</h3>
    <a href="{{ theme_user_dashboard_url() }}" class="ar-btn ar-btn-red mt-3">{{ __('Go to Dashboard') }}</a>
</div>
@else
<div class="container py-5">
    <div class="ar-auth-login-wrap">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}
        <div class="row g-0 ar-auth-panel">

            {{-- Sign In --}}
            <div class="col-md-6">
                <div class="ar-auth-panel-left">
                    <h2 class="ar-auth-title">{{ __('Sign In') }}</h2>
                    <p class="ar-auth-sub mb-3">{{ __('Welcome back! Log in to manage your orders.') }}</p>
                    <form action="" method="post" id="ar_login_form">
                        @csrf
                        <div class="mb-3">
                            <label class="ar-auth-label">{{ __('Email or Username') }}</label>
                            <input type="text" name="username" class="ar-auth-input" placeholder="{{ __('your@email.com') }}">
                        </div>
                        <div class="mb-3">
                            <label class="ar-auth-label">{{ __('Password') }}</label>
                            <div class="ar-input-wrap">
                                <input type="password" name="password" id="ar_login_pwd" class="ar-auth-input" placeholder="••••••••">
                                <button type="button" class="ar-input-eye" onclick="arTogglePwd('ar_login_pwd',this)"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                        <div class="ar-remember-row">
                            <label class="ar-remember-label"><input type="checkbox" name="remember"> {{ __('Remember Me') }}</label>
                            <a href="{{ route('tenant.user.forget.password') }}" class="ar-auth-forgot">{{ __('Forgot Password?') }}</a>
                        </div>
                        <button type="submit" id="ar_login_btn" class="ar-btn ar-btn-red w-100 justify-content-center">
                            <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                        </button>
                        @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                        <a href="{{ route('tenant.user.login.otp') }}" class="ar-btn ar-btn-outline w-100 justify-content-center mt-2">{{ __('Login with OTP') }}</a>
                        @endif
                        <p class="ar-auth-switch">
                            {{ __("Don't have an account?") }}
                            <a href="{{ route('tenant.user.register') }}">{{ __('Register') }}</a>
                        </p>
                    </form>
                </div>
            </div>

            {{-- Create Account --}}
            <div class="col-md-6 ar-auth-panel-right">
                <div class="ar-auth-panel-icon-wrap">
                    <i class="las la-flask ar-login-icon"></i>
                </div>
                <h2 class="ar-auth-panel-title">{{ __('New to Aromatic?') }}</h2>
                <p class="ar-auth-panel-desc">{{ __('Create your account to enjoy exclusive fragrances, track orders, and get personalized recommendations.') }}</p>
                <a href="{{ route('tenant.user.register') }}" class="ar-btn ar-btn-gold justify-content-center w-100">
                    <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                </a>
            </div>

        </div>
    </div>
</div>
@endauth
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).on('click', '#ar_login_btn', function (e) {
        e.preventDefault();
        var btn  = $(this);
        var form = $('#ar_login_form');
        btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url: '{{ theme_ajax_login_url() }}',
            data: {
                _token:   '{{ theme_csrf() }}',
                username: form.find('[name=username]').val(),
                password: form.find('[name=password]').val(),
                remember: form.find('[name=remember]').is(':checked') ? 1 : 0,
            },
            success: function (data) {
                if (data.status === 'invalid') {
                    btn.html('<i class="mdi mdi-login"></i> {{ __("Sign In") }}').prop('disabled', false);
                    toastr.error(data.msg);
                } else {
                    btn.html('{{ __("Redirecting…") }}');
                    location.reload();
                }
            },
            error: function (xhr) {
                btn.html('<i class="mdi mdi-login"></i> {{ __("Sign In") }}').prop('disabled', false);
                var msg = '{{ __("Something went wrong. Please try again.") }}';
                try { msg = xhr.responseJSON.message || msg; } catch (err) {}
                toastr.error(msg);
            }
        });
    });
})(jQuery);

function arTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
