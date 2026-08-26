@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Verify OTP') }} @endsection
@section('page-title') {{ __('Verify OTP') }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ __('Verify OTP') }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('Verify OTP') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:72px 0 88px;">
    <div class="mx-auto" style="max-width:460px;">
        <x-error-msg/>
        <x-flash-msg/>

        <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;box-shadow:var(--sz-shadow-sm);">
            <div style="background:var(--sz-navy);padding:28px 36px 24px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:44px;height:44px;background:var(--sz-red);display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;border-radius:var(--sz-radius);">
                        <i class="mdi mdi-shield-check-outline"></i>
                    </div>
                    <div>
                        <h1 style="font-family:var(--sz-font-head);font-size:22px;font-weight:700;color:#fff;margin:0;text-transform:uppercase;letter-spacing:2px;">{{ __('Enter Your Code') }}</h1>
                        <p style="font-size:12px;color:rgba(255,255,255,.55);margin:4px 0 0;">{{ __('A 6-digit OTP was sent to your phone.') }}</p>
                    </div>
                </div>
            </div>

            <div style="padding:32px 36px 36px;">
                @php $inp = 'width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:14px;font-family:var(--sz-font-body);outline:none;transition:border-color .2s;'; @endphp

                <form method="POST" action="{{ theme_otp_verify_url() }}">
                    @csrf
                    <div class="mb-4">
                        <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('6-Digit OTP Code') }}</label>
                        <input type="text" name="otp" required maxlength="6" autocomplete="one-time-code"
                               style="{{ $inp }}letter-spacing:8px;text-align:center;font-size:22px;font-weight:700;"
                               placeholder="••••••"
                               onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                    </div>

                    <div style="text-align:center;margin-bottom:24px;font-size:13px;color:var(--sz-muted);">
                        {{ __('Expires in') }}
                        <span id="sz_otp_timer" style="color:var(--sz-red);font-weight:700;">03:00</span>
                    </div>

                    <button type="submit" class="sz-btn sz-btn-red w-100" style="justify-content:center;">
                        <i class="mdi mdi-check-circle-outline"></i> {{ __('Verify & Login') }}
                    </button>

                    <div style="text-align:center;margin-top:20px;">
                        <a href="{{ theme_otp_resend_url() }}" style="font-size:13px;color:var(--sz-red);font-family:var(--sz-font-head);letter-spacing:1px;text-transform:uppercase;">
                            <i class="mdi mdi-refresh"></i> {{ __('Resend OTP') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    var total = 180, el = document.getElementById('sz_otp_timer');
    if (!el) return;
    var t = setInterval(function () {
        total--;
        if (total <= 0) { clearInterval(t); el.textContent = '{{ __('Expired') }}'; el.style.color='#999'; return; }
        var m = Math.floor(total / 60), s = total % 60;
        el.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }, 1000);
})();
</script>
@endsection
