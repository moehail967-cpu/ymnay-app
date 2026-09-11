@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{{ __('Forgot Password') }}</h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li>{{ __('Forgot Password') }}</li>
        </ul>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="fp-auth-card">
                <div class="fp-auth-header">
                    {!! theme_logo_html('fp-auth-logo') !!}
                    <h2 class="fp-auth-title">{{ __('Reset Password') }}</h2>
                    <p class="fp-auth-sub">{{ __('Enter your username and we\'ll send you a reset link') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.forget.password') }}" method="POST" class="fp-auth-form">
                    @csrf
                    <div class="fp-form-group">
                        <label class="fp-auth-label">{{ __('Username') }} <span class="fp-required">*</span></label>
                        <input type="text" name="username" class="fp-auth-input"
                               placeholder="{{ __('Enter your username') }}" value="{{ old('username') }}">
                    </div>

                    <button type="submit" id="send" class="fp-btn fp-btn-primary w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        <i class="mdi mdi-email-send-outline"></i> {{ __('Send Reset Mail') }}
                    </button>
                </form>

                <p class="fp-auth-switch">
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
