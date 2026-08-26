@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Login with OTP') }} @endsection
@section('page-title') {{ __('OTP Login') }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ __('Login with OTP') }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('OTP Login') }}</span>
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
                        <i class="mdi mdi-cellphone-message"></i>
                    </div>
                    <div>
                        <h1 style="font-family:var(--sz-font-head);font-size:22px;font-weight:700;color:#fff;margin:0;text-transform:uppercase;letter-spacing:2px;">{{ __('OTP Verification') }}</h1>
                        <p style="font-size:12px;color:rgba(255,255,255,.55);margin:4px 0 0;">{{ __("We'll send a one-time code to your phone.") }}</p>
                    </div>
                </div>
            </div>

            <div style="padding:32px 36px 36px;">
                @php $inp = 'width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:14px;font-family:var(--sz-font-body);outline:none;transition:border-color .2s;'; @endphp

                <form method="POST" action="{{ route('tenant.user.login.otp') }}" id="otp_login_form">
                    @csrf
                    <div class="mb-3">
                        <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Country Code') }}</label>
                        <select name="country_code" style="{{ $inp }}background:#fff;">
                            <option value="+1">+1 (US)</option>
                            <option value="+44">+44 (UK)</option>
                            <option value="+91">+91 (IN)</option>
                            <option value="+880">+880 (BD)</option>
                            <option value="+61">+61 (AU)</option>
                            <option value="+49">+49 (DE)</option>
                            <option value="+33">+33 (FR)</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Phone Number') }}</label>
                        <input type="tel" name="phone" required
                               style="{{ $inp }}"
                               placeholder="{{ __('e.g. 01711000000') }}"
                               onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                    </div>
                    <button type="submit" class="sz-btn sz-btn-red w-100" style="justify-content:center;" id="otp_send_btn">
                        <i class="mdi mdi-send-outline"></i> {{ __('Send OTP') }}
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

@section('scripts')
{!! theme_phone_js() !!}
@endsection
