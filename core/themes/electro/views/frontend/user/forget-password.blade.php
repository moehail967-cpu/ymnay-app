@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="el-auth-hero">
    <div class="container">
        <h2>{{ __('Forgot Password') }}</h2>
        <div class="el-dash-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="las la-angle-right" style="font-size:11px;"></i>
            <span>{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="el-auth-card">
                <div style="text-align:center;margin-bottom:28px;">
                    <div class="el-auth-icon">
                        <i class="las la-lock-open" style="font-size:32px;color:#E8603C;"></i>
                    </div>
                    <h2 style="font-size:22px;font-weight:700;color:#1a1a1a;margin-bottom:8px;">{{ __('Reset Password') }}</h2>
                    <p style="font-size:13px;color:#888;">{{ __('Enter your username or email to receive reset instructions.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.forget.password') }}" method="post">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label class="el-form-label">{{ __('Username or Email') }} <span class="el-form-required">*</span></label>
                        <input type="text" name="username" class="el-form-input" placeholder="{{ __('Enter your username or email') }}">
                    </div>
                    <button type="submit" id="forget_btn" class="el-btn el-btn-primary el-btn-block" style="padding:13px;font-size:15px;">
                        {{ __('Send Reset Link') }}
                    </button>
                </form>

                <p style="text-align:center;margin-top:20px;font-size:13px;">
                    <a href="{{ route('tenant.user.login') }}" style="color:#E8603C;font-weight:600;">
                        <i class="las la-arrow-left"></i> {{ __('Back to Sign In') }}
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_btn_loading_js('forget_btn', __('Sending…')) !!}
@endsection
