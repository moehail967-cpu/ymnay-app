@extends('layouts.app')

@section('title')
    {{__('Forgot Password')}}
@endsection

@section('content')

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

/* ── Full-height flex host so fp-body can stretch ────────────────── */
.container-scroller { height: 100vh !important; }

/* ── Page background — matches user panel ────────────────────────── */
body {
    background: #ffffff !important;
    min-height: 100vh;
    overflow-x: hidden;
}

/* ── Hero band — same feel as user panel top hero ────────────────── */
.fp-hero-band {
    width: 100%;
    padding: 3rem 2rem 4.5rem;
    background: linear-gradient(120deg,
        #f2f8f5 0%,
        #edf6f0 30%,
        #f5fbf7 60%,
        #fafdf8 80%,
        #fffef8 100%);
    border-bottom: 1px solid #e8f2ec;
    position: relative;
    overflow: hidden;
    text-align: center;
}

/* Subtle orb — top right like user panel */
.fp-hero-band::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 340px; height: 340px;
    border-radius: 50%;
    background: radial-gradient(circle,
        rgba(212,244,224,0.55) 0%,
        rgba(255,253,220,0.2) 50%,
        transparent 70%);
    pointer-events: none;
}

/* Dot grid — very faint */
.fp-hero-band::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: radial-gradient(rgba(26,92,78,0.06) 1px, transparent 1px);
    background-size: 26px 26px;
    pointer-events: none;
}

