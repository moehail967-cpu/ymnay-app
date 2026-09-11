@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div style="background:var(--tr-bark);padding:24px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:26px;font-weight:900;color:#fff;margin-bottom:6px;">{{ __('My Account') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="mx-auto" style="max-width:960px;">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;box-shadow:var(--tr-shadow);">
            <div class="row g-0">
                {{-- Sign In --}}
                <div class="col-md-6" style="border-right:1px solid var(--tr-border);">
                    <div style="padding:40px 36px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <div style="width:40px;height:40px;border-radius:var(--tr-radius);background:var(--tr-olive);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;">
                                <i class="mdi mdi-login-variant"></i>
                            </div>
                            <h2 style="font-size:20px;font-weight:900;color:var(--tr-bark);margin:0;">{{ __('Sign In') }}</h2>
                        </div>
                        <p style="font-size:13px;color:var(--tr-stone);margin-bottom:28px;">{{ __('Welcome back, explorer! Log in to manage your orders.') }}</p>

                        @php $inp = 'width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;font-family:inherit;outline:none;transition:border-color .2s;'; @endphp

                        <form action="" method="post" id="login_form_order_page">
                            @csrf
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:6px;display:block;">{{ __('Email or Username') }}</label>
                                <input type="text" name="username" placeholder="{{ __('your@email.com') }}" style="{{ $inp }}"
                                       onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                            </div>
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:6px;display:block;">{{ __('Password') }}</label>
                                <div style="position:relative;">
                                    <input type="password" name="password" id="tc_login_pwd" placeholder="••••••••" style="{{ $inp }}padding-right:42px;"
                                           onfocus="this.style.borderColor='var(--tr-olive)'" onblur="this.style.borderColor='var(--tr-border)'">
                                    <button type="button" onclick="tcTogglePwd('tc_login_pwd',this)"
                                            style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--tr-stone);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                                <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer;">
                                    <input type="checkbox" name="remember" style="accent-color:var(--tr-olive);"> {{ __('Remember Me') }}
                                </label>
                                <a href="{{ route('tenant.user.forget.password') }}" style="font-size:13px;color:var(--tr-olive);font-weight:600;">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button type="submit" id="login_btn" class="tr-btn tr-btn-primary" style="width:100%;justify-content:center;">
                                <i class="mdi mdi-login"></i> {{ __('Sign In') }}
                            </button>
                            @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                            <a href="{{ route('tenant.user.login.otp') }}" class="tr-btn" style="width:100%;justify-content:center;margin-top:8px;background:transparent;border:1.5px solid var(--tr-border);color:var(--tr-bark);">
                                {{ __('Login with OTP') }}
                            </a>
                            @endif
                        </form>
                    </div>
                </div>

                {{-- Create Account --}}
                <div class="col-md-6">
                    <div style="padding:40px 36px;background:var(--tr-cream);">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <div style="width:40px;height:40px;border-radius:var(--tr-radius);background:var(--tr-terra);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;">
                                <i class="mdi mdi-account-plus-outline"></i>
                            </div>
                            <h2 style="font-size:20px;font-weight:900;color:var(--tr-bark);margin:0;">{{ __('Create Account') }}</h2>
                        </div>
                        <p style="font-size:13px;color:var(--tr-stone);margin-bottom:28px;">{{ __('Join TrailCo for exclusive trail deals and adventure guides.') }}</p>

                        @php $inputStyleReg = 'width:100%;padding:10px 14px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);font-size:14px;font-family:inherit;outline:none;background:#fff;'; @endphp

                        <form action="{{ theme_register_store_url() }}" method="post">
                            @csrf
                            @foreach([['Full Name','name','text',__('Jane Smith')],['Username','username','text',__('Choose a username')],['Email Address','email','email',__('your@email.com')],['Password','password','password',__('Min. 8 characters')],['Confirm Password','password_confirmation','password',__('Repeat your password')]] as [$lbl,$name,$type,$placeholder])
                            <div class="mb-3">
                                <label style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tr-stone);margin-bottom:6px;display:block;">{{ __($lbl) }} <span style="color:var(--tr-terra);">*</span></label>
                                <input type="{{ $type }}" name="{{ $name }}" style="{{ $inputStyleReg }}" placeholder="{{ $placeholder }}" {{ $name=='name'?'value='.old('name') :($name=='username'?'value='.old('username'):'') }}>
                            </div>
                            @endforeach
                            <button type="submit" id="register" class="tr-btn tr-btn-terra" style="width:100%;justify-content:center;">
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
function tcTogglePwd(id, btn) {
    var input = document.getElementById(id), icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
