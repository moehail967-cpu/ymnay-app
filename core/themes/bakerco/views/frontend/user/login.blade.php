@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
{{-- Page Banner --}}
<div class="bk-page-banner">
    <div class="container">
        <h1>{{ __('My Account') }}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="mx-auto bk-auth-max-wrap">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}
        <div class="bk-auth-panel">
            <div class="row g-0">
                {{-- Sign In --}}
                <div class="col-md-6">
                    <div class="bk-auth-left">
                        <h2 class="bk-auth-title">{{ __('Sign In') }}</h2>
                        <p class="bk-auth-sub">{{ __('Welcome back! Log in to manage your orders.') }}</p>
                        <form action="" method="post" id="login_form_order_page">
                            @csrf
                            <div class="mb-3">
                                <label class="bk-label">{{ __('Email or Username') }}</label>
                                <input type="text" name="username" class="bk-input" placeholder="{{ __('your@email.com') }}">
                            </div>
                            <div class="mb-3">
                                <label class="bk-label">{{ __('Password') }}</label>
                                <div class="bk-input-wrap">
                                    <input type="password" name="password" id="bk_login_pwd" class="bk-input" placeholder="••••••••">
                                    <button type="button" class="bk-input-eye" onclick="bkTogglePwd('bk_login_pwd',this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                            <div class="bk-remember-row">
                                <label class="bk-remember-label"><input type="checkbox" name="remember"> {{ __('Remember Me') }}</label>
                                <a href="{{ route('tenant.user.forget.password') }}" class="bk-auth-forgot">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button type="submit" id="login_btn" class="bk-btn bk-btn-rose w-100 bk-auth-submit">
                                <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                            </button>
                            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                            <a href="{{ route('tenant.user.login.otp') }}" class="bk-btn bk-btn-outline w-100 justify-content-center bk-auth-otp-btn mt-2">{{ __('Login with OTP') }}</a>
                            @endif
                        </form>
                    </div>
                </div>
                {{-- Create Account --}}
                <div class="col-md-6">
                    <div class="bk-auth-right">
                        <h2 class="bk-auth-title">{{ __('Create Account') }}</h2>
                        <p class="bk-auth-sub">{{ __('Join for early access, loyalty rewards, and faster checkout.') }}</p>
                        <form action="{{ theme_register_store_url() }}" method="post">
                            @csrf
                            <div class="mb-3"><label class="bk-label">{{ __('Full Name') }} <span class="bk-required">*</span></label><input type="text" name="name" class="bk-input" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}"></div>
                            <div class="mb-3"><label class="bk-label">{{ __('Username') }} <span class="bk-required">*</span></label><input type="text" name="username" class="bk-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"></div>
                            <div class="mb-3"><label class="bk-label">{{ __('Email Address') }} <span class="bk-required">*</span></label><input type="email" name="email" class="bk-input" placeholder="{{ __('your@email.com') }}"></div>
                            <div class="mb-3"><label class="bk-label">{{ __('Password') }} <span class="bk-required">*</span></label><input type="password" name="password" class="bk-input" placeholder="{{ __('Min. 8 characters') }}"></div>
                            <div class="mb-3"><label class="bk-label">{{ __('Confirm Password') }} <span class="bk-required">*</span></label><input type="password" name="password_confirmation" class="bk-input" placeholder="{{ __('Repeat your password') }}"></div>
                            <div class="bk-terms-check">
                                <input type="checkbox" id="terms">
                                <label for="terms">{{ __('I agree to the') }} <a href="#">{{ __('Terms of Service') }}</a> &amp; <a href="#">{{ __('Privacy Policy') }}</a></label>
                            </div>
                            <button type="submit" id="register" class="bk-btn bk-btn-rose w-100 bk-auth-submit">
                                <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                            </button>
                        </form>
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
</script>
<script>
function bkTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
