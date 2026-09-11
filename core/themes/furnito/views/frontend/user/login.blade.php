@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div class="fn-page-banner">
    <div class="container">
        <h1>{{ __('My Account') }}</h1>
        <div class="fn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="fn-auth-max-wrap">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}
        <div class="fn-auth-panel">

            {{-- Sign In --}}
            <div class="fn-auth-left">
                <h2 class="fn-auth-title">{{ __('Sign In') }}</h2>
                <p class="fn-auth-sub">{{ __('Welcome back! Log in to manage your orders.') }}</p>
                <form action="" method="post" id="login_form_order_page">
                    @csrf
                    <div class="mb-3">
                        <label class="fn-label">{{ __('Email or Username') }}</label>
                        <input type="text" name="username" class="fn-input" placeholder="{{ __('your@email.com') }}">
                    </div>
                    <div class="mb-3">
                        <label class="fn-label">{{ __('Password') }}</label>
                        <div class="fn-input-wrap">
                            <input type="password" name="password" id="fn_login_pwd" class="fn-input" placeholder="••••••••">
                            <button type="button" class="fn-input-eye" onclick="fnTogglePwd('fn_login_pwd',this)"><i class="las la-eye"></i></button>
                        </div>
                    </div>
                    <div class="fn-remember-row mb-4">
                        <label class="fn-remember-label"><input type="checkbox" name="remember"> {{ __('Remember Me') }}</label>
                        <a href="{{ route('tenant.user.forget.password') }}" class="fn-auth-forgot">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" class="fn-btn fn-btn-gold w-100 fn-auth-submit">
                        <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" class="fn-btn fn-btn-outline w-100 justify-content-center fn-auth-otp-btn mt-2">{{ __('Login with OTP') }}</a>
                    @endif
                </form>
            </div>

            {{-- Create Account --}}
            <div class="fn-auth-right">
                <h2 class="fn-auth-title">{{ __('Create Account') }}</h2>
                <p class="fn-auth-sub">{{ __('Join for early access, exclusive offers, and faster checkout.') }}</p>
                <form action="{{ theme_register_store_url() }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="fn-label">{{ __('Full Name') }} <span class="fn-required">*</span></label>
                        <input type="text" name="name" class="fn-input" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}">
                    </div>
                    <div class="mb-3">
                        <label class="fn-label">{{ __('Username') }} <span class="fn-required">*</span></label>
                        <input type="text" name="username" class="fn-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                    </div>
                    <div class="mb-3">
                        <label class="fn-label">{{ __('Email Address') }} <span class="fn-required">*</span></label>
                        <input type="email" name="email" class="fn-input" placeholder="{{ __('your@email.com') }}">
                    </div>
                    <div class="mb-3">
                        <label class="fn-label">{{ __('Password') }} <span class="fn-required">*</span></label>
                        <input type="password" name="password" class="fn-input" placeholder="{{ __('Min. 8 characters') }}">
                    </div>
                    <div class="mb-3">
                        <label class="fn-label">{{ __('Confirm Password') }} <span class="fn-required">*</span></label>
                        <input type="password" name="password_confirmation" class="fn-input" placeholder="{{ __('Repeat your password') }}">
                    </div>
                    <div class="fn-terms-check mb-4">
                        <input type="checkbox" id="terms">
                        <label for="terms">{{ __('I agree to the') }} <a href="#">{{ __('Terms of Service') }}</a> &amp; <a href="#">{{ __('Privacy Policy') }}</a></label>
                    </div>
                    <button type="submit" id="register" class="fn-btn fn-btn-gold w-100 fn-auth-submit">
                        <i class="las la-user-plus"></i> {{ __('Create Account') }}
                    </button>
                </form>
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
        var btn = $(this);
        var form = $('#login_form_order_page');
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
                try { msg = xhr.responseJSON.message || msg; } catch (err) {}
                toastr.error(msg);
            }
        });
    });

    $('#register').closest('form').on('submit', function () {
        var btn = $('#register');
        btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
        btn.css({ opacity: '0.7', 'pointer-events': 'none' });
    });
})(jQuery);
</script>
<script>
function fnTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
