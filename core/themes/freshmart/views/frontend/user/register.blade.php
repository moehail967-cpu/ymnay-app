@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ __('Create Account') }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="fm-auth-card">
                <div class="fm-auth-header">
                    {!! theme_logo_html('fm-auth-logo-link', 'fm-auth-logo') !!}
                    <h2 class="fm-auth-title mt-3">{{ __('Join Us') }}</h2>
                    <p class="fm-auth-sub">{{ __('Create your free account today') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data" class="fm-auth-form">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fm-form-group">
                                <label class="fm-label">{{ __('Full Name') }} <span class="fm-required">*</span></label>
                                <input type="text" name="name" class="fm-input" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fm-form-group">
                                <label class="fm-label">{{ __('Username') }} <span class="fm-required">*</span></label>
                                <input type="text" name="username" class="fm-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                        </div>
                    </div>

                    <div class="fm-form-group">
                        <label class="fm-label">{{ __('Email Address') }} <span class="fm-required">*</span></label>
                        <input type="email" name="email" class="fm-input" placeholder="{{ __('your@email.com') }}">
                    </div>

                    

                    

                    

                    

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fm-form-group">
                                <label class="fm-label">{{ __('Password') }} <span class="fm-required">*</span></label>
                                <div class="fm-input-wrap">
                                    <input type="password" name="password" id="fm_reg_pwd" class="fm-input" placeholder="{{ __('Create password') }}" style="padding-right:44px;">
                                    <button type="button" class="fm-input-eye" onclick="fmTogglePwd('fm_reg_pwd', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fm-form-group">
                                <label class="fm-label">{{ __('Confirm Password') }} <span class="fm-required">*</span></label>
                                <div class="fm-input-wrap">
                                    <input type="password" name="password_confirmation" id="fm_reg_pwd2" class="fm-input" placeholder="{{ __('Confirm password') }}" style="padding-right:44px;">
                                    <button type="button" class="fm-input-eye" onclick="fmTogglePwd('fm_reg_pwd2', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="fm-generate-pwd" href="javascript:void(0)">
                        <i class="las la-magic"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="fm-btn fm-btn-green w-100 justify-content-center mt-4" style="padding:13px;font-size:15px;">
                        {{ __('Create Account') }}
                    </button>
                </form>

                <p class="fm-auth-switch">
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
                // (form.on('submit') avoids button-disabled race condition that blocks HTML form POST)
        $('form.fm-auth-form').on('submit', function () {
            
            var $btn = $('#register');
            $btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.fm-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#fm_reg_pwd').val(pwd);
            $('#fm_reg_pwd2').val(pwd);
        });

        
    });
})(jQuery);

function fmTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
