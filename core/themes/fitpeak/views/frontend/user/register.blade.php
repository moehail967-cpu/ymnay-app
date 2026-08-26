@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{{ __('Create Account') }}</h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li>{{ __('Register') }}</li>
        </ul>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="fp-auth-card">
                <div class="fp-auth-header">
                    {!! theme_logo_html('fp-auth-logo') !!}
                    <h2 class="fp-auth-title">{{ __('Join the Squad') }}</h2>
                    <p class="fp-auth-sub">{{ __('Create your free account and start training smarter') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data" class="fp-auth-form">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fp-form-group">
                                <label class="fp-auth-label">{{ __('Full Name') }} <span class="fp-required">*</span></label>
                                <input type="text" name="name" class="fp-auth-input" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fp-form-group">
                                <label class="fp-auth-label">{{ __('Username') }} <span class="fp-required">*</span></label>
                                <input type="text" name="username" class="fp-auth-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                        </div>
                    </div>

                    <div class="fp-form-group">
                        <label class="fp-auth-label">{{ __('Email Address') }} <span class="fp-required">*</span></label>
                        <input type="email" name="email" class="fp-auth-input" placeholder="{{ __('your@email.com') }}">
                    </div>

                    

                    

                    

                    

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fp-form-group">
                                <label class="fp-auth-label">{{ __('Password') }} <span class="fp-required">*</span></label>
                                <div class="fp-input-wrap">
                                    <input type="password" name="password" id="fp_reg_pwd" class="fp-auth-input" placeholder="{{ __('Create password') }}">
                                    <button type="button" class="fp-input-eye" onclick="fpTogglePwd('fp_reg_pwd', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="fp-form-group">
                                <label class="fp-auth-label">{{ __('Confirm Password') }} <span class="fp-required">*</span></label>
                                <div class="fp-input-wrap">
                                    <input type="password" name="password_confirmation" id="fp_reg_pwd2" class="fp-auth-input" placeholder="{{ __('Confirm password') }}">
                                    <button type="button" class="fp-input-eye" onclick="fpTogglePwd('fp_reg_pwd2', this)"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="fp-generate-pwd" href="javascript:void(0)" style="display:inline-block;font-size:12px;color:var(--fp-green);margin-bottom:16px;font-weight:700;text-transform:uppercase;letter-spacing:1px;">
                        <i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="fp-btn fp-btn-primary w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                    </button>
                </form>

                <p class="fp-auth-switch">
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
                $('form.fp-auth-form').on('submit', function () {
            
            var $btn = $('#register');
            $btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.fp-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#fp_reg_pwd').val(pwd);
            $('#fp_reg_pwd2').val(pwd);
        });

        // Live state search
        
    });
})(jQuery);

function fpTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
