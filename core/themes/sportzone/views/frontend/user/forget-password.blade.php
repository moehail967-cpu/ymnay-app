@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ __('Forgot Password') }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:72px 0 88px;">
    <div class="mx-auto" style="max-width:480px;">
        <x-error-msg/>
        <x-flash-msg/>

        <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;box-shadow:var(--sz-shadow-sm);">
            <div style="background:var(--sz-navy);padding:28px 36px 24px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:44px;height:44px;background:var(--sz-red);display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;border-radius:var(--sz-radius);">
                        <i class="mdi mdi-lock-reset"></i>
                    </div>
                    <div>
                        <h1 style="font-family:var(--sz-font-head);font-size:22px;font-weight:700;color:#fff;margin:0;text-transform:uppercase;letter-spacing:2px;">{{ __('Reset Password') }}</h1>
                        <p style="font-size:12px;color:rgba(255,255,255,.55);margin:4px 0 0;">{{ __("Enter your email and we'll send reset instructions.") }}</p>
                    </div>
                </div>
            </div>

            <div style="padding:32px 36px 36px;">
                @php $inp = 'width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:14px;font-family:var(--sz-font-body);outline:none;transition:border-color .2s;'; @endphp

                <form method="POST" action="{{ route('tenant.user.forget.password') }}">
                    @csrf
                    <div class="mb-4">
                        <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Email or Username') }}</label>
                        <input type="text" name="username" required
                               style="{{ $inp }}"
                               placeholder="{{ __('your@email.com') }}"
                               onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                    </div>
                    <button type="submit" class="sz-btn sz-btn-red w-100" style="justify-content:center;">
                        <i class="mdi mdi-send-outline"></i> {{ __('Send Reset Link') }}
                    </button>
                    <div style="text-align:center;margin-top:20px;">
                        <a href="{{ route('tenant.user.login') }}" style="font-size:13px;color:var(--sz-red);font-family:var(--sz-font-head);letter-spacing:1px;text-transform:uppercase;">
                            <i class="mdi mdi-arrow-left"></i> {{ __('Back to Login') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
