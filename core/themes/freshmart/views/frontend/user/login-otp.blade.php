@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('OTP Sign In') }} @endsection
@section('page-title') {{ __('OTP Sign In') }} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ __('OTP Sign In') }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ __('OTP Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="fm-auth-card">
                <div class="fm-auth-header">
                    {!! theme_logo_html('fm-auth-logo-link', 'fm-auth-logo') !!}
                    <h2 class="fm-auth-title mt-3">{{ __('Sign In with OTP') }}</h2>
                    <p class="fm-auth-sub">{{ __("Enter your phone number and we'll send you a one-time code") }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="post"
                      enctype="multipart/form-data" class="fm-auth-form" id="login_form_order_page">
                    @csrf
                    <div class="error-wrap mb-3"></div>

                    <div class="fm-form-group" style="z-index:unset;">
                        <label class="fm-label">{{ __('Phone Number') }} <span class="fm-required">*</span></label>
                        <input class="fm-input" type="tel" name="phone" id="telephone"
                               placeholder="{{ __('Enter your phone number') }}" value="{{ old('phone') }}">
                    </div>

                    <button type="submit" id="login_btn" class="fm-btn fm-btn-green w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="las la-paper-plane"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p class="fm-auth-switch">
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
