@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection

@section('content')
<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('Create Account') }}</h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <span>{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:44px;box-shadow:var(--gc-shadow);">

                <div style="text-align:center;margin-bottom:32px;">
                    {!! theme_logo_html('') !!}
                    <span style="display:block;font-size:10px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--gc-rose);margin-top:16px;margin-bottom:8px;">{{ __('New Member') }}</span>
                    <h2 style="font-size:24px;font-weight:400;color:var(--gc-dark);margin:0 0 6px;font-style:italic;">{{ __('Join GoldCraft') }}</h2>
                    <p style="font-size:14px;color:var(--gc-muted);margin:0;font-style:italic;">{{ __('Discover handcrafted jewellery made with love') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                @php
                    $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;outline:none;background:#fff;transition:border-color .2s;';
                    $lbl = 'font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:6px;display:block;';
                @endphp

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Full Name') }} <span style="color:var(--gc-rose);">*</span></label>
                            <input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}"
                                   onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Username') }} <span style="color:var(--gc-rose);">*</span></label>
                            <input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"
                                   onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label style="{{ $lbl }}">{{ __('Email Address') }} <span style="color:var(--gc-rose);">*</span></label>
                        <input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}"
                               onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                    </div>

                    

                    

                    

                    

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Password') }} <span style="color:var(--gc-rose);">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="password" id="gc_reg_pwd" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Create password') }}"
                                       onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                                <button type="button" onclick="gcTogglePwd('gc_reg_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--gc-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Confirm Password') }} <span style="color:var(--gc-rose);">*</span></label>
                            <div style="position:relative;">
                                <input type="password" name="password_confirmation" id="gc_reg_pwd2" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Confirm password') }}"
                                       onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                                <button type="button" onclick="gcTogglePwd('gc_reg_pwd2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--gc-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                            </div>
                        </div>
                    </div>

                    <a class="gc-generate-pwd" href="javascript:void(0)"
                       style="font-size:12px;color:var(--gc-rose);font-style:italic;display:inline-flex;align-items:center;gap:4px;margin:16px 0;text-decoration:none;">
                        <i class="mdi mdi-auto-fix" style="font-style:normal;"></i> {{ __('Generate random password') }}
                    </a>

                    {{-- Referral / additional info toggle --}}
                    <div style="border:1.5px dashed var(--gc-border);border-radius:var(--gc-radius);padding:14px 16px;margin-bottom:16px;">
                        <label style="display:flex;align-items:center;gap:10px;cursor:pointer;font-size:13px;color:var(--gc-muted);font-style:italic;margin:0;">
                            <input type="checkbox" id="gc_has_referral" style="width:16px;height:16px;accent-color:var(--gc-rose);cursor:pointer;">
                            {{ __('I have a referral or promo code') }}
                        </label>
                        <div id="gc_referral_fields" style="display:none;margin-top:14px;padding-top:14px;border-top:1px dashed var(--gc-border);">
                            <label style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:6px;display:block;">{{ __('Referral / Promo Code') }}</label>
                            <input type="text" name="referral_code" style="{{ $inp }}" placeholder="{{ __('Enter your code') }}"
                                   onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                        </div>
                    </div>

                    <button type="submit" id="register" class="gc-btn gc-btn-primary" style="width:100%;justify-content:center;font-size:12px;">
                        {{ __('Create Account') }}
                    </button>
                </form>

                <p style="text-align:center;font-size:13px;color:var(--gc-muted);margin-top:20px;font-style:italic;">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Sign In') }}</a>
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
            $btn.html('{{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });

        $('.gc-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#gc_reg_pwd').val(pwd); $('#gc_reg_pwd2').val(pwd);
        });

        $('#gc_has_referral').on('change', function () {
            $('#gc_referral_fields').slideToggle(200);
        });

        
    });
})(jQuery);

function gcTogglePwd(id, btn) {
    var input = document.getElementById(id), icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
