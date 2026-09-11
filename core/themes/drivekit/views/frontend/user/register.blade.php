@extends('tenant.frontend.frontend-page-master')

@section('title') {{ theme_site_name() }} — {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
{{-- Page Banner --}}
<div style="background:var(--dk-carbon);border-bottom:2px solid rgba(229,48,48,.25);padding:28px 0;position:relative;overflow:hidden;">
    <div style="position:absolute;inset:0;background-image:repeating-linear-gradient(90deg,rgba(255,255,255,.012) 0,rgba(255,255,255,.012) 1px,transparent 1px,transparent 60px);pointer-events:none;"></div>
    <div style="position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(to bottom,transparent,var(--dk-red),transparent);"></div>
    <div class="container" style="position:relative;z-index:1;">
        <h1 style="font-size:clamp(18px,2.5vw,26px);font-weight:900;color:var(--dk-white);text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;">{{ __('Create Account') }}</h1>
        <nav style="display:flex;align-items:center;gap:6px;font-size:12px;">
            <a href="{{ theme_home_url() }}" style="color:var(--dk-silver);text-decoration:none;font-weight:600;" onmouseover="this.style.color='var(--dk-red)'" onmouseout="this.style.color='var(--dk-silver)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="color:var(--dk-border);font-size:14px;"></i>
            <span style="color:var(--dk-white);font-weight:600;">{{ __('Register') }}</span>
        </nav>
    </div>
</div>

<div class="container" style="padding:56px 0 72px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div style="background:var(--dk-surface);border:1px solid var(--dk-border);border-radius:var(--dk-radius);padding:40px;position:relative;overflow:hidden;">
                <div style="position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(to right,var(--dk-red),var(--dk-orange));"></div>

                <div style="text-align:center;margin-bottom:28px;">
                    {!! theme_logo_html('') !!}
                    <h2 style="font-size:22px;font-weight:900;color:var(--dk-white);margin-top:16px;margin-bottom:4px;text-transform:uppercase;letter-spacing:-0.5px;">
                        {{ theme_register_page_headline() }}
                    </h2>
                    <p style="font-size:13px;color:var(--dk-silver);">{{ theme_register_page_subtitle() }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                @php
                    $inp = 'width:100%;padding:10px 14px;background:var(--dk-panel);border:1.5px solid var(--dk-border);border-radius:var(--dk-radius);color:var(--dk-white);font-size:14px;outline:none;transition:border-color .2s;';
                    $lbl = 'font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--dk-silver);display:block;margin-bottom:6px;';
                    $focus = "onfocus=\"this.style.borderColor='var(--dk-red)'\" onblur=\"this.style.borderColor='var(--dk-border)'\"";
                @endphp

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Full Name') }} *</label>
                            <input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}" onfocus="this.style.borderColor='var(--dk-red)'" onblur="this.style.borderColor='var(--dk-border)'">
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Username') }} *</label>
                            <input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}" onfocus="this.style.borderColor='var(--dk-red)'" onblur="this.style.borderColor='var(--dk-border)'">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label style="{{ $lbl }}">{{ __('Email Address') }} *</label>
                        <input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}" onfocus="this.style.borderColor='var(--dk-red)'" onblur="this.style.borderColor='var(--dk-border)'">
                    </div>
                    
                    
                    
                    
                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Password') }} *</label>
                            <div style="position:relative;">
                                <input type="password" name="password" id="dk_reg_pwd" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Create password') }}" onfocus="this.style.borderColor='var(--dk-red)'" onblur="this.style.borderColor='var(--dk-border)'">
                                <button type="button" onclick="dkTogglePwd('dk_reg_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--dk-silver);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Confirm Password') }} *</label>
                            <div style="position:relative;">
                                <input type="password" name="password_confirmation" id="dk_reg_pwd2" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Confirm') }}" onfocus="this.style.borderColor='var(--dk-red)'" onblur="this.style.borderColor='var(--dk-border)'">
                                <button type="button" onclick="dkTogglePwd('dk_reg_pwd2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--dk-silver);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                    </div>
                    <a class="dk-generate-pwd" href="javascript:void(0)" style="font-size:12px;color:var(--dk-red);display:inline-flex;align-items:center;gap:4px;margin:16px 0;text-decoration:none;font-weight:600;">
                        <i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}
                    </a>

                    <button type="submit" id="register" style="width:100%;background:var(--dk-red);color:#fff;border:0;padding:13px;border-radius:var(--dk-radius);font-size:12px;font-weight:800;cursor:pointer;font-family:inherit;text-transform:uppercase;letter-spacing:1px;transition:background .2s;display:flex;align-items:center;justify-content:center;gap:8px;" onmouseover="this.style.background='var(--dk-red-d)'" onmouseout="this.style.background='var(--dk-red)'">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                    </button>
                </form>

                <p style="text-align:center;font-size:13px;color:var(--dk-silver);margin-top:20px;">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--dk-red);text-decoration:none;font-weight:700;">{{ __('Sign In') }}</a>
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

        // Show loading on form submit (not on click — disabling on click blocks submission in Chrome/Firefox)
        $('#register').closest('form').on('submit', function () {
            var btn = $('#register');
            btn.html('<i class="las la-spinner la-spin"></i> {{ __('Please Wait…') }}');
            btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.dk-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#dk_reg_pwd').val(pwd);
            $('#dk_reg_pwd2').val(pwd);
        });

        

        $(document).on('click', function (e) {
            if (!$(e.target).closest('.live-state-input, .state-dropdown').length) { $('.state-dropdown').hide(); }
            if (!$(e.target).closest('.live-city-input, .city-dropdown').length) { $('.city-dropdown').hide(); }
        });
    });
})(jQuery);

function dkTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
