@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div style="background:var(--tr-bark);padding:24px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:26px;font-weight:900;color:#fff;margin-bottom:6px;">{{ __('Create Account') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;box-shadow:var(--tr-shadow);">
                <div style="background:var(--tr-bark);padding:20px 36px;display:flex;align-items:center;gap:16px;">
                    {!! theme_logo_html('') !!}
                    <div>
                        <h2 style="font-size:20px;font-weight:900;color:#fff;margin:0;">{{ __('Join TrailCo') }}</h2>
                        <p style="font-size:13px;color:rgba(255,255,255,.6);margin:2px 0 0;">{{ __('Gear up for your next adventure') }}</p>
                    </div>
                </div>

                <div style="padding:36px;">
                    {!! theme_error_msg() !!}
                    {!! theme_flash_msg() !!}

                    @php
                        $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;font-family:inherit;outline:none;background:#fff;transition:border-color .2s;';
                        $lbl = 'font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:6px;display:block;';
                    @endphp

                    <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label style="{{ $lbl }}">{{ __('Full Name') }} <span style="color:var(--tr-terra);">*</span></label>
                                <input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}"
                                       onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $lbl }}">{{ __('Username') }} <span style="color:var(--tr-terra);">*</span></label>
                                <input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"
                                       onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label style="{{ $lbl }}">{{ __('Email Address') }} <span style="color:var(--tr-terra);">*</span></label>
                            <input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}"
                                   onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                        </div>

                        

                        

                        

                        

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <label style="{{ $lbl }}">{{ __('Password') }} <span style="color:var(--tr-terra);">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="tc_reg_pwd" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Create password') }}"
                                           onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                                    <button type="button" onclick="tcTogglePwd('tc_reg_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--tr-stone);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $lbl }}">{{ __('Confirm Password') }} <span style="color:var(--tr-terra);">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password_confirmation" id="tc_reg_pwd2" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Confirm password') }}"
                                           onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                                    <button type="button" onclick="tcTogglePwd('tc_reg_pwd2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--tr-stone);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                        </div>

                        <a class="tc-generate-pwd" href="javascript:void(0)" style="font-size:13px;color:var(--tr-olive);font-weight:700;display:inline-block;margin:16px 0;">
                            <i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}
                        </a>

                        <button type="submit" id="register" class="tr-btn tr-btn-primary" style="width:100%;justify-content:center;">
                            <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                        </button>
                    </form>

                    <p style="text-align:center;font-size:13px;color:var(--tr-stone);margin-top:20px;">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('tenant.user.login') }}" style="color:var(--tr-olive);font-weight:700;">{{ __('Sign In') }}</a>
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

        $('.tc-generate-pwd').on('click', function () { var pwd = generateRandomPassword(); $('#tc_reg_pwd').val(pwd); $('#tc_reg_pwd2').val(pwd); });
        
    });
})(jQuery);
function tcTogglePwd(id, btn) { var input = document.getElementById(id), icon = btn.querySelector('i'); if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; } else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; } }
</script>
@endsection
