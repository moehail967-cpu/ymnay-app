@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div style="background:var(--pf-teal-light);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:28px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ __('My Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--pf-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="mx-auto" style="max-width:900px;">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="background:#fff;border:1px solid var(--pf-border);border-radius:var(--pf-radius-xl);overflow:hidden;box-shadow:var(--pf-shadow);">
            <div class="row g-0">

                {{-- Sign In --}}
                <div class="col-md-6" style="border-right:1px solid var(--pf-border);">
                    <div style="padding:40px 36px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--pf-teal-light);display:flex;align-items:center;justify-content:center;color:var(--pf-teal);font-size:18px;">
                                <i class="mdi mdi-login-variant"></i>
                            </div>
                            <h2 style="font-size:20px;font-weight:700;color:var(--pf-dark);margin:0;">{{ __('Sign In') }}</h2>
                        </div>
                        <p style="font-size:13px;color:var(--pf-muted);margin-bottom:24px;">{{ __('Welcome back! Log in to manage your orders.') }}</p>

                        <form action="" method="post" id="login_form_order_page">
                            @csrf
                            <div class="mb-3">
                                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);margin-bottom:6px;display:block;">{{ __('Email or Username') }}</label>
                                <input type="text" name="username" placeholder="{{ __('your@email.com') }}"
                                       style="width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;transition:border-color .2s;"
                                       onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);margin-bottom:6px;display:block;">{{ __('Password') }}</label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="pf_login_pwd" placeholder="••••••••"
                                           style="width:100%;padding:10px 42px 10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;transition:border-color .2s;"
                                           onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
                                    <button type="button" onclick="pfTogglePwd('pf_login_pwd',this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--pf-muted);font-size:16px;padding:0;">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                                    <input type="checkbox" name="remember" style="accent-color:var(--pf-teal);"> {{ __('Remember Me') }}
                                </label>
                                <a href="{{ route('tenant.user.forget.password') }}" style="font-size:13px;color:var(--pf-teal);font-weight:600;">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button type="submit" id="login_btn" class="pf-btn pf-btn-teal w-100 justify-content-center" style="font-size:15px;padding:13px;">
                                <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                            </button>
                            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                            <a href="{{ route('tenant.user.login.otp') }}" class="pf-btn pf-btn-outline w-100 justify-content-center mt-2" style="font-size:14px;padding:11px;">
                                {{ __('Login with OTP') }}
                            </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Create Account --}}
                <div class="col-md-6">
                    <div style="padding:40px 36px;background:var(--pf-bg);">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:var(--pf-teal-light);display:flex;align-items:center;justify-content:center;color:var(--pf-teal);font-size:18px;">
                                <i class="mdi mdi-account-plus-outline"></i>
                            </div>
                            <h2 style="font-size:20px;font-weight:700;color:var(--pf-dark);margin:0;">{{ __('Create Account') }}</h2>
                        </div>
                        <p style="font-size:13px;color:var(--pf-muted);margin-bottom:24px;">{{ __('Join for faster checkout, order tracking, and exclusive health tips.') }}</p>

                        @php $inputStyleReg = 'width:100%;padding:10px 14px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:14px;font-family:var(--pf-font);outline:none;background:#fff;'; @endphp

                        <form action="{{ theme_register_store_url() }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);margin-bottom:6px;display:block;">{{ __('Full Name') }} <span style="color:var(--pf-teal);">*</span></label>
                                <input type="text" name="name" style="{{ $inputStyleReg }}" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);margin-bottom:6px;display:block;">{{ __('Username') }} <span style="color:var(--pf-teal);">*</span></label>
                                <input type="text" name="username" style="{{ $inputStyleReg }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);margin-bottom:6px;display:block;">{{ __('Email Address') }} <span style="color:var(--pf-teal);">*</span></label>
                                <input type="email" name="email" style="{{ $inputStyleReg }}" placeholder="{{ __('your@email.com') }}">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);margin-bottom:6px;display:block;">{{ __('Password') }} <span style="color:var(--pf-teal);">*</span></label>
                                <input type="password" name="password" style="{{ $inputStyleReg }}" placeholder="{{ __('Min. 8 characters') }}">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:13px;font-weight:600;color:var(--pf-dark);margin-bottom:6px;display:block;">{{ __('Confirm Password') }} <span style="color:var(--pf-teal);">*</span></label>
                                <input type="password" name="password_confirmation" style="{{ $inputStyleReg }}" placeholder="{{ __('Repeat your password') }}">
                            </div>
                            <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:20px;">
                                <input type="checkbox" id="terms" style="margin-top:3px;accent-color:var(--pf-teal);">
                                <label for="terms" style="font-size:12px;color:var(--pf-muted);cursor:pointer;line-height:1.5;">
                                    {{ __('I agree to the') }}
                                    <a href="#" style="color:var(--pf-teal);">{{ __('Terms of Service') }}</a> &amp;
                                    <a href="#" style="color:var(--pf-teal);">{{ __('Privacy Policy') }}</a>
                                </label>
                            </div>
                            <button type="submit" id="register" class="pf-btn pf-btn-teal w-100 justify-content-center" style="font-size:15px;padding:13px;">
                                <i class="mdi mdi-account-plus-outline"></i> {{ __('Create Account') }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{!! theme_ajax_login_js() !!}
<script>
function pfTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
