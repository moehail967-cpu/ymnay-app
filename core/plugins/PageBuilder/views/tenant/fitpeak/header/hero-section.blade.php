@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<style>
    :root {
        --fp-green: #00FF41;
        --fp-green-deep: #00CC33;
        --fp-green-dim: #007A1F;
        --fp-green-glow: rgba(0,255,65,.1);
        --fp-bg: #0A0A0A;
        --fp-surface: #111111;
        --fp-card: #161616;
        --fp-text: #E0E0E0;
        --fp-muted: #707070;
        --fp-border: #1A3A1A;
    }

    .fp-hero {
        display: flex;
        align-items: center;
        gap: 60px;
        background: var(--fp-bg);
        border: 1px solid var(--fp-border);
        border-radius: 4px;
        padding: 72px 64px;
        position: relative;
        overflow: hidden;
    }
    .fp-hero::before {
        content: '';
        position: absolute;
        top: -120px;
        right: -80px;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, var(--fp-green-glow) 0%, transparent 70%);
        pointer-events: none;
    }
    .fp-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--fp-green), transparent);
    }
    .fp-hero-content { flex: 1; position: relative; z-index: 1; }
    .fp-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--fp-green-glow);
        color: var(--fp-green);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        padding: 6px 14px;
        border-radius: 2px;
        border: 1px solid var(--fp-border);
        margin-bottom: 20px;
    }
    .fp-hero-title {
        font-size: clamp(32px, 4.5vw, 60px);
        font-weight: 900;
        color: var(--fp-text);
        line-height: 1.1;
        margin-bottom: 18px;
        letter-spacing: -1px;
        text-transform: uppercase;
    }
    .fp-hero-title .fp-accent { color: var(--fp-green); }
    .fp-hero-sub {
        font-size: 15px;
        color: var(--fp-muted);
        line-height: 1.75;
        max-width: 440px;
        margin-bottom: 36px;
    }
    .fp-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all .2s;
        border: 2px solid transparent;
    }
    .fp-btn-primary {
        background: var(--fp-green);
        color: #000;
        border-color: var(--fp-green);
    }
    .fp-btn-primary:hover {
        background: var(--fp-green-deep);
        border-color: var(--fp-green-deep);
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0,255,65,.3);
    }
    .fp-btn-outline {
        background: transparent;
        color: var(--fp-text);
        border-color: var(--fp-border);
    }
    .fp-btn-outline:hover {
        border-color: var(--fp-green);
        color: var(--fp-green);
        transform: translateY(-2px);
    }
    .fp-hero-img {
        flex: 0 0 420px;
        max-width: 420px;
        position: relative;
        z-index: 1;
    }
    .fp-hero-img img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid var(--fp-border);
        box-shadow: 0 0 40px rgba(0,255,65,.08);
    }
    .fp-hero-placeholder {
        width: 100%;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        background: var(--fp-card);
        border-radius: 4px;
        border: 1px solid var(--fp-border);
    }
    @media (max-width: 991px) {
        .fp-hero { flex-direction: column; padding: 44px 24px; gap: 36px; }
        .fp-hero-img { flex: none; max-width: 300px; }
    }
</style>

<div style="background: var(--fp-bg, #0A0A0A); padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">
        <div class="fp-hero">
            <div class="fp-hero-content">
                @if(!empty($data['badge_text']))
                    <div class="fp-hero-badge">
                        <span style="display:inline-block;width:8px;height:8px;background:var(--fp-green);border-radius:50%;animation:fp-pulse 1.5s infinite;"></span>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h1 class="fp-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
                @endif

                @if(!empty($data['subtitle']))
                    <p class="fp-hero-sub">{{ $data['subtitle'] }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}" class="fp-btn fp-btn-primary">
                            {{ $data['button_text'] }}
                            <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}" class="fp-btn fp-btn-outline">
                            {{ $data['button2_text'] }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="fp-hero-img">
                @if(!empty($hero_img_url))
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}">
                @else
                    <div class="fp-hero-placeholder">💪</div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fp-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(1.4); }
}
</style>
