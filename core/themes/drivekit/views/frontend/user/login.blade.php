@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_site_name() }} — {{ __('Sign In') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')

{{-- Page Banner --}}
<div class="dk-page-banner">
    <div class="dk-page-banner-overlay"></div>
    <div class="dk-page-banner-bar"></div>
    <div class="container dk-page-banner-inner">
        <h1 class="dk-page-banner-title">{{ __('My Account') }}</h1>
        <nav class="dk-page-banner-nav">
            <a href="{{ theme_home_url() }}" class="dk-page-banner-link">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right dk-page-banner-sep"></i>
            <span class="dk-page-banner-current">{{ __('Sign In') }}</span>
        </nav>
    </div>
</div>

<div class="container dk-auth-wrap">

    <div class="dk-auth-intro">
        <span class="dk-section-tag">⚙️ {{ __('Member Area') }}</span>
        <h2 class="dk-auth-intro-title">{{ theme_login_page_headline() }}</h2>
    </div>

    {!! theme_error_msg() !!}
    {!! theme_flash_msg() !!}

    <div class="dk-auth-card">
        <div class="dk-auth-accent-bar"></div>
        <div class="row g-0">

            {{-- Sign In --}}
            <div class="col-md-6">
                <div class="dk-auth-col dk-auth-col-left">
                    <div class="dk-auth-heading">{{ __('Sign In') }}</div>
                    <p class="dk-auth-subtext">{{ theme_login_page_subtitle() }}</p>

                    <form action="" method="post" id="login_form_order_page">
                        @csrf
                        <div class="dk-input-wrap">
                            <label class="dk-input-label">{{ __('Email or Username') }}</label>
                            <div style="position:relative;">
                                <i class="mdi mdi-email-outline dk-input-icon"></i>
                                <input type="text" name="username" class="dk-input with-icon" placeholder="{{ __('your@email.com') }}">
                            </div>
                        </div>

                        <div class="dk-input-wrap">
                            <label class="dk-input-label">{{ __('Password') }}</label>
                            <div style="position:relative;">
                                <i class="mdi mdi-lock-outline dk-input-icon"></i>
                                <input type="password" name="password" id="dk_login_pwd" class="dk-input with-icon" placeholder="••••••••" style="padding-right:42px;">
                                <button type="button" onclick="dkTogglePwd('dk_login_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--dk-muted);font-size:16px;padding:0;">
                                    <i class="mdi mdi-eye-outline"></i>
                                </button>
                            </div>
                        </div>

                        <div class="dk-remember-row">
                            <label class="dk-remember-label">
                                <input type="checkbox" name="remember"> {{ __('Remember Me') }}
                            </label>
                            <a href="{{ route('tenant.user.forget.password') }}" class="dk-forgot-link">{{ __('Forgot Password?') }}</a>
                        </div>

                        <button type="submit" id="login_btn" class="dk-btn dk-btn-red w-100" style="justify-content:center;padding:13px;font-size:15px;">
                            <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                        </button>

                        @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                        <a href="{{ route('tenant.user.login.otp') }}" style="display:block;text-align:center;margin-top:12px;font-size:13px;color:var(--dk-red);text-decoration:none;">{{ __('Login with OTP') }}</a>
                        @endif
                    </form>

                </div>
            </div>

            {{-- Create Account --}}
            <div class="col-md-6">
                <div class="dk-auth-col dk-auth-col-right">
                    <div class="dk-auth-heading">{{ __('Create Account') }}</div>
                    <p class="dk-auth-subtext">{{ theme_register_page_subtitle() }}</p>

                    <div class="dk-register-highlight">
                        <p>🔧 {{ theme_register_page_promo_text() }}</p>
                    </div>

                    <ul class="dk-register-features">
                        @foreach(theme_login_page_features() as $feature)
                        <li><i class="mdi mdi-check-circle-outline"></i> {{ $feature }}</li>
                        @endforeach
                    </ul>

                    <a href="{{ route('tenant.user.register') }}" class="dk-btn dk-btn-outline w-100" style="justify-content:center;padding:13px;font-size:15px;">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Free Account') }}
                    </a>

                    <div class="dk-terms-note">
                        {{ __('By signing up you agree to our') }}
                        <a href="#">{{ __('Terms of Service') }}</a> {{ __('and') }} <a href="#">{{ __('Privacy Policy') }}</a>.
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
        var form = $('#login_form_order_page');
        var btn  = $(this);
        var username = form.find('input[name="username"]').val();
        var password = form.find('input[name="password"]').val();
        var remember = form.find('input[name="remember"]').is(':checked') ? 1 : 0;

        btn.html('<i class="las la-spinner la-spin"></i> {{ __('Please Wait…') }}').prop('disabled', true);

        $.ajax({
            method: 'POST',
            url: '{{ theme_ajax_login_url() }}',
            data: { _token: '{{ csrf_token() }}', username: username, password: password, remember: remember },
            success: function (data) {
                if (data.status === 'invalid') {
                    btn.html('<i class="mdi mdi-login"></i> {{ __('Sign In') }}').prop('disabled', false);
                    toastr.error(data.msg || '{{ __('Invalid credentials.') }}');
                } else {
                    btn.html('<i class="mdi mdi-check"></i> {{ __('Redirecting…') }}');
                    location.reload();
                }
            },
            error: function (xhr) {
                btn.html('<i class="mdi mdi-login"></i> {{ __('Sign In') }}').prop('disabled', false);
                var msg = '{{ __('Login failed. Please try again.') }}';
                try { msg = xhr.responseJSON.message || msg; } catch (err) {}
                toastr.error(msg);
            }
        });
    });
})(jQuery);

function dkTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
