@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div class="mc-auth-hero">
    <div class="container">
        <h2>{{ __('My Account') }}</h2>

    </div>
</div>

<div class="container py-5">
    <div class="mx-auto" style="max-width:900px;">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="background:#fff;border:1px solid #EAF2F8;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(232,96,60,.06);">
            <div class="row g-0">

                {{-- Sign In --}}
                <div class="col-md-6" style="border-right:1px solid #EAF2F8;">
                    <div style="padding:40px 36px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#EAF2F8;display:flex;align-items:center;justify-content:center;color:#1A85ED;font-size:18px;">
                                <i class="las la-sign-in-alt"></i>
                            </div>
                            <h2 style="font-size:20px;font-weight:700;color:#1a1a1a;margin:0;">{{ __('Sign In') }}</h2>
                        </div>
                        <p style="font-size:13px;color:#888;margin-bottom:24px;">{{ __('Welcome back! Log in to manage your orders.') }}</p>

                        <form action="" method="post" id="login_form_order_page">
                            @csrf
                            <div class="mb-3">
                                <label class="mc-form-label">{{ __('Email or Username') }}</label>
                                <input type="text" name="username" class="mc-form-input" placeholder="{{ __('your@email.com') }}">
                            </div>
                            <div class="mb-3">
                                <label class="mc-form-label">{{ __('Password') }}</label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="el_login_pwd" class="mc-form-input" placeholder="••••••••" style="padding-right:42px;">
                                    <button type="button" onclick="elTogglePwd('el_login_pwd',this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:#888;font-size:16px;padding:0;">
                                        <i class="las la-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                                    <input type="checkbox" name="remember" style="accent-color:#1A85ED;"> {{ __('Remember Me') }}
                                </label>
                                <a href="{{ route('tenant.user.forget.password') }}" style="font-size:13px;color:#1A85ED;font-weight:600;">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button type="submit" id="login_btn" class="mc-btn mc-btn-primary mc-btn-block" style="font-size:15px;padding:13px;">
                                <i class="las la-sign-in-alt"></i> {{ __('Sign In') }}
                            </button>
                            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                            <a href="{{ route('tenant.user.login.otp') }}" class="mc-btn mc-btn-outline mc-btn-block mt-2" style="font-size:14px;padding:11px;">
                                {{ __('Login with OTP') }}
                            </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Create Account --}}
                <div class="col-md-6">
                    <div style="padding:40px 36px;background:#fafafa;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:#EAF2F8;display:flex;align-items:center;justify-content:center;color:#1A85ED;font-size:18px;">
                                <i class="las la-user-plus"></i>
                            </div>
                            <h2 style="font-size:20px;font-weight:700;color:#1a1a1a;margin:0;">{{ __('Create Account') }}</h2>
                        </div>
                        <p style="font-size:13px;color:#888;margin-bottom:24px;">{{ __('Join for faster checkout, order tracking, and exclusive deals.') }}</p>

                        <form action="{{ theme_register_store_url() }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <label class="mc-form-label">{{ __('Full Name') }} <span class="mc-form-required">*</span></label>
                                <input type="text" name="name" class="mc-form-input" placeholder="{{ __('Jane Smith') }}" value="{{ old('name') }}">
                            </div>
                            <div class="mb-3">
                                <label class="mc-form-label">{{ __('Username') }} <span class="mc-form-required">*</span></label>
                                <input type="text" name="username" class="mc-form-input" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}">
                            </div>
                            <div class="mb-3">
                                <label class="mc-form-label">{{ __('Email Address') }} <span class="mc-form-required">*</span></label>
                                <input type="email" name="email" class="mc-form-input" placeholder="{{ __('your@email.com') }}">
                            </div>
                            <div class="mb-3">
                                <label class="mc-form-label">{{ __('Password') }} <span class="mc-form-required">*</span></label>
                                <input type="password" name="password" class="mc-form-input" placeholder="{{ __('Min. 8 characters') }}">
                            </div>
                            <div class="mb-3">
                                <label class="mc-form-label">{{ __('Confirm Password') }} <span class="mc-form-required">*</span></label>
                                <input type="password" name="password_confirmation" class="mc-form-input" placeholder="{{ __('Repeat your password') }}">
                            </div>
                            <button type="submit" id="register" class="mc-btn mc-btn-primary mc-btn-block" style="font-size:15px;padding:13px;">
                                <i class="las la-user-plus"></i> {{ __('Create Account') }}
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
function elTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'las la-eye-slash'; }
    else { input.type = 'password'; icon.className = 'las la-eye'; }
}
</script>
@endsection
