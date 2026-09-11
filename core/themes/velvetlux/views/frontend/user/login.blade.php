@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('My Account') }} @endsection
@section('page-title') {{ __('My Account') }} @endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Account') }}</div>
        <h2 style="font-size:28px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('My Account') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--vl-muted);letter-spacing:1px;text-transform:uppercase;">
            <a href="{{ theme_home_url() }}" style="color:var(--vl-champagne);">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{{ __('My Account') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:64px 0 80px;">
    <div class="mx-auto" style="max-width:900px;">
        {!! theme_error_msg() !!}
        {!! theme_flash_msg() !!}

        @php $inp = 'width:100%;padding:12px 16px;background:var(--vl-surface);border:1px solid var(--vl-border);color:var(--vl-ivory);font-size:14px;font-family:inherit;outline:none;'; $lbl = 'font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-muted);display:block;margin-bottom:10px;'; @endphp

        <div style="border:1px solid var(--vl-border);display:grid;grid-template-columns:1fr 1fr;">

            {{-- Sign In --}}
            <div style="padding:48px 40px;border-right:1px solid var(--vl-border);">
                <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Existing Client') }}</div>
                <h3 style="font-size:22px;font-weight:400;color:var(--vl-ivory);margin:0 0 8px;font-family:'Cormorant Garamond',serif;letter-spacing:1px;">{{ __('Sign In') }}</h3>
                <p style="font-size:13px;color:var(--vl-muted);margin-bottom:32px;">{{ __('Welcome back. Log in to manage your orders and wishlist.') }}</p>

                <form action="" method="post" id="login_form_order_page">
                    @csrf
                    <div class="mb-4">
                        <label style="{{ $lbl }}">{{ __('Email or Username') }}</label>
                        <input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}">
                    </div>
                    <div class="mb-4">
                        <label style="{{ $lbl }}">{{ __('Password') }}</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="vl_login_pwd" style="{{ $inp }}padding-right:42px;" placeholder="••••••••">
                            <button type="button" onclick="vlTogglePwd('vl_login_pwd',this)" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);border:0;background:transparent;cursor:pointer;color:var(--vl-muted);font-size:16px;padding:0;"><i class="mdi mdi-eye-outline"></i></button>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;">
                        <label style="display:flex;align-items:center;gap:6px;font-size:12px;cursor:pointer;color:var(--vl-muted);">
                            <input type="checkbox" name="remember" style="accent-color:var(--vl-champagne);"> {{ __('Remember Me') }}
                        </label>
                        <a href="{{ route('tenant.user.forget.password') }}" style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);">{{ __('Forgot?') }}</a>
                    </div>
                    <button type="submit" id="login_btn" style="width:100%;background:var(--vl-champagne);color:var(--vl-dark);border:0;padding:16px;font-size:10px;letter-spacing:4px;text-transform:uppercase;cursor:pointer;font-family:inherit;">
                        {{ __('Sign In') }}
                    </button>
                    @if(moduleExists('SmsGateway') && get_static_option('otp_login_status'))
                    <a href="{{ route('tenant.user.login.otp') }}" style="display:block;text-align:center;margin-top:12px;font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);text-decoration:none;">{{ __('Login with OTP') }}</a>
                    @endif
                </form>
            </div>

            {{-- Create Account --}}
            <div style="padding:48px 40px;background:var(--vl-surface);">
                <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('New Client') }}</div>
                <h3 style="font-size:22px;font-weight:400;color:var(--vl-ivory);margin:0 0 8px;font-family:'Cormorant Garamond',serif;letter-spacing:1px;">{{ __('Create Account') }}</h3>
                <p style="font-size:13px;color:var(--vl-muted);margin-bottom:32px;">{{ __('Join VelvetLux for exclusive access to curated collections and early releases.') }}</p>

                <form action="{{ theme_register_store_url() }}" method="post">
                    @csrf
                    <div class="mb-4"><label style="{{ $lbl }}">{{ __('Full Name') }} *</label><input type="text" name="name" style="{{ $inp }}" placeholder="{{ __('Your name') }}" value="{{ old('name') }}"></div>
                    <div class="mb-4"><label style="{{ $lbl }}">{{ __('Username') }} *</label><input type="text" name="username" style="{{ $inp }}" placeholder="{{ __('Choose a username') }}" value="{{ old('username') }}"></div>
                    <div class="mb-4"><label style="{{ $lbl }}">{{ __('Email Address') }} *</label><input type="email" name="email" style="{{ $inp }}" placeholder="{{ __('your@email.com') }}"></div>
                    <div class="mb-4"><label style="{{ $lbl }}">{{ __('Password') }} *</label><input type="password" name="password" style="{{ $inp }}" placeholder="{{ __('Min. 8 characters') }}"></div>
                    <div class="mb-4"><label style="{{ $lbl }}">{{ __('Confirm Password') }} *</label><input type="password" name="password_confirmation" style="{{ $inp }}" placeholder="{{ __('Repeat password') }}"></div>
                    <div style="display:flex;align-items:flex-start;gap:8px;margin-bottom:24px;">
                        <input type="checkbox" id="terms" style="margin-top:3px;accent-color:var(--vl-champagne);">
                        <label for="terms" style="font-size:12px;color:var(--vl-muted);cursor:pointer;line-height:1.5;">
                            {{ __('I agree to the') }} <a href="#" style="color:var(--vl-champagne);">{{ __('Terms') }}</a> & <a href="#" style="color:var(--vl-champagne);">{{ __('Privacy Policy') }}</a>
                        </label>
                    </div>
                    <button type="submit" id="register" style="width:100%;background:transparent;color:var(--vl-champagne);border:1px solid var(--vl-champagne);padding:16px;font-size:10px;letter-spacing:4px;text-transform:uppercase;cursor:pointer;font-family:inherit;transition:all .2s;"
                            onmouseover="this.style.background='var(--vl-champagne)';this.style.color='var(--vl-dark)'" onmouseout="this.style.background='transparent';this.style.color='var(--vl-champagne)'">
                        {{ __('Create Account') }}
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).on('click', '#login_btn', function (e) {
    e.preventDefault();
    var btn = $(this), form = $('#login_form_order_page');
    btn.html('{{ __("Please Wait…") }}').css('pointer-events','none');
    $.ajax({
        url: '{{ theme_ajax_login_url() }}', type: 'POST',
        data: { _token: '{{ theme_csrf() }}', username: form.find('[name=username]').val(), password: form.find('[name=password]').val(), remember: form.find('[name=remember]').is(':checked') ? 'on' : '' },
        success: function (data) {
            if (data.status === 'invalid') { btn.html('{{ __("Sign In") }}').css('pointer-events',''); toastr.error(data.msg); }
            else { btn.html('{{ __("Redirecting…") }}'); location.reload(); }
        },
        error: function (xhr) {
            btn.html('{{ __("Sign In") }}').css('pointer-events','');
            try { toastr.error(xhr.responseJSON.message || '{{ __("Something went wrong") }}'); } catch(e) { toastr.error('{{ __("Something went wrong") }}'); }
        }
    });
});
$('#register').closest('form').on('submit', function () {
    var btn = $('#register');
    btn.html('{{ __("Please Wait…") }}').css({'opacity':'.7','pointer-events':'none'});
});

function vlTogglePwd(id, btn) {
    var input = document.getElementById(id); var icon = btn.querySelector('i');
    if (input.type === 'password') { input.type = 'text'; icon.className = 'mdi mdi-eye-off-outline'; }
    else { input.type = 'password'; icon.className = 'mdi mdi-eye-outline'; }
}
</script>
@endsection
