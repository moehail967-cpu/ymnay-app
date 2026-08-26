@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('OTP Sign In') }} @endsection
@section('page-title') {{ __('OTP Sign In') }} @endsection

@section('content')
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ __('OTP Sign In') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ __('OTP Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:44px;box-shadow:var(--gl-shadow);">

                <div style="text-align:center;margin-bottom:32px;">
                    <span style="display:inline-flex;width:56px;height:56px;background:var(--gl-gold-pale);border-radius:50%;align-items:center;justify-content:center;color:var(--gl-gold);font-size:24px;margin-bottom:16px;">
                        <i class="mdi mdi-cellphone-message"></i>
                    </span>
                    <h2 style="font-size:22px;font-weight:300;color:var(--gl-dark);margin:0 0 8px;letter-spacing:-.3px;">{{ __('Sign In with OTP') }}</h2>
                    <p style="font-size:13px;color:var(--gl-muted);margin:0;line-height:1.6;">{{ __("Enter your phone number and we'll send you a one-time code") }}</p>
                </div>

                {!! theme_error_msg() !!}
                {!! theme_flash_msg() !!}

                <form action="{{ route('tenant.user.login.otp') }}" method="post" id="login_form_order_page">
                    @csrf
                    <div class="error-wrap mb-3"></div>

                    <div class="mb-4">
                        <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Phone Number') }} <span style="color:var(--gl-gold);">*</span></label>
                        <input type="tel" name="phone" id="telephone" value="{{ old('phone') }}"
                               placeholder="{{ __('Enter your phone number') }}"
                               style="width:100%;padding:11px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                    </div>

                    <button type="submit" id="login_btn"
                            style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                            onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                        <i class="mdi mdi-send"></i> {{ __('Send OTP') }}
                    </button>
                </form>

                <p style="text-align:center;font-size:13px;color:var(--gl-muted);margin-top:20px;">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('tenant.user.login') }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Sign In') }}</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_phone_js('#telephone', 'login_btn') !!}
@endsection
