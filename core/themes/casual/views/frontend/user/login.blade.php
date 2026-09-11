@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ __('My Account') }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container cs-auth-wrap">
    {!! theme_error_msg() !!}
    {!! theme_flash_msg() !!}

    <div class="cs-auth-panel">
        <div class="row g-0">

            {{-- Sign In --}}
            <div class="col-md-6">
                <div class="cs-auth-left">
                    <h2 class="cs-auth-title">{{ __('Sign In') }}</h2>
                    <p class="cs-auth-sub">{{ __('Welcome back! Log in to manage your orders.') }}</p>

                    <form action="" method="post" id="login_form_order_page">
                        @csrf
                        <div class="cs-auth-field">
                            <label class="cs-auth-label">{{ __('Email or Username') }}</label>
                            <div class="cs-auth-input-wrap">
                                <i class="las la-user cs-auth-input-icon"></i>
                                <input type="text" name="username" class="cs-auth-input" placeholder="{{ __('your@email.com') }}">
                            </div>
                        </div>
                        <div class="cs-auth-field">
                            <label class="cs-auth-label">{{ __('Password') }}</label>
                            <div class="cs-auth-input-wrap">
                                <i class="las la-lock cs-auth-input-icon"></i>
                                <input type="password" name="password" id="cs_login_pwd" class="cs-auth-input" placeholder="••••••••">
                                <button type="button" class="cs-auth-eye" onclick="csTogglePwd('cs_login_pwd', this)">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="cs-auth-remember-row">
                            <label class="cs-auth-remember">
                                <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                            </label>
                            <a href="{{ route('tenant.user.forget.password') }}" class="cs-auth-forgot">{{ __('Forgot Password?') }}</a>
                        </div>
                        <button type="submit" id="login_btn" class="cs-auth-submit-btn">
                            <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
                        </button>
                        @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                        <a href="{{ route('tenant.user.login.otp') }}" class="cs-auth-otp-btn mt-2">
                            <i class="las la-mobile-alt"></i> {{ __('Login with OTP') }}
                        </a>
                        @endif
                        <p class="cs-auth-switch-note mt-3">
                            {{ __("Don't have an account?") }}
                            <a href="{{ route('tenant.user.register') }}" class="cs-auth-switch-link">{{ __('Create one') }}</a>
                        </p>
                    </form>
                </div>
            </div>

            {{-- Create Account --}}
            <div class="col-md-6">
                <div class="cs-auth-right">
                    <div class="cs-auth-promo">
                        {!! theme_logo_html('cs-auth-logo-link', 'cs-auth-logo-img') !!}
                        <h2 class="cs-auth-promo-title">{{ __('New Here?') }}</h2>
                        <p class="cs-auth-promo-sub">{{ __('Create an account to track orders, save wishlists, and enjoy faster checkout every time.') }}</p>
                        <ul class="cs-auth-perks">
                            <li><i class="las la-check-circle"></i> {{ __('Track your orders in real-time') }}</li>
                            <li><i class="las la-check-circle"></i> {{ __('Save items to your wishlist') }}</li>
                            <li><i class="las la-check-circle"></i> {{ __('Faster checkout with saved address') }}</li>
                            <li><i class="las la-check-circle"></i> {{ __('Exclusive member offers') }}</li>
                        </ul>
                        <a href="{{ route('tenant.user.register') }}" class="cs-auth-register-btn">
                            <i class="las la-user-plus"></i> {{ __('Create Account') }}
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).on('click', '#login_btn', function (e) {
        e.preventDefault();
        var btn  = $(this);
        var form = $('#login_form_order_page');
        btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}').prop('disabled', true);
        $.ajax({
            type: 'POST',
            url:  '{{ theme_ajax_login_url() }}',
            data: {
                _token:   '{{ theme_csrf() }}',
                username: form.find('[name=username]').val(),
                password: form.find('[name=password]').val(),
                remember: form.find('[name=remember]').is(':checked') ? 1 : 0,
            },
            success: function (data) {
                if (data.status === 'invalid') {
                    btn.html('<i class="las la-sign-in-alt"></i> {{ __("Sign In") }}').prop('disabled', false);
                    toastr.error(data.msg);
                } else {
                    btn.html('{{ __("Redirecting…") }}');
                    location.reload();
                }
            },
            error: function (xhr) {
                btn.html('<i class="las la-sign-in-alt"></i> {{ __("Sign In") }}').prop('disabled', false);
                var msg = '{{ __("Something went wrong. Please try again.") }}';
                try { msg = xhr.responseJSON.message || msg; } catch (e) {}
                toastr.error(msg);
            }
        });
    });
})(jQuery);

function csTogglePwd(id, btn) {
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
