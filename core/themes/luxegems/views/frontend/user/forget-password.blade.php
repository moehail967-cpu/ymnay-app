@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="lg-auth-card">
                <div class="lg-auth-header">
                    <i class="las la-key" style="font-size:40px;color:var(--lx-gold);display:block;margin-bottom:16px;"></i>
                    <h2 class="lg-auth-title">{{ __('Reset Password') }}</h2>
                    <p class="lg-auth-sub">{{ __('Enter your username or email to receive a reset link.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="" method="post" class="lg-auth-form" id="forget_form">
                    @csrf
                    <div class="lg-form-group">
                        <label class="lg-form-label">{{ __('Username or Email') }} <span class="lg-required">*</span></label>
                        <input type="text" name="username_or_email" id="forget_input"
                               class="lg-form-control" placeholder="{{ __('Enter your username or email') }}"
                               value="{{ old('username_or_email') }}">
                    </div>
                    <button type="submit" id="forget_btn" class="lx-btn lx-btn-primary w-100 justify-content-center" style="padding:13px;">
                        {{ __('Send Reset Link') }}
                    </button>
                </form>

                <p class="lg-auth-switch">
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--lx-gold);">
                        <i class="las la-arrow-left"></i> {{ __('Back to Sign In') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function($){
    {!! theme_btn_loading_js('forget_btn', __('Please Wait…')) !!}
})(jQuery);
</script>
@endsection
