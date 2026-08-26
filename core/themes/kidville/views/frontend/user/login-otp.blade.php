@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('OTP Sign In') }} @endsection

@section('content')

<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('OTP Sign In') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('OTP Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="kv-auth-card">
                <div class="kv-auth-header">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--kv-light);border:3px solid var(--kv-border);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:32px;color:var(--kv-blue);">
                        <i class="las la-mobile-alt"></i>
                    </div>
                    <h2 class="kv-auth-title">{{ __('Sign In with OTP') }}</h2>
                    <p class="kv-auth-sub">{{ __("Enter your phone number and we'll send you a one-time code.") }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="post" id="login_form_order_page">
                    @csrf
                    <div class="error-wrap mb-3"></div>

                    <div class="kv-form-group">
                        <label class="kv-label">{{ __('Phone Number') }} <span class="kv-required">*</span></label>
                        <input type="tel" name="phone" id="telephone" value="{{ old('phone') }}"
                               class="kv-input" placeholder="{{ __('Enter your phone number') }}">
                    </div>

                    <button type="submit" id="login_btn" class="kv-btn kv-btn-red" style="width:100%;justify-content:center;padding:13px;font-size:15px;">
                        <i class="las la-paper-plane"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p class="kv-auth-switch" style="margin-top:20px;">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
{!! theme_phone_js('#telephone', 'login_btn') !!}
@endsection
