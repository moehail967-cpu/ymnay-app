@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')

{{-- Page Banner --}}
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:44px 0 32px;text-align:center;">
    <div class="container">
        <div style="font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('Account') }}</div>
        <h1 style="font-size:clamp(22px,4vw,36px);font-weight:300;color:var(--ms-dark);margin:0 auto 12px;line-height:1.2;">{{ __('Forgot Password') }}</h1>
        <div style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;"
               onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-linen-d)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<section style="background:var(--ms-cream);padding:64px 0 80px;">
    <div class="container">
        <div style="max-width:440px;margin:0 auto;">

            <x-flash-msg/>
            <x-error-msg/>

            <div class="ms-auth-card">
                {{-- Icon --}}
                <div style="text-align:center;margin-bottom:24px;">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--ms-warm);border:1px solid var(--ms-border);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                        <i class="mdi mdi-lock-reset" style="font-size:28px;color:var(--ms-olive);"></i>
                    </div>
                    <h2 style="font-size:20px;font-weight:400;color:var(--ms-dark);margin-bottom:8px;">{{ __('Reset Your Password') }}</h2>
                    <p style="font-size:13px;color:var(--ms-muted);line-height:1.6;">
                        {{ __('Enter your email address and we\'ll send you a link to reset your password.') }}
                    </p>
                </div>

                <div class="ms-auth-divider"></div>

                <form action="{{ route('tenant.user.forget.password') }}" method="POST" style="margin-top:24px;">
                    @csrf
                    <div class="ms-form-group">
                        <label class="ms-form-label">{{ __('Email Address') }}</label>
                        <input type="email" name="email" class="ms-form-input"
                               placeholder="{{ __('your@email.com') }}"
                               value="{{ old('email') }}" required>
                    </div>

                    <button type="submit" class="ms-btn-full ms-btn-dark" style="margin-top:20px;">
                        <i class="mdi mdi-email-send-outline" style="margin-right:8px;font-size:16px;"></i>
                        {{ __('Send Reset Link') }}
                    </button>
                </form>

                <div style="text-align:center;margin-top:20px;">
                    <a href="{{ route('tenant.user.login') }}"
                       style="font-size:13px;color:var(--ms-muted);text-decoration:none;display:inline-flex;align-items:center;gap:6px;transition:color .2s;"
                       onmouseover="this.style.color='var(--ms-linen-d)'"
                       onmouseout="this.style.color='var(--ms-muted)'">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Sign In') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
