@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ __('My Account') }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="mx-auto" style="max-width:960px;">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;box-shadow:var(--sz-shadow-sm);">
            <div class="row g-0">

                {{-- Sign In --}}
                <div class="col-md-6" style="border-right:2px solid var(--sz-border);">
                    <div style="padding:40px 36px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <div style="width:40px;height:40px;background:var(--sz-red);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;border-radius:var(--sz-radius);">
                                <i class="mdi mdi-login-variant"></i>
                            </div>
                            <h2 style="font-family:var(--sz-font-head);font-size:22px;font-weight:700;color:var(--sz-dark);margin:0;text-transform:uppercase;letter-spacing:2px;">{{ __('Sign In') }}</h2>
                        </div>
                        <p style="font-size:13px;color:var(--sz-muted);margin-bottom:28px;">{{ __('Welcome back! Log in to manage your orders.') }}</p>

                        @php $inp = 'width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:14px;font-family:var(--sz-font-body);outline:none;transition:border-color .2s;'; @endphp

                        <form action="" method="post" id="login_form_order_page">
                            @csrf
                            <div class="mb-3">
                                <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Email or Username') }}</label>
                                <input type="text" name="username" placeholder="{{ __('your@email.com') }}" style="{{ $inp }}"
                                       onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Password') }}</label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="sz_login_pwd" placeholder="••••••••" style="{{ $inp }}padding-right:42px;"
                                           onfocus="this.style.borderColor='var(--sz-red)'" onblur="this.style.borderColor='var(--sz-border)'">
                                    <button type="button" onclick="szTogglePwd('sz_login_pwd',this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--sz-muted);font-size:16px;padding:0;">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                                    <input type="checkbox" name="remember" style="accent-color:var(--sz-red);"> {{ __('Remember Me') }}
                                </label>
                                <a href="{{ route('tenant.user.forget.password') }}" style="font-size:13px;color:var(--sz-red);font-weight:600;">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button type="submit" id="login_btn" class="sz-btn sz-btn-red w-100" style="justify-content:center;font-size:14px;">
                                <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                            </button>
                            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                            <a href="{{ route('tenant.user.login.otp') }}" class="sz-btn sz-btn-outline w-100 mt-2" style="justify-content:center;font-size:13px;">
                                {{ __('Login with OTP') }}
                            </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Create Account --}}
                <div class="col-md-6">
                    <div style="padding:40px 36px;background:var(--sz-bg);">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <div style="width:40px;height:40px;background:var(--sz-navy);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;border-radius:var(--sz-radius);">
                                <i class="mdi mdi-account-plus-outline"></i>
                            </div>
                            <h2 style="font-family:var(--sz-font-head);font-size:22px;font-weight:700;color:var(--sz-dark);margin:0;text-transform:uppercase;letter-spacing:2px;">{{ __('Create Account') }}</h2>
                        </div>
                        <p style="font-size:13px;color:var(--sz-muted);margin-bottom:28px;">{{ __('Join SportZone for exclusive deals, training tips and early access to new gear.') }}</p>

                        @php $inputStyleReg = 'width:100%;padding:10px 14px;border:2px solid var(--sz-border);border-radius:var(--sz-radius);font-size:14px;font-family:var(--sz-font-body);outline:none;background:#fff;'; @endphp

                        <form action="{{ theme_register_store_url() }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Full Name') }} <span style="color:var(--sz-red);">*</span></label>
                                <input type="text" name="name" style="{{ $inputStyleReg }}" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Username') }} <span style="color:var(--sz-red);">*</span></label>
                                <input type="text" name="username" style="{{ $inputStyleReg }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                            <div class="mb-3">
                                <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Email Address') }} <span style="color:var(--sz-red);">*</span></label>
                                <input type="email" name="email" style="{{ $inputStyleReg }}" placeholder="{{ __('your@email.com') }}">
                            </div>
                            <div class="mb-3">
                                <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Password') }} <span style="color:var(--sz-red);">*</span></label>
                                <input type="password" name="password" style="{{ $inputStyleReg }}" placeholder="{{ __('Min. 8 characters') }}">
                            </div>
                            <div class="mb-3">
                                <label style="font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);margin-bottom:6px;display:block;">{{ __('Confirm Password') }} <span style="color:var(--sz-red);">*</span></label>
                                <input type="password" name="password_confirmation" style="{{ $inputStyleReg }}" placeholder="{{ __('Repeat your password') }}">
                            </div>
                            <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:24px;">
                                <input type="checkbox" id="terms" style="margin-top:3px;accent-color:var(--sz-red);">
                                <label for="terms" style="font-size:12px;color:var(--sz-muted);cursor:pointer;line-height:1.5;">
                                    {{ __('I agree to the') }}
                                    <a href="#" style="color:var(--sz-red);">{{ __('Terms of Service') }}</a> &amp;
                                    <a href="#" style="color:var(--sz-red);">{{ __('Privacy Policy') }}</a>
                                </label>
                            </div>
                            <button type="submit" id="register" class="sz-btn sz-btn-navy w-100" style="justify-content:center;font-size:14px;">
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
function szTogglePwd(id, btn) {
    var input = document.getElementById(id), icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
