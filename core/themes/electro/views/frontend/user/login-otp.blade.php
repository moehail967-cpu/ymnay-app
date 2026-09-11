@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Login with OTP') }} @endsection
@section('page-title') {{ __('Login with OTP') }} @endsection

@section('content')
<div class="el-auth-hero">
    <div class="container">
        <h2>{{ __('OTP Login') }}</h2>
        <div class="el-dash-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="las la-angle-right" style="font-size:11px;"></i>
            <span>{{ __('OTP Login') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="el-auth-card">
                <div style="text-align:center;margin-bottom:28px;">
                    <div class="el-auth-icon">
                        <i class="las la-mobile-alt" style="font-size:32px;color:#E8603C;"></i>
                    </div>
                    <h2 style="font-size:22px;font-weight:700;color:#1a1a1a;margin-bottom:8px;">{{ __('Login with OTP') }}</h2>
                    <p style="font-size:13px;color:#888;">{{ __('Enter your phone number to receive a one-time password.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="post">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label class="el-form-label">{{ __('Phone Number') }} <span class="el-form-required">*</span></label>
                        <input type="tel" name="phone" id="telephone" class="el-form-input" placeholder="{{ __('Your registered phone number') }}">
                    </div>
                    <button type="submit" id="login_btn" class="el-btn el-btn-primary el-btn-block" style="padding:13px;font-size:15px;">
                        <i class="las la-paper-plane"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p style="text-align:center;margin-top:20px;font-size:13px;color:#888;">
                    {{ __('Prefer password?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:#E8603C;font-weight:600;">{{ __('Sign In') }}</a>
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
