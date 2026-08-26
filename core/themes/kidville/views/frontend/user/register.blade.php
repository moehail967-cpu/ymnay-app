@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection

@section('content')
<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('Create Account') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="kv-auth-card">
                <div class="kv-auth-header">
                    <div class="kv-auth-logo">
                        {!! theme_logo_html('kv-auth-logo-link', 'kv-logo-img', 'kv-brand') !!}
                    </div>
                    <h2 class="kv-auth-title">{{ __('Join Us') }}</h2>
                    <p class="kv-auth-sub">{{ __('Create your free account today') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data" class="kv-auth-form">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="kv-form-group">
                                <label class="kv-label">{{ __('Full Name') }} <span class="kv-required">*</span></label>
                                <input type="text" name="name" class="kv-input" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kv-form-group">
                                <label class="kv-label">{{ __('Username') }} <span class="kv-required">*</span></label>
                                <input type="text" name="username" class="kv-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                        </div>
                    </div>

                    <div class="kv-form-group">
                        <label class="kv-label">{{ __('Email Address') }} <span class="kv-required">*</span></label>
                        <input type="email" name="email" class="kv-input" placeholder="{{ __('your@email.com') }}">
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="kv-form-group">
                                <label class="kv-label">{{ __('Password') }} <span class="kv-required">*</span></label>
                                <div class="kv-input-wrap">
                                    <input type="password" name="password" id="kv_reg_pwd" class="kv-input" placeholder="{{ __('Create password') }}">
                                    <button type="button" class="kv-input-eye" onclick="kvTogglePwd('kv_reg_pwd', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kv-form-group">
                                <label class="kv-label">{{ __('Confirm Password') }} <span class="kv-required">*</span></label>
                                <div class="kv-input-wrap">
                                    <input type="password" name="password_confirmation" id="kv_reg_pwd2" class="kv-input" placeholder="{{ __('Confirm password') }}">
                                    <button type="button" class="kv-input-eye" onclick="kvTogglePwd('kv_reg_pwd2', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="kv-generate-pwd" href="javascript:void(0)">
                        <i class="las la-magic"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="kv-btn kv-btn-red w-100 justify-content-center mt-4" style="padding:13px;font-size:15px;">
                        {{ __('Create Account') }}
                    </button>
                </form>

                <p class="kv-auth-switch">
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
                $('form.kv-auth-form').on('submit', function () {
            
            var $btn = $('#register');
            $btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.kv-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#kv_reg_pwd').val(pwd);
            $('#kv_reg_pwd2').val(pwd);
        });


    });
})(jQuery);

function kvTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
