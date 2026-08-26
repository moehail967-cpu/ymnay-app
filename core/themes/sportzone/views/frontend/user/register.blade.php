@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ __('Create Account') }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;box-shadow:var(--sz-shadow-sm);">
                <div style="background:var(--sz-navy);padding:20px 36px;display:flex;align-items:center;gap:14px;">
                    {!! theme_logo_html('') !!}
                    <div>
                        <h2 style="font-family:var(--sz-font-head);font-size:20px;font-weight:700;color:#fff;margin:0;text-transform:uppercase;letter-spacing:2px;">{{ __('Join SportZone') }}</h2>
                        <p style="font-size:13px;color:rgba(255,255,255,.6);margin:2px 0 0;">{{ __('Gear up with exclusive athlete benefits') }}</p>
                    </div>
                </div>

                <div style="padding:36px;">
                    {!! theme_error_msg() !!}
                    {!! theme_flash_msg() !!}

                    @php
                        $inp = 'width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:14px;font-family:var(--sz-font-body);outline:none;background:#fff;transition:border-color .2s;';
                        $lbl = 'font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;';
                    @endphp

                    <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label style="{{ $lbl }}">{{ __('Full Name') }} <span style="color:var(--sz-red);">*</span></label>
                                <input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}"
                                       onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $lbl }}">{{ __('Username') }} <span style="color:var(--sz-red);">*</span></label>
                                <input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"
                                       onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label style="{{ $lbl }}">{{ __('Email Address') }} <span style="color:var(--sz-red);">*</span></label>
                            <input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}"
                                   onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                        </div>

                        

                        

                        

                        

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <label style="{{ $lbl }}">{{ __('Password') }} <span style="color:var(--sz-red);">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="sz_reg_pwd" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Create password') }}"
                                           onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                                    <button type="button" onclick="szTogglePwd('sz_reg_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--sz-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $lbl }}">{{ __('Confirm Password') }} <span style="color:var(--sz-red);">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password_confirmation" id="sz_reg_pwd2" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Confirm password') }}"
                                           onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                                    <button type="button" onclick="szTogglePwd('sz_reg_pwd2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--sz-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>

                        <a class="sz-generate-pwd" href="javascript:void(0)" style="font-size:12px;color:var(--sz-red);font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:1px;display:inline-block;margin:16px 0;">
                            <i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}
                        </a>

                        <button type="submit" id="register" class="sz-btn sz-btn-red w-100" style="justify-content:center;font-size:14px;">
                            <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                        </button>
                    </form>

                    <p style="text-align:center;font-size:13px;color:var(--sz-muted);margin-top:20px;">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('tenant.user.login') }}" style="color:var(--sz-red);font-weight:600;font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:.5px;">{{ __('Sign In') }}</a>
                    </p>
                </div>
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

        $('.sz-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#sz_reg_pwd').val(pwd); $('#sz_reg_pwd2').val(pwd);
        });

        
    });
})(jQuery);

function szTogglePwd(id, btn) {
    var input = document.getElementById(id), icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
