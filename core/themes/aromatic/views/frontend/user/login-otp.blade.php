@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Login with OTP') }} @endsection
@section('page-title') {{ __('Login with OTP') }} @endsection

@section('content')
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('Login with OTP') }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span>{{ __('OTP Login') }}</span>
        </div>
    </div>
</div>

<div class="container ar-auth-wrap">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="ar-auth-card">
                <div class="ar-auth-centered">
                    <i class="mdi mdi-cellphone-message" style="font-size:48px;color:var(--ar-red);"></i>
                    <h2 class="ar-auth-title" style="margin-top:12px;">{{ __('OTP Login') }}</h2>
                    <p class="ar-auth-sub">{{ __('Enter your phone number to receive a one-time password.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route(route_prefix().'user.login.otp') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="ar-auth-label">{{ __('Phone Number') }} <span class="ar-required">*</span></label>
                        <input type="tel" name="telephone" id="telephone" class="ar-auth-input" placeholder="{{ __('Your phone number') }}" required>
                    </div>
                    <button type="submit" id="otp_send_btn" class="ar-btn ar-btn-red w-100 justify-content-center">
                        <i class="mdi mdi-send-outline"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p class="ar-auth-switch">
                    <a href="{{ route('tenant.user.login') }}"><i class="mdi mdi-arrow-left"></i> {{ __('Back to Login') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_phone_js('#telephone', 'otp_send_btn') !!}
@endsection
