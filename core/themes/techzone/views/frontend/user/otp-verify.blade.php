@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div style="background:var(--tz-surface);border-bottom:1px solid var(--tz-border);padding:28px 0 20px;">
    <div class="container">
        <h2 style="font-size:20px;font-weight:700;color:#fff;margin-bottom:8px;">{{ __('Verify OTP') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--tz-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--tz-muted);text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:72px 0;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:40px;">
                <div style="text-align:center;margin-bottom:28px;">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--tz-blue-glow);border:1px solid var(--tz-blue);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:var(--tz-blue);">
                        <i class="mdi mdi-shield-key-outline"></i>
                    </div>
                    <h3 style="font-size:18px;font-weight:700;color:#fff;margin-bottom:6px;">{{ __('Enter Your OTP') }}</h3>
                    <p style="font-size:13px;color:var(--tz-muted);">{{ __('We sent a 6-digit code to your phone. It expires in 3 minutes.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ theme_otp_verify_url() }}" method="post">
                    @csrf
                    <div style="margin-bottom:20px;">
                        <label style="font-size:12px;font-weight:600;color:var(--tz-muted);display:block;margin-bottom:8px;text-align:center;">{{ __('OTP Code') }}</label>
                        <input type="number" name="otp"
                               style="width:100%;padding:14px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:24px;font-family:var(--tz-font);outline:none;text-align:center;letter-spacing:12px;font-weight:700;transition:border-color .2s;"
                               placeholder="------" maxlength="6"
                               onfocus="this.style.borderColor='var(--tz-blue)'" onblur="this.style.borderColor='var(--tz-border)'">
                    </div>
                    <div style="text-align:center;margin-bottom:16px;font-size:13px;color:var(--tz-muted);">
                        {{ __('Code expires in') }}: <span id="tz-otp-countdown" style="color:var(--tz-blue);font-weight:700;">3:00</span>
                    </div>
                    <button type="submit"
                            style="width:100%;background:var(--tz-blue);color:#fff;border:0;padding:12px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;margin-bottom:12px;"
                            onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                        <i class="mdi mdi-check-circle-outline"></i> {{ __('Verify OTP') }}
                    </button>
                </form>
                <div style="text-align:center;">
                    <a href="{{ theme_otp_resend_url() }}" style="font-size:13px;color:var(--tz-blue);text-decoration:none;">
                        <i class="mdi mdi-refresh"></i> {{ __('Resend OTP') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
    var total = 180;
    var el = document.getElementById('tz-otp-countdown');
    var timer = setInterval(function(){
        total--;
        var m = Math.floor(total/60), s = total % 60;
        el.textContent = m + ':' + (s < 10 ? '0' : '') + s;
        if(total <= 0){ clearInterval(timer); el.textContent = '{{ __("Expired") }}'; el.style.color = '#ef4444'; }
    }, 1000);
})();
</script>
@endsection
