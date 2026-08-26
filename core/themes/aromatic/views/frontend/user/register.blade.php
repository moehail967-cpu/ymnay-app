@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
{{-- Page Banner --}}
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('Create Account') }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container ar-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="ar-auth-card">
                <div class="ar-auth-centered">
                    {!! theme_logo_html('ar-auth-logo') !!}
                    <h2 class="ar-auth-title ar-auth-logo-top">{{ __('Join Aromatic') }}</h2>
                    <p class="ar-auth-sub">{{ __('Create your free account today and explore luxury fragrances') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="ar-auth-label">{{ __('Full Name') }} <span class="ar-required">*</span></label>
                                <input type="text" name="name" class="ar-auth-input" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="ar-auth-label">{{ __('Username') }} <span class="ar-required">*</span></label>
                                <input type="text" name="username" class="ar-auth-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="ar-auth-label">{{ __('Email Address') }} <span class="ar-required">*</span></label>
                        <input type="email" name="email" class="ar-auth-input" placeholder="{{ __('your@email.com') }}">
                    </div>

                    

                    

                    

                    

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="ar-auth-label">{{ __('Password') }} <span class="ar-required">*</span></label>
                                <div class="ar-input-wrap">
                                    <input type="password" name="password" id="ar_reg_pwd" class="ar-auth-input" placeholder="{{ __('Create password') }}">
                                    <button type="button" class="ar-input-eye" onclick="arTogglePwd('ar_reg_pwd', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="ar-auth-label">{{ __('Confirm Password') }} <span class="ar-required">*</span></label>
                                <div class="ar-input-wrap">
                                    <input type="password" name="password_confirmation" id="ar_reg_pwd2" class="ar-auth-input" placeholder="{{ __('Confirm password') }}">
                                    <button type="button" class="ar-input-eye" onclick="arTogglePwd('ar_reg_pwd2', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="ar-generate-pwd" href="javascript:void(0)">
                        <i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="ar-btn ar-btn-red w-100 justify-content-center">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                    </button>
                </form>

                <p class="ar-auth-switch">
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

        // Submit handler — shows loading state
        $('#register').closest('form').on('submit', function () {
            var btn = $('#register');
            btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.ar-generate-pwd').on('click', function () {
            if (typeof generateRandomPassword === 'function') {
                var pwd = generateRandomPassword();
                $('#ar_reg_pwd').val(pwd);
                $('#ar_reg_pwd2').val(pwd);
            }
        });

        // Live state search
        
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
