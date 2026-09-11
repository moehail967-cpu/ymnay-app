@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Account') }}</div>
        <h2 style="font-size:28px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('Create Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--vl-muted);letter-spacing:1px;text-transform:uppercase;">
            <a href="{{ theme_home_url() }}" style="color:var(--vl-champagne);">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:64px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:48px 40px;">
                <div style="text-align:center;margin-bottom:32px;">
                    {!! theme_logo_html('') !!}
                    <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-top:16px;margin-bottom:4px;">{{ __('New Client') }}</div>
                    <h2 style="font-size:24px;font-weight:400;color:var(--vl-ivory);margin:0;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('Join VelvetLux') }}</h2>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                @php $inp = 'width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;'; $lbl = 'font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:8px;'; @endphp

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
                            <div style="position:relative;"><input type="password" name="password" id="vl_reg_pwd" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Create password') }}"><button type="button" onclick="vlTogglePwd('vl_reg_pwd',this)" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--vl-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button></div>
                        </div>
                        <div class="col-md-6">
                            <label style="{{ $lbl }}">{{ __('Confirm Password') }} *</label>
                            <div style="position:relative;"><input type="password" name="password_confirmation" id="vl_reg_pwd2" style="{{ $inp }}padding-right:42px;" placeholder="{{ __('Confirm password') }}"><button type="button" onclick="vlTogglePwd('vl_reg_pwd2',this)" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--vl-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button></div>
                        </div>
                    </div>
                    <a class="vl-generate-pwd" href="javascript:void(0)" style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);display:inline-block;margin:16px 0;">
                        <i class="mdi mdi-auto-fix"></i> {{ __('Generate password') }}
                    </a>
                    <button type="submit" id="register" style="width:100%;background:var(--vl-champagne);color:var(--vl-dark);border:0;padding:16px;font-size:10px;letter-spacing:4px;text-transform:uppercase;cursor:pointer;font-family:inherit;display:block;">
                        {{ __('Create Account') }}
                    </button>
                </form>

                <p style="text-align:center;font-size:12px;color:var(--vl-muted);margin-top:20px;">
                    {{ __('Already a member?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--vl-champagne);letter-spacing:1px;">{{ __('Sign In') }}</a>
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

        $('.vl-generate-pwd').on('click', function () { var pwd = generateRandomPassword(); $('#vl_reg_pwd').val(pwd); $('#vl_reg_pwd2').val(pwd); });
        
    });
})(jQuery);
function vlTogglePwd(id, btn) { var input = document.getElementById(id); var icon = btn.querySelector('i'); if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; } else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; } }
</script>
@endsection
