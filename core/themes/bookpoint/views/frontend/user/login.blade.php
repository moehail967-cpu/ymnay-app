@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('My Account') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="mx-auto bp-auth-max-wrap">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}
        <div class="bp-auth-panel">
            <div class="row g-0">
                {{-- Sign In --}}
                <div class="col-md-6">
                    <div class="bp-auth-left">
                        <h2 class="bp-auth-title">{{ __('Sign In') }}</h2>
                        <p class="bp-auth-sub">{{ __('Welcome back! Log in to manage your orders.') }}</p>
                        <form action="" method="post" id="login_form_order_page">
                            @csrf
                            <div class="mb-3">
                                <label class="bp-label">{{ __('Email or Username') }}</label>
                                <input type="text" name="username" class="bp-input" placeholder="{{ __('your@email.com') }}">
                            </div>
                            <div class="mb-3">
                                <label class="bp-label">{{ __('Password') }}</label>
                                <div class="bp-input-wrap">
                                    <input type="password" name="password" id="bp_login_pwd" class="bp-input" placeholder="••••••••">
                                    <button type="button" class="bp-input-eye" onclick="bpTogglePwd('bp_login_pwd',this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                            <div class="bp-remember-row">
                                <label class="bp-remember-label"><input type="checkbox" name="remember"> {{ __('Remember Me') }}</label>
                                <a href="{{ route('tenant.user.forget.password') }}" class="bp-auth-forgot">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button type="submit" id="login_btn" class="bp-btn bp-btn-green w-100 bp-auth-submit">
                                <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
                            </button>
                            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                            <a href="{{ route('tenant.user.login.otp') }}" class="bp-btn bp-btn-outline w-100 justify-content-center bp-auth-otp-btn mt-2">{{ __('Login with OTP') }}</a>
                            @endif
                        </form>
                    </div>
                </div>
                {{-- Create Account --}}
                <div class="col-md-6">
                    <div class="bp-auth-right">
                        <h2 class="bp-auth-title">{{ __('Create Account') }}</h2>
                        <p class="bp-auth-sub">{{ __('Join for early access, loyalty rewards, and faster checkout.') }}</p>
                        <form action="{{ theme_register_store_url() }}" method="post">
                            @csrf
                            <div class="mb-3"><label class="bp-label">{{ __('Full Name') }} <span class="bp-required">*</span></label><input type="text" name="name" class="bp-input" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}"></div>
                            <div class="mb-3"><label class="bp-label">{{ __('Username') }} <span class="bp-required">*</span></label><input type="text" name="username" class="bp-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"></div>
                            <div class="mb-3"><label class="bp-label">{{ __('Email Address') }} <span class="bp-required">*</span></label><input type="email" name="email" class="bp-input" placeholder="{{ __('your@email.com') }}"></div>
                            <div class="mb-3"><label class="bp-label">{{ __('Password') }} <span class="bp-required">*</span></label><input type="password" name="password" class="bp-input" placeholder="{{ __('Min. 8 characters') }}"></div>
                            <div class="mb-3"><label class="bp-label">{{ __('Confirm Password') }} <span class="bp-required">*</span></label><input type="password" name="password_confirmation" class="bp-input" placeholder="{{ __('Repeat your password') }}"></div>
                            <div class="bp-terms-check">
                                <input type="checkbox" id="terms">
                                <label for="terms">{{ __('I agree to the') }} <a href="#">{{ __('Terms of Service') }}</a> &amp; <a href="#">{{ __('Privacy Policy') }}</a></label>
                            </div>
                            <button type="submit" id="register" class="bp-btn bp-btn-green w-100 bp-auth-submit">
                                <i class="las la-user-plus"></i> {{ __('Create Account') }}
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
})(jQuery);
</script>
<script>
function bpTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
