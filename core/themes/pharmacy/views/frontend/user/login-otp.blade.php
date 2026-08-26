@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Login with OTP') }} @endsection
@section('page-title') {{ __('Login with OTP') }} @endsection

@section('content')
<div style="background:var(--pf-teal-light);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('OTP Login') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--pf-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('OTP Login') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div style="background:#fff;border:1px solid var(--pf-border);border-radius:var(--pf-radius-xl);padding:40px 36px;box-shadow:var(--pf-shadow);">
                <div style="text-align:center;margin-bottom:28px;">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--pf-teal-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="las la-mobile-alt" style="font-size:32px;color:var(--pf-teal);"></i>
                    </div>
                    <h2 style="font-size:22px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('Login with OTP') }}</h2>
                    <p style="font-size:13px;color:var(--pf-muted);">{{ __('Enter your phone number to receive a one-time password.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="post">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">
                            {{ __('Phone Number') }} <span style="color:var(--pf-teal);">*</span>
                        </label>
                        <input type="tel" name="phone" id="telephone" placeholder="{{ __('Your registered phone number') }}"
                               style="width:100%;padding:11px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;"
                               onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
                    </div>
                    <button type="submit" id="login_btn" class="pf-btn pf-btn-teal w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="las la-paper-plane"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p style="text-align:center;margin-top:20px;font-size:13px;color:var(--pf-muted);">
                    {{ __('Prefer password?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_phone_js('#telephone', 'login_btn') !!}
{!! theme_btn_loading_js('login_btn', __('Sending…')) !!}
@endsection
