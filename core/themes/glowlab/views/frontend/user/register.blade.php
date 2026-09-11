@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ __('Create Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:44px;box-shadow:var(--gl-shadow);">

                <div style="text-align:center;margin-bottom:32px;">
                    {!! theme_logo_html('') !!}
                    <h2 style="font-size:22px;font-weight:300;color:var(--gl-dark);margin-top:18px;margin-bottom:6px;letter-spacing:-.3px;">{{ __('Join GlowLab') }}</h2>
                    <p style="font-size:14px;color:var(--gl-muted);">{{ __('Discover skincare tailored to your glow journey') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                @php
                    $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:14px;font-family:inherit;outline:none;background:#fff;transition:border-color .2s;';
                    $lbl = 'font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;';
                @endphp

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Full Name') }} <span style="color:var(--gl-gold);">*</span></label>
                            <input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}"
                                   onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Username') }} <span style="color:var(--gl-gold);">*</span></label>
                            <input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"
                                   onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label style="{{ $lbl }}">{{ __('Email Address') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}"
                               onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                    </div>

                    

                    

                    

                    

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Password') }} <span style="color:var(--gl-gold);">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="password" id="gl_reg_pwd" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Create password') }}"
                                       onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                                <button type="button" onclick="glTogglePwd('gl_reg_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--gl-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Confirm Password') }} <span style="color:var(--gl-gold);">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="password_confirmation" id="gl_reg_pwd2" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Confirm password') }}"
                                       onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                                <button type="button" onclick="glTogglePwd('gl_reg_pwd2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--gl-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                    </div>

                    <a class="gl-generate-pwd" href="javascript:void(0)"
                       style="font-size:12px;color:var(--gl-gold);font-weight:600;display:inline-flex;align-items:center;gap:4px;margin:16px 0;text-decoration:none;">
                        <i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register"
                            style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                    </button>
                </form>

                <p style="text-align:center;font-size:13px;color:var(--gl-muted);margin-top:20px;">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Sign In') }}</a>
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

        $('.gl-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#gl_reg_pwd').val(pwd); $('#gl_reg_pwd2').val(pwd);
        });

        
    });
})(jQuery);

function glTogglePwd(id, btn) {
    var input = document.getElementById(id), icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
