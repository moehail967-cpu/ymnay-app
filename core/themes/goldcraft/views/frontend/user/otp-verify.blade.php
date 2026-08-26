@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection

@section('content')
<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('Verify OTP') }}</h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <span>{{ __('OTP Verification') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:44px;box-shadow:var(--gc-shadow);">

                <div style="text-align:center;margin-bottom:24px;">
                    <div style="font-size:44px;margin-bottom:16px;">🛡️</div>
                    <div style="width:32px;height:1px;background:var(--gc-rose);margin:0 auto 16px;"></div>
                    <h2 style="font-size:22px;font-weight:400;color:var(--gc-dark);margin:0 0 8px;font-family:Georgia,serif;font-style:italic;">{{ __('Enter OTP Code') }}</h2>
                    <p style="font-size:13px;color:var(--gc-muted);margin:0;line-height:1.7;font-style:italic;">{{ __('An OTP has been sent to your phone number.') }}</p>
                </div>

                <div class="gc-otp-countdown" style="text-align:center;font-size:16px;font-weight:400;color:var(--gc-rose);margin-bottom:16px;min-height:24px;font-family:Georgia,serif;font-style:italic;"></div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                @php
                    $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;outline:none;background:#fff;transition:border-color .2s;color:var(--gc-dark);';
                    $lbl = 'font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:6px;display:block;';
                @endphp

                <form action="{{ theme_otp_verify_url() }}" method="post" id="login_form_order_page">
                    @csrf
                    <div class="error-wrap mb-3"></div>

                    <div class="mb-3">
                        <label style="{{ $lbl }}">{{ __('OTP Code') }} <span style="color:var(--gc-rose);">*</span></label>
                        <input type="number" name="otp" value="{{ old('otp') }}"
                               placeholder="{{ __('Enter 6-digit OTP') }}"
                               style="{{ $inp }}font-size:22px;font-weight:400;letter-spacing:8px;text-align:center;"
                               onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                    </div>

                    <div class="mb-4">
                        <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;color:var(--gc-dark);font-style:italic;">
                            <input type="checkbox" name="remember" style="accent-color:var(--gc-rose);width:14px;height:14px;"> {{ __('Remember Me') }}
                        </label>
                    </div>

                    <button type="submit" id="login_btn" class="gc-btn gc-btn-primary" style="width:100%;justify-content:center;">
                        <i class="las la-check-circle"></i> {{ __('Verify OTP') }}
                    </button>
                </form>

                <div style="display:flex;justify-content:space-between;margin-top:16px;font-size:13px;font-style:italic;">
                    <a href="{{ route('tenant.user.login.otp') }}" style="color:var(--gc-rose);font-weight:600;text-decoration:none;">{{ __('Update number?') }}</a>
                    <a href="{{ theme_otp_resend_url() }}" style="color:var(--gc-rose);font-weight:600;text-decoration:none;">{{ __('Resend OTP?') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@php
    $expire_time = 0;
    if (!now()->isAfter($userOtp->expire_date ?? now())) {
        $expire_time = $userOtp ? now()->diffInRealSeconds($userOtp->expire_date) : 0;
    }
@endphp
<script>
let expire_time = {{ $expire_time }};
let interval = setInterval(function () {
    if (expire_time > 0) expire_time--;
    let cd = $('.gc-otp-countdown');
    if (parseInt(expire_time) === 0) {
        cd.css('color','#e53e3e').text('{{ __("The OTP is expired") }}');
        return clearInterval(interval);
    }
    cd.css('color','var(--gc-rose)').text(expire_time + ' {{ __("Seconds") }}');
}, 1000);
</script>
@endsection
