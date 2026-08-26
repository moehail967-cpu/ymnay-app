@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div class="mc-auth-hero">
    <div class="container">
        <h2>{{ __('Create Account') }}</h2>

    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="mc-auth-card">

                <div style="text-align:center;margin-bottom:28px;">
                    {!! theme_logo_html('') !!}
                    <h2 style="font-size:22px;font-weight:700;color:#1a1a1a;margin-top:16px;margin-bottom:4px;">{{ __('Join Us Today') }}</h2>
                    <p style="font-size:14px;color:#888;">{{ __('Create your free account and start shopping') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="mc-form-label">{{ __('Full Name') }} <span class="mc-form-required">*</span></label>
                            <input type="text" name="name" class="mc-form-input" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="mc-form-label">{{ __('Username') }} <span class="mc-form-required">*</span></label>
                            <input type="text" name="username" class="mc-form-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                        </div>
                        <div class="col-12">
                            <label class="mc-form-label">{{ __('Email Address') }} <span class="mc-form-required">*</span></label>
                            <input type="email" name="email" class="mc-form-input" placeholder="{{ __('your@email.com') }}">
                        </div>
                        
                        
                        
                        
                        
                        <div class="col-md-6">
                            <label class="mc-form-label">{{ __('Password') }} <span class="mc-form-required">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="password" id="el_reg_pwd" class="mc-form-input" placeholder="{{ __('Create password') }}" style="padding-right:42px;">
                                <button type="button" onclick="elTogglePwd('el_reg_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:#888;font-size:16px;padding:0;"><i class="las la-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="mc-form-label">{{ __('Confirm Password') }} <span class="mc-form-required">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="password_confirmation" id="el_reg_pwd2" class="mc-form-input" placeholder="{{ __('Confirm password') }}" style="padding-right:42px;">
                                <button type="button" onclick="elTogglePwd('el_reg_pwd2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:#888;font-size:16px;padding:0;"><i class="las la-eye"></i></button>
                            </div>
                        </div>
                        <div class="col-12">
                            <a class="mc-generate-pwd" href="javascript:void(0)" style="font-size:13px;color:#1A85ED;font-weight:600;display:inline-block;margin-bottom:4px;">
                                <i class="las la-magic"></i> {{ __('Generate random password') }}
                            </a>
                        </div>
                        <div class="col-12">
                            <button type="submit" id="register" class="mc-btn mc-btn-primary mc-btn-block" style="font-size:15px;padding:13px;">
                                <i class="las la-user-plus"></i> {{ __('Create Account') }}
                            </button>
                        </div>
                    </div>
                </form>

                <p style="text-align:center;font-size:13px;color:#888;margin-top:20px;">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:#1A85ED;font-weight:600;">{{ __('Sign In') }}</a>
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
            
            var $btn = $('#register');
            $btn.html('<i class="las la-spinner la-spin"></i> {{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.mc-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#el_reg_pwd').val(pwd); $('#el_reg_pwd2').val(pwd);
        });

        
    });
})(jQuery);

function elTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
