@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection

@section('content')
<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('My Account') }}</h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="mx-auto" style="max-width:900px;">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);">
            <div class="row g-0">

                {{-- Sign In --}}
                <div class="col-md-6" style="border-right:1px solid var(--gc-border);">
                    <div style="padding:44px 40px;">
                        <span style="display:block;font-size:10px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('Welcome Back') }}</span>
                        <h2 style="font-size:22px;font-weight:400;color:var(--gc-dark);margin:0 0 6px;font-style:italic;">{{ __('Sign In') }}</h2>
                        <p style="font-size:13px;color:var(--gc-muted);margin-bottom:28px;font-style:italic;">{{ __('Access your jewellery journey.') }}</p>

                        @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;outline:none;transition:border-color .2s;background:#fff;'; $lbl = 'font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--gc-muted);margin-bottom:6px;display:block;'; @endphp

                        <form action="" method="post" id="login_form_order_page">
                            @csrf
                            <div class="mb-3">
                                <label style="{{ $lbl }}">{{ __('Email or Username') }}</label>
                                <input type="text" name="username" placeholder="{{ __('your@email.com') }}" style="{{ $inp }}"
                                       onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="{{ $lbl }}">{{ __('Password') }}</label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="gc_login_pwd" placeholder="••••••••" style="{{ $inp }}padding-right:42px;"
                                           onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                                    <button type="button" onclick="gcTogglePwd('gc_login_pwd',this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--gc-muted);font-size:16px;padding:0;">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </button>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;font-style:italic;">
                                    <input type="checkbox" name="remember" style="accent-color:var(--gc-rose);"> {{ __('Remember Me') }}
                                </label>
                                <a href="{{ route('tenant.user.forget.password') }}" style="font-size:12px;color:var(--gc-rose);font-style:italic;text-decoration:none;">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button type="submit" id="login_btn" class="gc-btn gc-btn-primary" style="width:100%;justify-content:center;">
                                {{ __('Sign In') }}
                            </button>
                            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                            <a href="{{ route('tenant.user.login.otp') }}" class="gc-btn gc-btn-ghost" style="width:100%;justify-content:center;margin-top:10px;display:flex;">
                                {{ __('Login with OTP') }}
                            </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Create Account --}}
                <div class="col-md-6">
                    <div style="padding:44px 40px;background:var(--gc-warm);">
                        <span style="display:block;font-size:10px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('New Here') }}</span>
                        <h2 style="font-size:22px;font-weight:400;color:var(--gc-dark);margin:0 0 6px;font-style:italic;">{{ __('Create Account') }}</h2>
                        <p style="font-size:13px;color:var(--gc-muted);margin-bottom:28px;font-style:italic;">{{ __('Join our community of craft lovers.') }}</p>

                        @php $inputStyleReg = 'width:100%;padding:10px 14px;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);font-size:14px;font-family:Georgia,serif;outline:none;background:#fff;transition:border-color .2s;'; @endphp

                        <form action="{{ theme_register_store_url() }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label style="{{ $lbl }}">{{ __('Full Name') }} <span style="color:var(--gc-rose);">*</span></label>
                                <input type="text" name="name" style="{{ $inputStyleReg }}" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}"
                                       onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="{{ $lbl }}">{{ __('Username') }} <span style="color:var(--gc-rose);">*</span></label>
                                <input type="text" name="username" style="{{ $inputStyleReg }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"
                                       onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="{{ $lbl }}">{{ __('Email Address') }} <span style="color:var(--gc-rose);">*</span></label>
                                <input type="email" name="email" style="{{ $inputStyleReg }}" placeholder="{{ __('your@email.com') }}"
                                       onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="{{ $lbl }}">{{ __('Password') }} <span style="color:var(--gc-rose);">*</span></label>
                                <input type="password" name="password" style="{{ $inputStyleReg }}" placeholder="{{ __('Min. 8 characters') }}"
                                       onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="{{ $lbl }}">{{ __('Confirm Password') }} <span style="color:var(--gc-rose);">*</span></label>
                                <input type="password" name="password_confirmation" style="{{ $inputStyleReg }}" placeholder="{{ __('Repeat your password') }}"
                                       onfocus="this.style.borderColor='var(--gc-rose)'" onblur="this.style.borderColor='var(--gc-border)'">
                            </div>
                            <button type="submit" id="register" class="gc-btn gc-btn-primary" style="width:100%;justify-content:center;">
                                {{ __('Create Account') }}
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
function gcTogglePwd(id, btn) {
    var input = document.getElementById(id), icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
