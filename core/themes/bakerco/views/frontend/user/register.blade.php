@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
{{-- Page Banner --}}
<div class="bk-page-banner">
    <div class="container">
        <h1>{{ __('Create Account') }}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container bk-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="bk-auth-card">
                <div class="bk-auth-centered">
                    {!! theme_logo_html('bk-auth-logo') !!}
                    <h2 class="bk-auth-title bk-auth-logo-top">{{ __('Join BakerCo') }}</h2>
                    <p class="bk-auth-sub">{{ __('Create your free account today') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bk-label">{{ __('Full Name') }} <span class="bk-required">*</span></label>
                                <input type="text" name="name" class="bk-input" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bk-label">{{ __('Username') }} <span class="bk-required">*</span></label>
                                <input type="text" name="username" class="bk-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="bk-label">{{ __('Email Address') }} <span class="bk-required">*</span></label>
                        <input type="email" name="email" class="bk-input" placeholder="{{ __('your@email.com') }}">
                    </div>

                    

                    

                    

                    

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bk-label">{{ __('Password') }} <span class="bk-required">*</span></label>
                                <div class="bk-input-wrap">
                                    <input type="password" name="password" id="bk_reg_pwd" class="bk-input" placeholder="{{ __('Create password') }}">
                                    <button type="button" class="bk-input-eye" onclick="bkTogglePwd('bk_reg_pwd', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bk-label">{{ __('Confirm Password') }} <span class="bk-required">*</span></label>
                                <div class="bk-input-wrap">
                                    <input type="password" name="password_confirmation" id="bk_reg_pwd2" class="bk-input" placeholder="{{ __('Confirm password') }}">
                                    <button type="button" class="bk-input-eye" onclick="bkTogglePwd('bk_reg_pwd2', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="bk-generate-pwd bk-auth-generate" href="javascript:void(0)">
                        <i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="bk-btn bk-btn-rose w-100 justify-content-center bk-auth-register-submit">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                    </button>
                </form>

                <p class="bk-auth-switch">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}">{{ __('Sign In') }}</a>
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

        // Register — form submit handler (not click) avoids button-disabled race condition
        $('#register').closest('form').on('submit', function () {
            var btn = $('#register');
            btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.bk-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#bk_reg_pwd').val(pwd);
            $('#bk_reg_pwd2').val(pwd);
        });

        // Live state search
        
    });
})(jQuery);

function bkTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
