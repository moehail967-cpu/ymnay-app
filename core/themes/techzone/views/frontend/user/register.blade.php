@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div style="background:var(--tz-surface);border-bottom:1px solid var(--tz-border);padding:28px 0 20px;">
    <div class="container">
        <h2 style="font-size:20px;font-weight:700;color:#fff;margin-bottom:8px;">{{ __('Create Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--tz-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--tz-muted);text-decoration:none;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-muted)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:56px 0 72px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:40px;">
                <div style="text-align:center;margin-bottom:28px;">
                    {!! theme_logo_html('') !!}
                    <h2 style="font-size:20px;font-weight:700;color:#fff;margin-top:16px;margin-bottom:4px;">{{ __('Join TechZone') }}</h2>
                    <p style="font-size:13px;color:var(--tz-muted);">{{ __('Get access to exclusive deals, order tracking, and member benefits') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                @php
                    $inp = 'width:100%;padding:10px 14px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:14px;font-family:var(--tz-font);outline:none;';
                    $lbl = 'font-size:12px;font-weight:600;color:var(--tz-muted);display:block;margin-bottom:6px;';
                @endphp

                <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6"><label style="{{ $lbl }}">{{ __('Full Name') }} *</label><input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}"></div>
                        <div class="col-md-6"><label style="{{ $lbl }}">{{ __('Username') }} *</label><input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"></div>
                    </div>
                    <div class="mt-3"><label style="{{ $lbl }}">{{ __('Email Address') }} *</label><input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}"></div>
                    
                    
                    
                    
                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Password') }} *</label>
                            <div style="position:relative;"><input type="password" name="password" id="tz_reg_pwd" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Create password') }}"><button type="button" onclick="tzTogglePwd('tz_reg_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--tz-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button></div>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Confirm Password') }} *</label>
                            <div style="position:relative;"><input type="password" name="password_confirmation" id="tz_reg_pwd2" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Confirm') }}"><button type="button" onclick="tzTogglePwd('tz_reg_pwd2',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--tz-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button></div>
                        </div>
                    </div>
                    <a class="tz-generate-pwd" href="javascript:void(0)" style="font-size:12px;color:var(--tz-blue);display:inline-block;margin:16px 0;text-decoration:none;"><i class="mdi mdi-auto-fix"></i> {{ __('Generate random password') }}</a>
                    <button type="submit" id="register" style="width:100%;background:var(--tz-blue);color:#fff;border:0;padding:12px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;display:block;" onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                    </button>
                </form>
                <p style="text-align:center;font-size:13px;color:var(--tz-muted);margin-top:20px;">
                    {{ __('Already have an account?') }} <a href="{{ route('tenant.user.login') }}" style="color:var(--tz-blue);text-decoration:none;font-weight:600;">{{ __('Sign In') }}</a>
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

        $('.tz-generate-pwd').on('click', function () { var pwd = generateRandomPassword(); $('#tz_reg_pwd').val(pwd); $('#tz_reg_pwd2').val(pwd); });
        
    });
})(jQuery);
function tzTogglePwd(id, btn) { var input = document.getElementById(id); var icon = btn.querySelector('i'); if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; } else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; } }
</script>
@endsection
