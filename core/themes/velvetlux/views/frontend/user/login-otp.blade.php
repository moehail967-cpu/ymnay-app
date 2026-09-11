@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('OTP Sign In') }} @endsection
@section('page-title') {{ __('OTP Sign In') }} @endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Account') }}</div>
        <h2 style="font-size:28px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('OTP Sign In') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--vl-muted);letter-spacing:1px;text-transform:uppercase;">
            <a href="{{ theme_home_url() }}" style="color:var(--vl-champagne);">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{{ __('OTP Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:80px 0;">
    <div class="mx-auto" style="max-width:480px;">

        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="border:1px solid var(--vl-border);padding:48px 40px;">
            <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Secure Login') }}</div>
            <h3 style="font-size:22px;font-weight:400;color:var(--vl-ivory);margin:0 0 10px;font-family:'Cormorant Garamond',serif;letter-spacing:1px;">{{ __('Sign In with OTP') }}</h3>
            <p style="font-size:13px;color:var(--vl-muted);margin-bottom:36px;line-height:1.7;">{{ __('Enter your phone number to receive a one-time password.') }}</p>

            <form action="{{ route('tenant.user.login.otp') }}" method="POST" class="account-form" id="login_form_order_page">
                @csrf
                <div class="error-wrap" style="margin-bottom:12px;"></div>

                <div style="margin-bottom:24px;" class="single-input" style="z-index:unset;">
                    <label class="label-title" style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:10px;">
                        {{ __('Phone Number') }} <x-fields.mandatory-indicator/>
                    </label>
                    <input class="form--control"
                           type="tel" name="phone" id="telephone"
                           value="{{ old('phone') }}"
                           style="width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;">
                </div>

                <button type="submit" id="login_btn"
                        style="width:100%;background:var(--vl-champagne);color:var(--vl-dark);border:0;padding:16px;font-size:10px;letter-spacing:4px;text-transform:uppercase;cursor:pointer;font-family:inherit;margin-bottom:20px;">
                    {{ __('Send OTP') }}
                </button>
            </form>

            <div style="text-align:center;font-size:12px;color:var(--vl-muted);">
                {{ __('Already have an account?') }}
                <a href="{{ route('tenant.user.login') }}" style="color:var(--vl-champagne);text-decoration:none;letter-spacing:1px;">{{ __('Sign In') }}</a>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
    {!! theme_phone_js('#telephone', 'login_btn') !!}
@endsection
