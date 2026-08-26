@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Login with OTP') }} @endsection
@section('page-title') {{ __('Login with OTP') }} @endsection

@section('content')
<div class="mc-auth-hero">
    <div class="container">
        <h2>{{ __('OTP Login') }}</h2>

    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="mc-auth-card">
                <div style="text-align:center;margin-bottom:28px;">
                    <div class="mc-auth-icon">
                        <i class="las la-mobile-alt" style="font-size:32px;color:#1A85ED;"></i>
                    </div>
                    <h2 style="font-size:22px;font-weight:700;color:#1a1a1a;margin-bottom:8px;">{{ __('Login with OTP') }}</h2>
                    <p style="font-size:13px;color:#888;">{{ __('Enter your phone number to receive a one-time password.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="post">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label class="mc-form-label">{{ __('Phone Number') }} <span class="mc-form-required">*</span></label>
                        <input type="tel" name="phone" id="telephone" class="mc-form-input" placeholder="{{ __('Your registered phone number') }}">
                    </div>
                    <button type="submit" id="login_btn" class="mc-btn mc-btn-primary mc-btn-block" style="padding:13px;font-size:15px;">
                        <i class="las la-paper-plane"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p style="text-align:center;margin-top:20px;font-size:13px;color:#888;">
                    {{ __('Prefer password?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:#1A85ED;font-weight:600;">{{ __('Sign In') }}</a>
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
