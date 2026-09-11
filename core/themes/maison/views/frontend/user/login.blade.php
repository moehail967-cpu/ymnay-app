@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')

<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:40px 0 28px;">
    <div class="container">
        <h1 style="font-size:13px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ __('Welcome') }}</h1>
        <h2 style="font-size:30px;font-weight:300;color:var(--ms-dark);margin:0 0 10px;">{{ __('My Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('Sign In') }}</span>
        </div>
    </div>
</div>

<div style="background:var(--ms-cream);padding:64px 0 80px;">
    <div class="container">
        <div class="mx-auto" style="max-width:920px;">
            {!! theme_error_msg() !!}
            {!! theme_flash_msg() !!}

            <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;box-shadow:var(--ms-shadow);">
                <div class="row g-0">

                    {{-- Sign In --}}
                    <div class="col-md-6" style="border-right:1px solid var(--ms-border);">
                        <div style="padding:44px 40px;">
                            <div style="margin-bottom:24px;">
                                <div style="font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('Returning Customer') }}</div>
                                <h2 style="font-size:24px;font-weight:300;color:var(--ms-dark);margin:0;">{{ __('Sign In') }}</h2>
                                <div style="width:32px;height:2px;background:var(--ms-linen);margin-top:10px;"></div>
                            </div>
                            <p style="font-size:13px;color:var(--ms-muted);margin-bottom:28px;line-height:1.6;">{{ __('Welcome back. Enter your credentials to access your Maison account.') }}</p>

                            <form action="" method="post" id="login_form_order_page">
                                @csrf
                                <div class="mb-4">
                                    <label style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;display:block;">{{ __('Email or Username') }}</label>
                                    <input type="text" name="username" placeholder="{{ __('your@email.com') }}"
                                           style="width:100%;padding:11px 14px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:14px;font-family:inherit;outline:none;color:var(--ms-dark);background:#fff;transition:border-color .2s;"
                                           onfocus="this.style.borderColor='var(--ms-linen)'"
                                           onblur="this.style.borderColor='var(--ms-border)'">
                                </div>
                                <div class="mb-4">
                                    <label style="font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;display:block;">{{ __('Password') }}</label>
                                    <div style="position:relative;">
                                        <input type="password" name="password" id="ms_login_pwd" placeholder="••••••••"
                                               style="width:100%;padding:11px 42px 11px 14px;border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:14px;font-family:inherit;outline:none;color:var(--ms-dark);background:#fff;transition:border-color .2s;"
                                               onfocus="this.style.borderColor='var(--ms-linen)'"
                                               onblur="this.style.borderColor='var(--ms-border)'">
                                        <button type="button" onclick="msTogglePwd('ms_login_pwd', this)"
                                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--ms-muted);font-size:16px;padding:0;">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </button>
                                    </div>
                                </div>
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                                    <label style="display:flex;align-items:center;gap:7px;font-size:13px;cursor:pointer;color:var(--ms-charcoal);">
                                        <input type="checkbox" name="remember" style="accent-color:var(--ms-linen-d);width:14px;height:14px;">
                                        {{ __('Remember Me') }}
                                    </label>
                                    <a href="{{ route('tenant.user.forget.password') }}"
                                       style="font-size:12px;color:var(--ms-linen-d);font-weight:600;text-decoration:none;letter-spacing:.02em;">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                </div>
                                <button type="submit" id="login_btn"
                                        style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px;background:var(--ms-dark);color:#fff;border:none;border-radius:var(--ms-radius);font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;cursor:pointer;transition:background .2s;"
                                        onmouseover="this.style.background='var(--ms-linen-d)'"
                                        onmouseout="this.style.background='var(--ms-dark)'">
                                    {{ __('Sign In') }}
                                </button>
                                @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                                <a href="{{ route('tenant.user.login.otp') }}"
                                   style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;background:transparent;color:var(--ms-charcoal);border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:12px;font-weight:600;letter-spacing:.06em;text-transform:uppercase;text-decoration:none;margin-top:10px;transition:all .2s;"
                                   onmouseover="this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)'"
                                   onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-charcoal)'">
                                    {{ __('Login with OTP') }}
                                </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    {{-- Create Account --}}
                    <div class="col-md-6">
                        <div style="padding:44px 40px;background:var(--ms-warm);">
                            <div style="margin-bottom:24px;">
                                <div style="font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('New Here?') }}</div>
                                <h2 style="font-size:24px;font-weight:300;color:var(--ms-dark);margin:0;">{{ __('Create Account') }}</h2>
                                <div style="width:32px;height:2px;background:var(--ms-olive);margin-top:10px;"></div>
                            </div>
                            <p style="font-size:13px;color:var(--ms-muted);margin-bottom:28px;line-height:1.6;">{{ __('Join Maison for a curated shopping experience — track orders, save favourites and enjoy exclusive access.') }}</p>

                            <ul style="list-style:none;padding:0;margin:0 0 28px;display:flex;flex-direction:column;gap:12px;">
                                @foreach([__('Faster checkout with saved addresses'), __('Order history & easy tracking'), __('Exclusive member offers'), __('Curated design inspiration')] as $benefit)
                                <li style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--ms-charcoal);">
                                    <span style="width:20px;height:20px;border-radius:50%;background:var(--ms-surface);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="mdi mdi-check" style="font-size:12px;color:var(--ms-olive);"></i>
                                    </span>
                                    {{ $benefit }}
                                </li>
                                @endforeach
                            </ul>

                            <a href="{{ route('tenant.user.register') }}"
                               style="display:flex;align-items:center;justify-content:center;gap:8px;padding:13px;background:transparent;color:var(--ms-dark);border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:12px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;text-decoration:none;transition:all .2s;"
                               onmouseover="this.style.background='var(--ms-surface)';this.style.borderColor='var(--ms-linen)'"
                               onmouseout="this.style.background='transparent';this.style.borderColor='var(--ms-border)'">
                                {{ __('Create Your Account') }}
                                <i class="mdi mdi-arrow-right" style="font-size:16px;"></i>
                            </a>
                        </div>
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
function msTogglePwd(id, btn) {
    var input = document.getElementById(id);
    var icon  = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
