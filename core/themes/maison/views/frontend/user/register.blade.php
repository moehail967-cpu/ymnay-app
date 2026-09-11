@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Create Account') }} @endsection
@section('page-title') {{ __('Create Account') }} @endsection

@section('content')

<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:40px 0 28px;">
    <div class="container">
        <h1 style="font-size:13px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('New Member') }}</h1>
        <h2 style="font-size:30px;font-weight:300;color:var(--ms-dark);margin:0 0 10px;">{{ __('Create Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('Register') }}</span>
        </div>
    </div>
</div>

<div style="background:var(--ms-cream);padding:64px 0 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:44px;box-shadow:var(--ms-shadow);">

                    <div style="text-align:center;margin-bottom:32px;">
                        <div style="font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('Welcome') }}</div>
                        <h2 style="font-size:26px;font-weight:300;color:var(--ms-dark);margin:0 0 6px;">{{ __('Join Maison') }}</h2>
                        <div style="width:40px;height:2px;background:var(--ms-linen);margin:12px auto 0;"></div>
                    </div>

                    {!! theme_error_msg() !!}
                    {!! theme_flash_msg() !!}

                    @php
                        $iS = 'width:100%;padding:11px 14px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:14px;font-family:inherit;outline:none;color:var(--ms-dark);background:#fff;transition:border-color .2s;';
                        $lS = 'font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:7px;display:block;';
                    @endphp

                    <form action="{{ theme_register_store_url() }}" method="post" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label style="{{ $lS }}">{{ __('Full Name') }} <span style="color:var(--ms-linen);">*</span></label>
                                <input type="text" name="name" style="{{ $iS }}" placeholder="{{ __('Your full name') }}" value="{{ old('name') }}"
                                       onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $lS }}">{{ __('Username') }} <span style="color:var(--ms-linen);">*</span></label>
                                <input type="text" name="username" style="{{ $iS }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"
                                       onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                            </div>
                        </div>

                        <div style="margin-top:16px;">
                            <label style="{{ $lS }}">{{ __('Email Address') }} <span style="color:var(--ms-linen);">*</span></label>
                            <input type="email" name="email" style="{{ $iS }}" placeholder="{{ __('your@email.com') }}"
                                   onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                        </div>
                        <div class="row g-3" style="margin-top:16px;">
                            <div class="col-md-6">
                                <label style="{{ $lS }}">{{ __('Password') }} <span style="color:var(--ms-linen);">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="ms_reg_pwd" style="{{ $iS }}padding-right:42px;" placeholder="{{ __('Create password') }}"
                                           onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                                    <button type="button" onclick="msTogglePwd('ms_reg_pwd', this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--ms-muted);font-size:16px;padding:0;">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label style="{{ $lS }}">{{ __('Confirm Password') }} <span style="color:var(--ms-linen);">*</span></label>
                                <div style="position:relative;">
                                    <input type="password" name="password_confirmation" id="ms_reg_pwd2" style="{{ $iS }}padding-right:42px;" placeholder="{{ __('Confirm password') }}"
                                           onfocus="this.style.borderColor='var(--ms-linen)'" onblur="this.style.borderColor='var(--ms-border)'">
                                    <button type="button" onclick="msTogglePwd('ms_reg_pwd2', this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--ms-muted);font-size:16px;padding:0;">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:16px;">
                            <a class="ms-generate-pwd" href="javascript:void(0)"
                               style="font-size:12px;color:var(--ms-olive);font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px;letter-spacing:.04em;">
                                <i class="mdi mdi-auto-fix" style="font-size:15px;"></i>
                                {{ __('Generate a secure password') }}
                            </a>
                        </div>

                        <div style="margin-top:28px;">
                            <button type="submit" id="register"
                                    style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px;background:var(--ms-dark);color:#fff;border:none;border-radius:var(--ms-radius);font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                                    onmouseover="this.style.background='var(--ms-linen-d)'"
                                    onmouseout="this.style.background='var(--ms-dark)'">
                                {{ __('Create Account') }}
                            </button>
                        </div>
                    </form>

                    <p style="text-align:center;font-size:13px;color:var(--ms-muted);margin-top:20px;">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('tenant.user.login') }}" style="color:var(--ms-linen-d);font-weight:600;text-decoration:none;">{{ __('Sign In') }}</a>
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
        $('.ms-generate-pwd').on('click', function () {
            var pwd = generateRandomPassword();
            $('#ms_reg_pwd').val(pwd); $('#ms_reg_pwd2').val(pwd);
        });





                $('form').on('submit', function () {
            
            var $btn = $('#register');
            $btn.html('{{ __("Please Wait…") }}');
            $btn.css({'opacity': '0.7', 'pointer-events': 'none'});
        });
    });
})(jQuery);

function msTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
