@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('Create Account') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container bp-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="bp-auth-card">
                <div class="bp-auth-centered">
                    {!! theme_logo_html('bp-auth-logo') !!}
                    <h2 class="bp-auth-title bp-auth-logo-top">{{ __('Join BookPoint') }}</h2>
                    <p class="bp-auth-sub">{{ __('Create your free account today') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bp-label">{{ __('Full Name') }} <span class="bp-required">*</span></label>
                                <input type="text" name="name" class="bp-input" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bp-label">{{ __('Username') }} <span class="bp-required">*</span></label>
                                <input type="text" name="username" class="bp-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="bp-label">{{ __('Email Address') }} <span class="bp-required">*</span></label>
                        <input type="email" name="email" class="bp-input" placeholder="{{ __('your@email.com') }}">
                    </div>

                    

                    

                    

                    

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bp-label">{{ __('Password') }} <span class="bp-required">*</span></label>
                                <div class="bp-input-wrap">
                                    <input type="password" name="password" id="bp_reg_pwd" class="bp-input" placeholder="{{ __('Create password') }}">
                                    <button type="button" class="bp-input-eye" onclick="bpTogglePwd('bp_reg_pwd', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="bp-label">{{ __('Confirm Password') }} <span class="bp-required">*</span></label>
                                <div class="bp-input-wrap">
                                    <input type="password" name="password_confirmation" id="bp_reg_pwd2" class="bp-input" placeholder="{{ __('Confirm password') }}">
                                    <button type="button" class="bp-input-eye" onclick="bpTogglePwd('bp_reg_pwd2', this)"><i class="las la-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a class="bp-generate-pwd bp-auth-generate" href="javascript:void(0)">
                        <i class="las la-magic"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="bp-btn bp-btn-green w-100 justify-content-center bp-auth-register-submit">
                        <i class="las la-user-plus"></i> {{ __('Create Account') }}
                    </button>
                </form>

                <p class="bp-auth-switch">
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

        $('#register').closest('form').on('submit', function () {
            var btn = $('#register');
            btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.bp-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#bp_reg_pwd').val(pwd);
            $('#bp_reg_pwd2').val(pwd);
        });

        
    });
})(jQuery);

function bpTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
