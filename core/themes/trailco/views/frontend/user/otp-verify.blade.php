@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div style="background:var(--tr-bark);padding:24px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:26px;font-weight:900;color:#fff;margin-bottom:6px;">{{ __('Verify OTP') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('OTP Verification') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;box-shadow:var(--tr-shadow);">
                <div style="background:var(--tr-bark);padding:20px 36px;display:flex;align-items:center;gap:14px;">
                    <div style="width:42px;height:42px;border-radius:var(--tr-radius);background:var(--tr-olive);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;">
                        <i class="mdi mdi-shield-key"></i>
                    </div>
                    <div>
                        <h2 style="font-size:18px;font-weight:900;color:#fff;margin:0;">{{ __('Enter OTP Code') }}</h2>
                        <p style="font-size:12px;color:rgba(255,255,255,.6);margin:2px 0 0;">{{ __('An OTP has been sent to your phone number') }}</p>
                    </div>
                </div>

                <div style="padding:36px;">
                    <div class="countdown" style="text-align:center;font-size:16px;font-weight:700;color:var(--tr-olive);margin-bottom:20px;min-height:24px;"></div>

                    {!! theme_error_msg() !!}
                    {!! theme_flash_msg() !!}

                    @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;'; @endphp

                    <form action="{{ theme_otp_verify_url() }}" method="post" enctype="multipart/form-data" id="login_form_order_page">
                        @csrf
                        <div class="error-wrap mb-3"></div>
                        <div class="mb-3">
                            <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:6px;display:block;">{{ __('OTP Code') }} <span style="color:var(--tr-terra);">*</span></label>
                            <input type="number" name="otp" style="{{ $inp }}letter-spacing:8px;font-size:22px;font-weight:900;text-align:center;" placeholder="······" value="{{ old('otp') }}"
                                   onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                        </div>
                        <div class="mb-4">
                            <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                                <input type="checkbox" name="remember" style="accent-color:var(--tr-olive);"> {{ __('Remember Me') }}
                            </label>
                        </div>
                        <button type="submit" id="login_btn" class="tr-btn tr-btn-primary" style="width:100%;justify-content:center;">
                            <i class="mdi mdi-check-circle"></i> {{ __('Verify OTP') }}
                        </button>
                    </form>

                    <div style="display:flex;justify-content:space-between;margin-top:20px;font-size:13px;">
                        <a href="{{ route('tenant.user.login.otp') }}" style="color:var(--tr-olive);font-weight:600;">{{ __('Update number?') }}</a>
                        <a href="{{ theme_otp_resend_url() }}" style="color:var(--tr-olive);font-weight:600;">{{ __('Resend OTP?') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@php
    $expire_time = 0;
    if (!now()->isAfter($userOtp->expire_date)) {
        $expire_time = $userOtp ? now()->diffInRealSeconds($userOtp->expire_date) : 0;
    }
@endphp
<script>
let expire_time = {{ $expire_time }};
let interval = setInterval(function () {
    if (expire_time > 0) expire_time--;
    let cd = $('.countdown');
    if (parseInt(expire_time) === 0) {
        cd.css('color','var(--tr-terra)').text('{{ __("The OTP is expired") }}');
        return clearInterval(interval);
    }
    cd.css('color','var(--tr-olive)').text(expire_time + ' {{ __("Seconds") }}');
}, 1000);
</script>
@endsection