.fp-hero-band h1 {
    position: relative;
    z-index: 1;
    font-size: 1.625rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 0.5rem;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.fp-hero-band p {
    position: relative;
    z-index: 1;
    font-size: 0.9rem;
    color: #6b7280;
    margin: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

.fp-hero-band .fp-breadcrumb {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #9ca3af;
    margin-top: 0.375rem;
    font-family: 'Inter', sans-serif;
}

.fp-hero-band .fp-breadcrumb a {
    color: #6b7280;
    text-decoration: none;
    font-weight: 500;
}

.fp-hero-band .fp-breadcrumb a:hover { color: #1a5c4e; }

.fp-hero-band .fp-breadcrumb span { font-size: 0.45rem; color: #d1d5db; }

/* ── Form section ────────────────────────────────────────────────── */
.fp-body {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1.5rem;
    background: #ffffff;
}

/* ── Card wrapper ────────────────────────────────────────────────── */
.fp-wrap {
    width: 100%;
    max-width: 420px;
    position: relative;
    z-index: 1;
}

/* ── Card ────────────────────────────────────────────────────────── */
.fp-card {
    background: #ffffff;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow:
        0 4px 6px -1px rgba(26,92,78,0.05),
        0 16px 40px -8px rgba(26,92,78,0.10);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    transition: box-shadow .3s ease, transform .3s ease;
}
.fp-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px -8px rgba(45, 106, 79, 0.70);
}

/* Top accent — matches user panel sidebar color */
.fp-card__accent {
    height: 3.5px;
    background: linear-gradient(90deg, #1a5c4e 0%, #2db888 50%, #1a5c4e 100%);
}

.fp-card__body {
    padding: 2rem 2.25rem 2.25rem;
}

/* ── Logo ────────────────────────────────────────────────────────── */
.fp-logo { margin-bottom: 1.625rem; }
.fp-logo img { max-height: 36px; }

/* ── Icon badge — matches user panel stat card icons ─────────────── */
.fp-icon {
    width: 46px; height: 46px;
    border-radius: 50%;
    background: #e8f5ef;
    border: 1.5px solid #c5e8d8;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 1.25rem;
}
.fp-icon i { font-size: 1.25rem; color: #1a5c4e; }

/* ── Heading ─────────────────────────────────────────────────────── */
.fp-card h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.02em;
    margin: 0 0 0.375rem;
}
.fp-sub {
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.65;
    margin: 0 0 1.625rem;
}

/* Thin rule */
.fp-rule {
    height: 1px;
    background: linear-gradient(90deg, #e8f2ec, #f0f8f4 60%, #e8f2ec);
    margin-bottom: 1.5rem;
}

/* ── Field ───────────────────────────────────────────────────────── */
.fp-field { margin-bottom: 1.125rem; }
.fp-field label {
    display: block;
    font-size: 0.8rem; font-weight: 600;
    color: #374151; margin-bottom: 0.375rem;
}

.fp-input-wrap { position: relative; }
.fp-input-wrap .ico {
    position: absolute; left: 0.825rem; top: 50%;
    transform: translateY(-50%);
    color: #a8c5b8; font-size: 1rem;
    pointer-events: none; transition: color .2s;
}
.fp-input-wrap:focus-within .ico { color: #1a5c4e; }

.fp-input-wrap input {
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
.fp-input-wrap input:focus {
    background: #fff;
    border-color: #1a5c4e;
    box-shadow: 0 0 0 3px rgba(26,92,78,0.09);
}
.fp-input-wrap input::placeholder { color: #b0c9be; }

/* ── Button — dark teal, matches user panel "Create Shop" button ── */
.fp-btn {
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
.fp-btn:hover {
    background: #103d34;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px -5px rgba(26,92,78,0.5);
}
.fp-btn:active { transform: translateY(0); }
.fp-btn.is-loading { opacity: .7; pointer-events: none; }

.fp-spinner {
    display: none;
    width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,.35);
    border-top-color: #fff;
    border-radius: 50%;
    animation: fp-spin .7s linear infinite;
}
.fp-btn.is-loading .fp-spinner { display: block; }
@keyframes fp-spin { to { transform: rotate(360deg); } }

/* ── Back link ───────────────────────────────────────────────────── */
.fp-back {
    display: flex; align-items: center; justify-content: center; gap: 0.35rem;
    margin-top: 1.25rem;
    font-size: 0.8375rem; font-weight: 600;
    color: #6b7280; text-decoration: none;
    transition: color .2s;
}
.fp-back:hover { color: #1a5c4e; text-decoration: none; }
.fp-back i { font-size: 0.9375rem; }
</style>

@php
    $route = is_null(tenant())
        ? route('landlord.admin.forget.password')
        : route('tenant.admin.forget.password');

    $loginRoute = is_null(tenant())
        ? route('landlord.admin.login')
        : route('tenant.admin.login');
@endphp



{{-- Form --}}
<div class="fp-body">
    <div class="fp-wrap">
        <div class="fp-card">
            <div class="fp-card__body">

                <div class="fp-logo">
                    {!! render_image_markup_by_attachment_id(get_static_option('site_logo')) !!}
                </div>

                <div class="fp-icon">
                    <i class="mdi mdi-lock-reset"></i>
                </div>

                <h2>{{__('Forgot password?')}}</h2>
                <p class="fp-sub">{{__("Enter your username and we'll send a reset link to your registered email.")}}</p>

                <div class="fp-rule"></div>

                <x-error-msg/>
                <x-flash-msg/>

                <form action="{{ $route }}" method="post" id="fp-form">
                    @csrf

                    <div class="fp-field">
                        <label for="fp_username">{{__('Username')}}</label>
                        <div class="fp-input-wrap">
                            <i class="mdi mdi-account-outline ico"></i>
                            <input type="text" id="fp_username" name="username"
                                   placeholder="{{__('Enter your username')}}"
                                   autocomplete="username" required />
                        </div>
                    </div>

                    <button type="submit" class="fp-btn" id="fp_btn">
                        <span class="fp-spinner"></span>
                        <span id="fp_btn_text">{{__('Send Reset Link')}}</span>
                    </button>

                </form>

                <a href="{{ $loginRoute }}" class="fp-back">
                    <i class="mdi mdi-arrow-left"></i> {{__('Back to sign in')}}
                </a>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
(function () {
    var btn  = document.getElementById('fp_btn');
    var text = document.getElementById('fp_btn_text');
    var form = document.getElementById('fp-form');
    btn.addEventListener('click', function () {
        if (!form.checkValidity()) return;
        btn.classList.add('is-loading');
        text.textContent = '{{__("Sending…")}}';
    });
})();
</script>
@endsection
