@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('OTP Login') }} @endsection
@section('page-title') {{ __('OTP Login') }} @endsection

@section('content')
<div style="background:var(--tz-surface);border-bottom:1px solid var(--tz-border);padding:28px 0 20px;">
    <div class="container">
        <h2 style="font-size:20px;font-weight:700;color:#fff;margin-bottom:8px;">{{ __('Login with OTP') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--tz-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--tz-muted);text-decoration:none;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-muted)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('OTP Login') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:72px 0;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:40px;">
                <div style="text-align:center;margin-bottom:28px;">
                    <div style="width:64px;height:64px;border-radius:50%;background:var(--tz-blue-glow);border:1px solid var(--tz-blue);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:var(--tz-blue);">
                        <i class="mdi mdi-cellphone-key"></i>
                    </div>
                    <h3 style="font-size:18px;font-weight:700;color:#fff;margin-bottom:6px;">{{ __('Phone OTP Login') }}</h3>
                    <p style="font-size:13px;color:var(--tz-muted);">{{ __('Enter your phone number to receive a one-time password.') }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="post">
                    @csrf
                    <div style="margin-bottom:16px;">
                        <label style="font-size:12px;font-weight:600;color:var(--tz-muted);display:block;margin-bottom:6px;">{{ __('Phone Number') }}</label>
                        <input type="tel" name="phone" id="phone_number"
                               style="width:100%;padding:10px 14px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);color:var(--tz-text);font-size:14px;font-family:var(--tz-font);outline:none;transition:border-color .2s;"
                               placeholder="{{ __('Your phone number') }}"
                               onfocus="this.style.borderColor='var(--tz-blue)'" onblur="this.style.borderColor='var(--tz-border)'">
                    </div>
                    <button type="submit"
                            style="width:100%;background:var(--tz-blue);color:#fff;border:0;padding:12px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;cursor:pointer;font-family:var(--tz-font);transition:background .2s;margin-bottom:16px;"
                            onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                        <i class="mdi mdi-send-outline"></i> {{ __('Send OTP') }}
                    </button>
                </form>
                <div style="text-align:center;">
                    <a href="{{ route('tenant.user.login') }}" style="font-size:13px;color:var(--tz-blue);text-decoration:none;">
                        <i class="mdi mdi-arrow-left"></i> {{ __('Back to Sign In') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_phone_js('#phone_number') !!}
@endsection
