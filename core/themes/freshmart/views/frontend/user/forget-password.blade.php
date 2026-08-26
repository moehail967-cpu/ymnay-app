@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ __('Forgot Password') }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="fm-auth-card">
                <div class="fm-auth-header">
                    {!! theme_logo_html('fm-auth-logo-link', 'fm-auth-logo') !!}
                    <h2 class="fm-auth-title mt-3">{{ __('Reset Password') }}</h2>
                    <p class="fm-auth-sub">{{ __("Enter your username and we'll send you a reset link") }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.forget.password') }}" method="POST" class="fm-auth-form">
                    @csrf
                    <div class="fm-form-group">
                        <label class="fm-label">{{ __('Username') }} <span class="fm-required">*</span></label>
                        <input type="text" name="username" class="fm-input"
                               placeholder="{{ __('Enter your username') }}" value="{{ old('username') }}">
                    </div>

                    <button type="submit" id="send" class="fm-btn fm-btn-green w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="las la-envelope"></i> {{ __('Send Reset Mail') }}
                    </button>
                </form>

                <p class="fm-auth-switch">
                    {{ __('Remember your password?') }}
                    <a href="{{ route('tenant.user.login') }}">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    $(document).ready(function () {
        {!! theme_btn_loading_js('send', __('Sending')) !!}
    });
})(jQuery);
</script>
@endsection
