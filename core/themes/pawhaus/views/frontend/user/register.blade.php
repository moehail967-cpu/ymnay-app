@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div class="ph-page-banner">
    <div class="container">
        <h1>{{ __('Create Account') }}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="ph-auth-card">
                <div class="ph-auth-header">
                    {!! theme_logo_html('ph-auth-logo') !!}
                    <h2 class="ph-auth-title">{{ __('Join Us') }}</h2>
                    <p class="ph-auth-sub">{{ __('Create your free account today') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data" class="ph-auth-form">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="ph-form-group">
                                <label class="ph-label">{{ __('Full Name') }} <span class="ph-required">*</span></label>
                                <input type="text" name="name" class="ph-input" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ph-form-group">
                                <label class="ph-label">{{ __('Username') }} <span class="ph-required">*</span></label>
                                <input type="text" name="username" class="ph-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                        </div>
                    </div>

                    <div class="ph-form-group">
                        <label class="ph-label">{{ __('Email Address') }} <span class="ph-required">*</span></label>
                        <input type="email" name="email" class="ph-input" placeholder="{{ __('your@email.com') }}">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="ph-form-group">
                                <label class="ph-label">{{ __('Password') }} <span class="ph-required">*</span></label>
                                <div class="ph-input-wrap">
                                    <input type="password" name="password" id="ph_reg_pwd" class="ph-input" placeholder="{{ __('Create password') }}">
                                    <button type="button" class="ph-input-eye" onclick="phTogglePwd('ph_reg_pwd', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="ph-form-group">
                                <label class="ph-label">{{ __('Confirm Password') }} <span class="ph-required">*</span></label>
                                <div class="ph-input-wrap">
                                    <input type="password" name="password_confirmation" id="ph_reg_pwd2" class="ph-input" placeholder="{{ __('Confirm password') }}">
                                    <button type="button" class="ph-input-eye" onclick="phTogglePwd('ph_reg_pwd2', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="ph-generate-pwd" href="javascript:void(0)">
                        <i class="las la-magic"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="ph-btn ph-btn-terra w-100 justify-content-center mt-4" style="padding:13px;font-size:15px;">
                        {{ __('Create Account') }}
                    </button>
                </form>

                <p class="ph-auth-switch">
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
        $('.ph-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#ph_reg_pwd').val(pwd);
            $('#ph_reg_pwd2').val(pwd);
        });



                $('form.ph-auth-form').on('submit', function () {
            
            var $btn = $('#register');
            $btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });
    });
})(jQuery);

function phTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
