@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('OTP Sign In') }} @endsection
@section('page-title') {{ __('OTP Sign In') }} @endsection

@section('content')
<div style="background:var(--tr-bark);padding:24px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:26px;font-weight:900;color:#fff;margin-bottom:6px;">{{ __('OTP Sign In') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('OTP Sign In') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;box-shadow:var(--tr-shadow);">
                <div style="background:var(--tr-bark);padding:20px 36px;display:flex;align-items:center;gap:14px;">
                    <div style="width:42px;height:42px;border-radius:var(--tr-radius);background:var(--tr-olive);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;">
                        <i class="mdi mdi-cellphone-message"></i>
                    </div>
                    <div>
                        <h2 style="font-size:18px;font-weight:900;color:#fff;margin:0;">{{ __('Sign In with OTP') }}</h2>
                        <p style="font-size:12px;color:rgba(255,255,255,.6);margin:2px 0 0;">{{ __("Enter your phone number to receive a one-time code") }}</p>
                    </div>
                </div>

                <div style="padding:36px;">
                    {!! theme_error_msg() !!}
                    {!! theme_flash_msg() !!}

                    @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;'; @endphp

                    <form action="{{ route('tenant.user.login.otp') }}" method="post" enctype="multipart/form-data" id="login_form_order_page">
                        @csrf
                        <div class="error-wrap mb-3"></div>
                        <div class="mb-4">
                            <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:6px;display:block;">{{ __('Phone Number') }} <span style="color:var(--tr-terra);">*</span></label>
                            <input type="tel" name="phone" id="telephone" style="{{ $inp }}" placeholder="{{ __('Enter your phone number') }}" value="{{ old('phone') }}"
                                   onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                        </div>
                        <button type="submit" id="login_btn" class="tr-btn tr-btn-primary" style="width:100%;justify-content:center;">
                            <i class="mdi mdi-send"></i> {{ __('Send OTP') }}
                        </button>
                    </form>

                    <p style="text-align:center;font-size:13px;color:var(--tr-stone);margin-top:20px;">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('tenant.user.login') }}" style="color:var(--tr-olive);font-weight:700;">{{ __('Sign In') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_phone_js('#telephone', 'login_btn') !!}
@endsection
