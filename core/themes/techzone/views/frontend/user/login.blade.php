@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div style="background:var(--tz-surface);border-bottom:1px solid var(--tz-border);padding:28px 0 20px;">
    <div class="container">
        <h2 style="font-size:20px;font-weight:700;color:#fff;margin-bottom:8px;">{{ __('My Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--tz-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--tz-muted);text-decoration:none;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-muted)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:56px 0 72px;">
    <div class="mx-auto" style="max-width:920px;">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        @php
            $inp = 'width:100%;padding:10px 14px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:14px;font-family:var(--tz-font);outline:none;transition:border-color .2s;';
            $lbl = 'font-size:12px;font-weight:600;color:var(--tz-muted);display:block;margin-bottom:6px;letter-spacing:.3px;';
        @endphp

        <div style="display:grid;grid-template-columns:1fr 1fr;border:1px solid var(--tz-border);border-radius:var(--tz-radius);overflow:hidden;background:var(--tz-card);">

            {{-- Sign In --}}
            <div style="padding:40px;border-right:1px solid var(--tz-border);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                    <div style="width:32px;height:32px;border-radius:var(--tz-radius-sm);background:var(--tz-blue-glow);border:1px solid var(--tz-blue);display:flex;align-items:center;justify-content:center;color:var(--tz-blue);font-size:16px;">
                        <i class="mdi mdi-login-variant"></i>
                    </div>
                    <h3 style="font-size:18px;font-weight:700;color:#fff;margin:0;">{{ __('Sign In') }}</h3>
                </div>
                <p style="font-size:13px;color:var(--tz-muted);margin-bottom:28px;">{{ __('Access your orders, wishlist, and account details.') }}</p>

                <form action="" method="post" id="login_form_order_page">
                    @csrf
                    <div class="mb-3">
                        <label style="{{ $lbl }}">{{ __('Email or Username') }}</label>
                        <input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}" onfocus="this.style.borderColor='var(--tz-blue)'" onblur="this.style.borderColor='var(--tz-border)'">
                    </div>
                    <div class="mb-3">
                        <label style="{{ $lbl }}">{{ __('Password') }}</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="tz_login_pwd" style="{{ $inp }}padding-right:42px;" placeholder="••••••••" onfocus="this.style.borderColor='var(--tz-blue)'" onblur="this.style.borderColor='var(--tz-border)'">
                            <button type="button" onclick="tzTogglePwd('tz_login_pwd',this)" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--tz-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;color:var(--tz-muted);">
                            <input type="checkbox" name="remember" style="accent-color:var(--tz-blue);"> {{ __('Remember Me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" style="font-size:12px;color:var(--tz-blue);text-decoration:none;">{{ __('Forgot Password?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" style="width:100%;background:var(--tz-blue);color:#fff;border:0;padding:12px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;" onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                        <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" style="display:block;text-align:center;margin-top:12px;font-size:13px;color:var(--tz-blue);text-decoration:none;">{{ __('Login with OTP') }}</a>
                    @endif
                </form>
            </div>

            {{-- Create Account --}}
            <div style="padding:40px;background:var(--tz-surface);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                    <div style="width:32px;height:32px;border-radius:var(--tz-radius-sm);background:var(--tz-blue-glow);border:1px solid var(--tz-blue);display:flex;align-items:center;justify-content:center;color:var(--tz-blue);font-size:16px;">
                        <i class="mdi mdi-account-plus-outline"></i>
                    </div>
                    <h3 style="font-size:18px;font-weight:700;color:#fff;margin:0;">{{ __('Create Account') }}</h3>
                </div>
                <p style="font-size:13px;color:var(--tz-muted);margin-bottom:28px;">{{ __('Join TechZone for deal alerts, order tracking, and member pricing.') }}</p>

                <form action="{{ theme_register_store_url() }}" method="post">
                    @csrf
                    @php $inp2 = 'width:100%;padding:10px 14px;background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:14px;font-family:var(--tz-font);outline:none;'; @endphp
                    <div class="mb-3"><label style="{{ $lbl }}">{{ __('Full Name') }} *</label><input type="text" name="name" style="{{ $inp2 }}" placeholder="{{ __('Your name') }}" value="{{ old('name') }}"></div>
                    <div class="mb-3"><label style="{{ $lbl }}">{{ __('Username') }} *</label><input type="text" name="username" style="{{ $inp2 }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"></div>
                    <div class="mb-3"><label style="{{ $lbl }}">{{ __('Email Address') }} *</label><input type="email" name="email" style="{{ $inp2 }}" placeholder="{{ __('your@email.com') }}"></div>
                    <div class="mb-3"><label style="{{ $lbl }}">{{ __('Password') }} *</label><input type="password" name="password" style="{{ $inp2 }}" placeholder="{{ __('Min. 8 characters') }}"></div>
                    <div class="mb-3"><label style="{{ $lbl }}">{{ __('Confirm Password') }} *</label><input type="password" name="password_confirmation" style="{{ $inp2 }}" placeholder="{{ __('Repeat password') }}"></div>
                    <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:20px;">
                        <input type="checkbox" id="terms" style="margin-top:3px;accent-color:var(--tz-blue);">
                        <label for="terms" style="font-size:12px;color:var(--tz-muted);cursor:pointer;line-height:1.5;">
                            {{ __('I agree to the') }} <a href="#" style="color:var(--tz-blue);">{{ __('Terms') }}</a> &amp; <a href="#" style="color:var(--tz-blue);">{{ __('Privacy') }}</a>
                        </label>
                    </div>
                    <button type="submit" id="register" style="width:100%;background:transparent;color:var(--tz-blue);border:1px solid var(--tz-blue);padding:12px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:all .2s;" onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='var(--tz-blue)'">
                        <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_ajax_login_js() !!}
<script>
function tzTogglePwd(id, btn) { var input = document.getElementById(id); var icon = btn.querySelector('i'); if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; } else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; } }
</script>
@endsection
