@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection

@section('content')
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="lg-auth-card">
                <div class="lg-auth-header">
                    {!! theme_logo_html('lg-auth-logo') !!}
                    <h2 class="lg-auth-title">{{ __('Join Us') }}</h2>
                    <p class="lg-auth-sub">{{ __('Create your free account today') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data" class="lg-auth-form">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="lg-form-group">
                                <label class="lg-form-label">{{ __('Full Name') }} <span class="lg-required">*</span></label>
                                <input type="text" name="name" class="lg-form-control" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="lg-form-group">
                                <label class="lg-form-label">{{ __('Username') }} <span class="lg-required">*</span></label>
                                <input type="text" name="username" class="lg-form-control" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                        </div>
                    </div>

                    <div class="lg-form-group">
                        <label class="lg-form-label">{{ __('Email Address') }} <span class="lg-required">*</span></label>
                        <input type="email" name="email" class="lg-form-control" placeholder="{{ __('your@email.com') }}">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="lg-form-group">
                                <label class="lg-form-label">{{ __('Password') }} <span class="lg-required">*</span></label>
                                <div class="lg-input-wrap">
                                    <input type="password" name="password" id="lg_reg_pwd" class="lg-form-control" placeholder="{{ __('Create password') }}">
                                    <button type="button" class="lg-input-eye" onclick="lgTogglePwd('lg_reg_pwd', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="lg-form-group">
                                <label class="lg-form-label">{{ __('Confirm Password') }} <span class="lg-required">*</span></label>
                                <div class="lg-input-wrap">
                                    <input type="password" name="password_confirmation" id="lg_reg_pwd2" class="lg-form-control" placeholder="{{ __('Confirm password') }}">
                                    <button type="button" class="lg-input-eye" onclick="lgTogglePwd('lg_reg_pwd2', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="lg-generate-pwd" href="javascript:void(0)">
                        <i class="las la-magic"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="lx-btn lx-btn-primary w-100 justify-content-center mt-4" style="padding:13px;font-size:15px;">
                        {{ __('Create Account') }}
                    </button>
                </form>

                <p class="lg-auth-switch">
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
                $('form.lg-auth-form').on('submit', function () {
            
            var $btn = $('#register');
            $btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.lg-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#lg_reg_pwd').val(pwd);
            $('#lg_reg_pwd2').val(pwd);
        });

        // Live state search

    });
})(jQuery);

function lgTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
