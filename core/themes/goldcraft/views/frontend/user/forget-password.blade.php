@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Forgot Password') }} @endsection

@section('content')
<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('Forgot Password') }}</h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <span>{{ __('Forgot Password') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:44px;box-shadow:var(--gc-shadow);">

                <div style="text-align:center;margin-bottom:32px;">
                    <div style="font-size:44px;margin-bottom:16px;"><i class="las la-key"></i></div>
                    <div style="width:32px;height:1px;background:var(--gc-rose);margin:0 auto 16px;"></div>
                    <h2 style="font-size:22px;font-weight:400;color:var(--gc-dark);margin:0 0 8px;font-family:Georgia,serif;font-style:italic;">{{ __('Reset Your Password') }}</h2>
                    <p style="font-size:13px;color:var(--gc-muted);margin:0;line-height:1.7;font-style:italic;">{{ __("Enter your username or email and we'll send you a reset link.") }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                @php
                    $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;outline:none;background:#fff;transition:border-color .2s;color:var(--gc-dark);';
                    $lbl = 'font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:6px;display:block;';
                @endphp

                <form action="{{ route('tenant.user.forget.password') }}" method="post">
                    @csrf
                    <div class="mb-4">
                        <label style="{{ $lbl }}">{{ __('Username or Email') }}</label>
                        <input type="text" name="username" placeholder="{{ __('Enter your username or email') }}"
                               style="{{ $inp }}"
                               onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                    </div>

                    <button type="submit" id="send" class="gc-btn gc-btn-primary" style="width:100%;justify-content:center;">
                        <i class="las la-envelope-open-text"></i> {{ __('Send Reset Mail') }}
                    </button>
                </form>

                <p style="text-align:center;font-size:13px;color:var(--gc-muted);margin-top:20px;font-style:italic;">
                    {{ __('Remembered your password?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--gc-rose);font-weight:600;text-decoration:none;">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).ready(function () {
        {!! theme_btn_loading_js('send', __('Sending…')) !!}
    });
})(jQuery);
</script>
@endsection
