@extends('layouts.app')

@section('title')
    {{__('Login')}}
@endsection

@section('content')
@php
$username = "";
$password = "";
if(preg_match('/(nazmart|hexfashion)/',url('/'))){
    $username = "super_admin";
    $password = "12345678";
}
@endphp

<style>
/* ── Wrapper ─────────────────────────────────────────────────────── */
.container-scroller,
.page-body-wrapper.full-page-wrapper,
.content-wrapper.auth {
    min-height: 100vh !important;
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: stretch !important;
    justify-content: flex-start !important;
    padding: 0 !important;
    margin: 0 !important;
    background: transparent !important;
}

/* ── Full-height flex host so body can stretch ────────────────── */
.container-scroller { height: 100vh !important; }

/* ── Page background ────────────────────────────────────────────── */
body {
    background: #ffffff !important;
    min-height: 100vh;
    overflow-x: hidden;
}

/* ── Form section ────────────────────────────────────────────────── */
.lg-body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    background: #ffffff;
}

/* ── Card wrapper ────────────────────────────────────────────────── */
.lg-wrap {
    width: 100%;
    max-width: 420px;
    position: relative;
    z-index: 1;
}

/* ── Card ────────────────────────────────────────────────────────── */
.lg-card {
    background: #ffffff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow:
        0 4px 6px -1px rgba(26,92,78,0.05),
        0 16px 40px -8px rgba(26,92,78,0.10);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    transition: box-shadow .3s ease, transform .3s ease;
}
.lg-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px -8px rgba(45, 106, 79, 0.70);
}



.lg-card__body {
    padding: 2rem 2.25rem 2.25rem;
}

/* ── Logo ────────────────────────────────────────────────────────── */
.lg-logo { margin-bottom: 1.625rem; }
.lg-logo img { max-height: 36px; }

/* ── Icon badge ──────────────────────────────────────────────────── */
.lg-icon {
    width: 46px; height: 46px;
    border-radius: 50%;
    background: #e8f5ef;
    border: 1.5px solid #c5e8d8;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.25rem;
}
.lg-icon i { font-size: 1.25rem; color: #1a5c4e; }

/* ── Heading ─────────────────────────────────────────────────────── */
.lg-card h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 0.375rem;
}
.lg-sub {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.65;
    margin: 0 0 1.625rem;
}

