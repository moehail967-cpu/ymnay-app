@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div style="background:var(--pf-teal-light);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:28px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('Create Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--pf-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div style="background:#fff;border:1px solid var(--pf-border);border-radius:var(--pf-radius-xl);padding:40px;box-shadow:var(--pf-shadow);">

                <div style="text-align:center;margin-bottom:28px;">
                    {!! theme_logo_html('') !!}
                    <h2 style="font-size:22px;font-weight:700;color:var(--pf-dark);margin-top:16px;margin-bottom:4px;">{{ __('Join Pharmacy') }}</h2>
                    <p style="font-size:14px;color:var(--pf-muted);">{{ __('Create your free account and take control of your health') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;background:#fff;'; $lbl = 'font-size:13px;font-weight:600;color:var(--pf-dark);margin-bottom:6px;display:block;'; @endphp

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Full Name') }} <span style="color:var(--pf-teal);">*</span></label>
                            <input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Username') }} <span style="color:var(--pf-teal);">*</span></label>
                            <input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label style="{{ $lbl }}">{{ __('Email Address') }} <span style="color:var(--pf-teal);">*</span></label>
                        <input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}">
                    </div>

                    

                    

                    

                    

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Password') }} <span style="color:var(--pf-teal);">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="password" id="pf_reg_pwd" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Create password') }}">
                                <button type="button" onclick="pfTogglePwd('pf_reg_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--pf-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Confirm Password') }} <span style="color:var(--pf-teal);">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="password_confirmation" id="pf_reg_pwd2" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Confirm password') }}">
                                <button type="button" onclick="pfTogglePwd('pf_reg_pwd2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--pf-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                    </div>

                    <a class="pf-generate-pwd" href="javascript:void(0)" style="font-size:13px;color:var(--pf-teal);font-weight:600;display:inline-block;margin:16px 0;">
                        <i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" class="pf-btn pf-btn-teal w-100 justify-content-center" style="font-size:15px;padding:13px;">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                    </button>
                </form>

                <p style="text-align:center;font-size:13px;color:var(--pf-muted);margin-top:20px;">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Sign In') }}</a>
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
            $btn.html('<i class="mdi mdi-account-plus-outline"></i> {{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.pf-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#pf_reg_pwd').val(pwd); $('#pf_reg_pwd2').val(pwd);
        });

        
    });
})(jQuery);

function pfTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
