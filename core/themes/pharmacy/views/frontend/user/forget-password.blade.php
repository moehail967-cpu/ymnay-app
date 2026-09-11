@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div style="background:var(--pf-teal-light);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('Forgot Password') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--pf-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div style="background:#fff;border:1px solid var(--pf-border);border-radius:var(--pf-radius-xl);padding:40px 36px;box-shadow:var(--pf-shadow);">
                <div style="text-align:center;margin-bottom:28px;">
                    <div style="width:72px;height:72px;border-radius:50%;background:var(--pf-teal-light);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="las la-lock-open" style="font-size:32px;color:var(--pf-teal);"></i>
                    </div>
                    <h2 style="font-size:22px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('Reset Password') }}</h2>
                    <p style="font-size:13px;color:var(--pf-muted);">{{ __('Enter your username or email to receive reset instructions.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.forget.password') }}" method="post">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label style="font-size:13px;font-weight:600;color:var(--pf-dark);display:block;margin-bottom:6px;">
                            {{ __('Username or Email') }} <span style="color:var(--pf-teal);">*</span>
                        </label>
                        <input type="text" name="username" placeholder="{{ __('Enter your username or email') }}"
                               style="width:100%;padding:11px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;"
                               onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
                    </div>
                    <button type="submit" id="forget_btn" class="pf-btn pf-btn-teal w-100 justify-content-center" style="padding:13px;font-size:15px;">
                        {{ __('Send Reset Link') }}
                    </button>
                </form>

                <p style="text-align:center;margin-top:20px;font-size:13px;">
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--pf-teal);font-weight:600;">
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
