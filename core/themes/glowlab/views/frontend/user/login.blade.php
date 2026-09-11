@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ __('My Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="mx-auto" style="max-width:900px;">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);">
            <div class="row g-0">

                {{-- Sign In --}}
                <div class="col-md-6" style="border-right:1px solid var(--gl-border);">
                    <div style="padding:44px 40px;">
                        <div style="margin-bottom:24px;">
                            <span style="display:inline-block;width:40px;height:40px;background:var(--gl-gold-pale);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gl-gold);font-size:18px;margin-bottom:12px;">
                                <i class="mdi mdi-login-variant"></i>
                            </span>
                            <h2 style="font-size:20px;font-weight:300;color:var(--gl-dark);margin:0 0 4px;letter-spacing:-.3px;">{{ __('Sign In') }}</h2>
                            <p style="font-size:13px;color:var(--gl-muted);margin:0;">{{ __('Welcome back! Access your beauty routine.') }}</p>
                        </div>

                        @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;'; @endphp

                        <form action="" method="post" id="login_form_order_page">
                            @csrf
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Email or Username') }}</label>
                                <input type="text" name="username" placeholder="{{ __('your@email.com') }}" style="{{ $inp }}"
                                       onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Password') }}</label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="gl_login_pwd" placeholder="••••••••" style="{{ $inp }}padding-right:42px;"
                                           onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                                    <button type="button" onclick="glTogglePwd('gl_login_pwd',this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--gl-muted);font-size:16px;padding:0;">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;">
                                    <input type="checkbox" name="remember" style="accent-color:var(--gl-gold);"> {{ __('Remember Me') }}
                                </label>
                                <a href="{{ route('tenant.user.forget.password') }}" style="font-size:12px;color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button type="submit" id="login_btn"
                                    style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                                    onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                                <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                            </button>
                            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                            <a href="{{ route('tenant.user.login.otp') }}"
                               style="display:flex;align-items:center;justify-content:center;width:100%;padding:12px;background:transparent;color:var(--gl-dark);border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:12px;font-weight:600;text-decoration:none;margin-top:10px;transition:border-color .2s;"
                               onmouseover="this.style.borderColor='var(--gl-dark)'" onmouseout="this.style.borderColor='var(--gl-border)'">
                                {{ __('Login with OTP') }}
                            </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Create Account --}}
                <div class="col-md-6">
                    <div style="padding:44px 40px;background:var(--gl-gold-pale);">
                        <div style="margin-bottom:24px;">
                            <span style="display:inline-flex;width:40px;height:40px;background:#fff;border-radius:50%;align-items:center;justify-content:center;color:var(--gl-gold);font-size:18px;margin-bottom:12px;">
                                <i class="mdi mdi-account-plus-outline"></i>
                            </span>
                            <h2 style="font-size:20px;font-weight:300;color:var(--gl-dark);margin:0 0 4px;letter-spacing:-.3px;">{{ __('Create Account') }}</h2>
                            <p style="font-size:13px;color:var(--gl-muted);margin:0;">{{ __('Join for personalized skincare recommendations.') }}</p>
                        </div>

                        @php $inputStyleReg = 'width:100%;padding:10px 14px;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);font-size:14px;font-family:inherit;outline:none;background:#fff;transition:border-color .2s;'; @endphp

                        <form action="{{ theme_register_store_url() }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Full Name') }} <span style="color:var(--gl-gold);">*</span></label>
                                <input type="text" name="name" style="{{ $inputStyleReg }}" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}"
                                       onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Username') }} <span style="color:var(--gl-gold);">*</span></label>
                                <input type="text" name="username" style="{{ $inputStyleReg }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"
                                       onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Email Address') }} <span style="color:var(--gl-gold);">*</span></label>
                                <input type="email" name="email" style="{{ $inputStyleReg }}" placeholder="{{ __('your@email.com') }}"
                                       onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Password') }} <span style="color:var(--gl-gold);">*</span></label>
                                <input type="password" name="password" style="{{ $inputStyleReg }}" placeholder="{{ __('Min. 8 characters') }}"
                                       onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:600;color:var(--gl-dark);margin-bottom:6px;display:block;letter-spacing:.3px;">{{ __('Confirm Password') }} <span style="color:var(--gl-gold);">*</span></label>
                                <input type="password" name="password_confirmation" style="{{ $inputStyleReg }}" placeholder="{{ __('Repeat your password') }}"
                                       onfocus="this.style.borderColor='var(--gl-gold)'" onblur="this.style.borderColor='var(--gl-border)'">
                            </div>
                            <button type="submit" id="register"
                                    style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:var(--gl-dark);color:#fff;border:none;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                                    onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
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
function glTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
