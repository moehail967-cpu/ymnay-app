@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Forgot Password') }} @endsection
@section('page-title') {{ __('Forgot Password') }} @endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Account') }}</div>
        <h2 style="font-size:28px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('Forgot Password') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--vl-muted);letter-spacing:1px;text-transform:uppercase;">
            <a href="{{ theme_home_url() }}" style="color:var(--vl-champagne);">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:80px 0;">
    <div class="mx-auto" style="max-width:480px;">

        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="border:1px solid var(--vl-border);padding:48px 40px;">
            <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Recovery') }}</div>
            <h3 style="font-size:22px;font-weight:400;color:var(--vl-ivory);margin:0 0 10px;font-family:'Cormorant Garamond',serif;letter-spacing:1px;">{{ __('Reset Your Password') }}</h3>
            <p style="font-size:13px;color:var(--vl-muted);margin-bottom:36px;line-height:1.7;">{{ __('Enter your username or email address and we will send you a reset link.') }}</p>

            <form action="{{ route('tenant.user.forget.password') }}" method="POST">
                @csrf
                <div style="margin-bottom:20px;">
                    <label style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:10px;">{{ __('Username or Email') }}</label>
                    <input type="text" name="username"
                           style="width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;"
                           placeholder="{{ __('Enter username or email') }}"
                           value="{{ old('username') }}">
                </div>

                <button type="submit" id="send"
                        style="width:100%;background:var(--vl-champagne);color:var(--vl-dark);border:0;padding:16px;font-size:10px;letter-spacing:4px;text-transform:uppercase;cursor:pointer;font-family:inherit;">
                    {{ __('Send Reset Mail') }}
                </button>
            </form>

            <div style="margin-top:24px;text-align:center;font-size:12px;color:var(--vl-muted);">
                {{ __('Remembered it?') }}
                <a href="{{ route('tenant.user.login') }}" style="color:var(--vl-champagne);text-decoration:none;letter-spacing:1px;">{{ __('Sign In') }}</a>
            </div>
        </div>

    </div>
</div>
@endsection
