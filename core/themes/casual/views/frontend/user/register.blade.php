@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ __('Create Account') }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container cs-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="cs-auth-card">
                <div class="cs-auth-card-head">
                    {!! theme_logo_html('cs-auth-logo-link', 'cs-auth-logo-img') !!}
                    <h2 class="cs-auth-title">{{ __('Create Your Account') }}</h2>
                    <p class="cs-auth-sub">{{ __('Join us and enjoy faster checkout, order tracking, and exclusive offers.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="cs-auth-label">{{ __('Full Name') }} <span class="cs-required">*</span></label>
                            <input type="text" name="name" class="cs-auth-input" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="cs-auth-label">{{ __('Username') }} <span class="cs-required">*</span></label>
                            <input type="text" name="username" class="cs-auth-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="cs-auth-label">{{ __('Email Address') }} <span class="cs-required">*</span></label>
                            <input type="email" name="email" class="cs-auth-input" placeholder="{{ __('your@email.com') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="cs-auth-label">{{ __('Password') }} <span class="cs-required">*</span></label>
                            <div class="cs-auth-input-wrap">
                                <input type="password" name="password" id="cs_reg_pwd" class="cs-auth-input" placeholder="{{ __('Min. 8 characters') }}">
                                <button type="button" class="cs-auth-eye" onclick="csTogglePwd('cs_reg_pwd', this)">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="cs-auth-label">{{ __('Confirm Password') }} <span class="cs-required">*</span></label>
                            <div class="cs-auth-input-wrap">
                                <input type="password" name="password_confirmation" id="cs_reg_pwd2" class="cs-auth-input" placeholder="{{ __('Repeat your password') }}">
                                <button type="button" class="cs-auth-eye" onclick="csTogglePwd('cs_reg_pwd2', this)">
                                    <i class="las la-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="javascript:void(0)" class="cs-auth-generate-pwd mt-3">
                        <i class="las la-magic"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="cs-auth-submit-btn mt-4">
                        <i class="las la-user-plus"></i> {{ __('Create Account') }}
                    </button>
                </form>

                <p class="cs-auth-switch-note mt-4">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}" class="cs-auth-switch-link">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_generate_password_js() !!}
<script>
(function ($) {
    'use strict';

    $(document).ready(function () {

                $('form').on('submit', function () {
            
        });

        // Register — use form submit (not click) to avoid button-disabled race
        $('#register').closest('form').on('submit', function () {
            var btn = $('#register');
            btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        // Generate password
        $('.cs-auth-generate-pwd').on('click', function () {
            if (typeof generateRandomPassword === 'function') {
                var pwd = generateRandomPassword();
                $('#cs_reg_pwd').val(pwd);
                $('#cs_reg_pwd2').val(pwd);
            }
        });

        // Live state search


        // Close dropdowns on outside click
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.cs-auth-dropdown-wrap').length) {
                $('.cs-auth-dropdown').hide();
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