/* Thin rule */
.lg-rule {
    height: 1px;
    background: linear-gradient(90deg, #e8f2ec, #f0f8f4 60%, #e8f2ec);
    margin-bottom: 1.5rem;
}

/* ── Field ───────────────────────────────────────────────────────── */
.lg-field { margin-bottom: 1.125rem; }
.lg-field label {
    display: block;
    font-size: 0.8rem; font-weight: 600;
    color: #374151; margin-bottom: 0.375rem;
}

.lg-input-wrap { position: relative; }
.lg-input-wrap .ico {
    position: absolute; left: 0.825rem; top: 50%;
    transform: translateY(-50%);
    color: #a8c5b8; font-size: 1rem;
    pointer-events: none; transition: color .2s;
}
.lg-input-wrap:focus-within .ico { color: #1a5c4e; }

.lg-input-wrap input {
    width: 100%; box-sizing: border-box;
    padding: 0.7rem 0.875rem 0.7rem 2.5rem;
    font-size: 0.875rem; font-family: inherit;
    color: #111827;
    background: #f8fbf9;
    border: 1.5px solid #ddeee6;
    border-radius: 0.625rem;
    outline: none;
    transition: border-color .2s, background .2s, box-shadow .2s;
}
.lg-input-wrap input:focus {
    background: #fff;
    border-color: #1a5c4e;
    box-shadow: 0 0 0 3px rgba(26,92,78,0.09);
}
.lg-input-wrap input::placeholder { color: #b0c9be; }

/* Password toggle */
.lg-pw-toggle {
    position: absolute;
    right: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
    color: #a8c5b8;
    font-size: 1rem;
    line-height: 1;
    transition: color .2s;
}
.lg-pw-toggle:hover { color: #1a5c4e; }

/* Extras row */
.lg-extras {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin: 0.5rem 0 1.375rem;
}
.lg-remember {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    cursor: pointer;
    font-size: 0.8125rem;
    color: #6b7280;
    user-select: none;
    margin: 0;
}
.lg-remember input[type="checkbox"] {
    width: 14px; height: 14px;
    accent-color: #1a5c4e;
    cursor: pointer;
    margin: 0;
}
.lg-forgot {
    font-size: 0.8125rem;
    font-weight: 600;
    color: #1a5c4e;
    text-decoration: none;
    transition: opacity .2s;
}
.lg-forgot:hover { opacity: .7; color: #1a5c4e; text-decoration: none; }

/* ── Button ──────────────────────────────────────────────────────── */
.lg-btn {
    width: 100%; padding: 0.75rem 1.5rem;
    font-size: 0.9rem; font-weight: 600; font-family: inherit;
    color: #fff;
    background: #1a5c4e;
    border: none; border-radius: 0.625rem; cursor: pointer;
    transition: background .2s, transform .12s, box-shadow .2s;
    box-shadow: 0 3px 12px -3px rgba(26,92,78,0.4);
    display: flex; align-items: center; justify-content: center; gap: 0.45rem;
    margin-top: 0.25rem;
    letter-spacing: 0.01em;
}
.lg-btn:hover {
    background: #103d34;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px -5px rgba(26,92,78,0.5);
}
.lg-btn:active { transform: translateY(0); }
.lg-btn.is-loading { opacity: .7; pointer-events: none; }

.lg-spinner {
    display: none;
    width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,.35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: lg-spin .7s linear infinite;
}
.lg-btn.is-loading .lg-spinner { display: block; }
@keyframes lg-spin { to { transform: rotate(360deg); } }

/* Alerts */
#lg-msg-wrapper .lg-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 0.625rem;
    font-size: 0.855rem;
    margin-bottom: 1rem;
    line-height: 1.5;
}
#lg-msg-wrapper .lg-alert-danger {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}
#lg-msg-wrapper .lg-alert i { flex-shrink: 0; font-size: 1rem; margin-top: 1px; }
#lg-msg-wrapper .lg-alert ul { margin: 0; padding: 0; list-style: none; }
#lg-msg-wrapper .lg-alert ul li + li { margin-top: 0.25rem; }

/* Demo credentials */
.lg-demo {
    margin-top: 1.25rem;
    padding: 0.875rem 1rem;
    background: #f8f9fa;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    text-align: center;
}
.lg-demo p { margin: 0; }
.lg-demo__title {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #9ca3af;
    margin-bottom: 0.35rem !important;
}
.lg-demo__info {
    font-size: 0.875rem;
    color: #6b7280;
}
.lg-demo__info strong { color: #111827; }
.lg-demo__dot {
    margin: 0 0.5rem;
    color: #d1d5db;
}
</style>

@php
    $forgotRoute = !is_null(tenant())
        ? route('tenant.admin.forget.password')
        : route('landlord.admin.forget.password');
@endphp

{{-- Form --}}
<div class="lg-body">
    <div class="lg-wrap">
        <div class="lg-card">
            <div class="lg-card__body">
                <div class="lg-logo">
                    {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                </div>

                <div class="lg-icon">
                    <i class="mdi mdi-shield-account-outline"></i>
                </div>

                <h2>{{__('Sign in')}}</h2>
                <p class="lg-sub">{{__('Enter your credentials to access the dashboard.')}}</p>

                <div class="lg-rule"></div>

                <div id="lg-msg-wrapper"></div>
                <x-error-msg/>
                <x-flash-msg/>

                <form id="lg-form" action="#" method="post" novalidate>

                    <div class="lg-field">
                        <label for="login_email">{{__('Email address')}}</label>
                        <div class="lg-input-wrap">
                            <i class="mdi mdi-email-outline ico"></i>
                            <input type="email" id="login_email" name="email"
                                   value="{{$username}}" placeholder="{{ __('you@example.com') }}"
                                   autocomplete="email" />
                        </div>
                    </div>

                    <div class="lg-field">
                        <label for="login_password">{{__('Password')}}</label>
                        <div class="lg-input-wrap">
                            <i class="mdi mdi-lock-outline ico"></i>
                            <input type="password" id="login_password" name="password"
                                   value="{{$password}}" placeholder="••••••••"
                                   autocomplete="current-password" />
                            <button type="button" class="lg-pw-toggle" id="pw_toggle"
                                    title="{{__('Toggle password visibility')}}">
                                <i class="mdi mdi-eye-outline" id="pw_icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="lg-extras">
                        <label class="lg-remember">
                            <input type="checkbox" name="remember">
                            {{__('Keep me signed in')}}
                        </label>
                        <a href="{{$forgotRoute}}" class="lg-forgot">{{__('Forgot password?')}}</a>
                    </div>

                    <button type="submit" class="lg-btn" id="login_submit_btn">
                        <span class="lg-spinner"></span>
                        <span id="login_btn_text">{{__('Sign in')}}</span>
                    </button>

                </form>
                @if($username)
                {{-- Demo credentials --}}
                <div class="lg-demo">
                    <p class="lg-demo__title">{{__('Demo Access')}}</p>
                    <p class="lg-demo__info">
                        {{__('Username:')}} <strong>{{$username}}</strong>
{{--                        <span class="lg-demo__dot">&middot;</span>--}}
                        <br>
                        {{__('Password:')}} <strong>{{$password}}</strong>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    // Password show/hide
    var pwToggle = document.getElementById('pw_toggle');
    var pwField  = document.getElementById('login_password');
    var pwIcon   = document.getElementById('pw_icon');
    pwToggle.addEventListener('click', function () {
        var show = pwField.type === 'password';
        pwField.type  = show ? 'text' : 'password';
        pwIcon.className = show ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline';
    });

    // Login
    var btn     = document.getElementById('login_submit_btn');
    var btnText = document.getElementById('login_btn_text');
    var msgWrap = document.getElementById('lg-msg-wrapper');

    btn.addEventListener('click', function (e) {
        e.preventDefault();
        msgWrap.innerHTML = '';
        btn.classList.add('is-loading');
        btnText.textContent = '{{__("Signing in…")}}';

        axios({
            url: '{{route(route_prefix()."admin.login")}}',
            method: 'post',
            responseType: 'json',
            data: {
                email:    document.getElementById('login_email').value,
                password: document.getElementById('login_password').value,
                remember: document.querySelector('input[name="remember"]').checked ? 1 : 0,
            }
        }).then(function () {
            btnText.textContent = '{{__("Redirecting…")}}';
            window.location.reload();
        }).catch(function (error) {
            btn.classList.remove('is-loading');
            btnText.textContent = '{{__("Sign in")}}';

            var data   = error.response && error.response.data;
            var errors = data && data.errors;

            if (!errors) {
                var msg = (data && data.message) || '{{__("An error occurred. Please try again.")}}';
                msgWrap.innerHTML = '<div class="lg-alert lg-alert-danger"><i class="mdi mdi-alert-circle-outline"></i><span>' + msg + '</span></div>';
                return;
            }

            var html = '<div class="lg-alert lg-alert-danger"><i class="mdi mdi-alert-circle-outline"></i><ul>';
            Object.values(errors).forEach(function (msgs) {
                (Array.isArray(msgs) ? msgs : [msgs]).forEach(function (m) {
                    html += '<li>' + m + '</li>';
                });
            });
            html += '</ul></div>';
            msgWrap.innerHTML = html;
        });
    });
})();
</script>
@endsection
