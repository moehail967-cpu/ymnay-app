@php
    $hero_image   = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt           = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb           = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url     = route('tenant.shop');
@endphp

<style>
    :root {
        --lg-gold: #C9A84C;
        --lg-gold-deep: #A88530;
        --lg-gold-light: #F0D98C;
        --lg-bg: #080808;
        --lg-surface: #111111;
        --lg-card: #141414;
        --lg-dark: #E8E0D0;
        --lg-muted: #8A7E6E;
        --lg-border: #2A2410;
        --lg-font-head: 'Cinzel', Georgia, serif;
    }

    .lg-hero {
        display: flex;
        align-items: center;
        gap: 64px;
        background: var(--lg-bg);
        border: 1px solid var(--lg-border);
        padding: 80px 72px;
        position: relative;
        overflow: hidden;
    }
    .lg-hero::before {
        content: '';
        position: absolute;
        top: 50%;
        right: 80px;
        transform: translateY(-50%);
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(201,168,76,.06) 0%, transparent 70%);
        pointer-events: none;
    }
    .lg-hero-content { flex: 1; position: relative; z-index: 1; }
    .lg-hero-visual {
        flex-shrink: 0;
        width: 360px;
        text-align: center;
        font-size: 120px;
        line-height: 1;
        position: relative;
        z-index: 1;
    }
    .lg-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: transparent;
        color: var(--lg-gold);
        border: 1px solid var(--lg-border);
        font-size: 10px;
        font-weight: 600;
        letter-spacing: .2em;
        text-transform: uppercase;
        padding: 7px 18px;
        margin-bottom: 24px;
        font-family: var(--lg-font-head);
    }
    .lg-hero-title {
        font-size: clamp(32px, 4.5vw, 58px);
        font-weight: 700;
        color: var(--lg-dark);
        line-height: 1.15;
        margin-bottom: 20px;
        font-family: var(--lg-font-head);
        letter-spacing: -.01em;
    }
    .lg-hero-sub {
        font-size: 16px;
        color: var(--lg-muted);
        line-height: 1.8;
        max-width: 480px;
        margin-bottom: 40px;
    }
    .lg-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
        padding: 14px 36px;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        letter-spacing: .12em;
        text-transform: uppercase;
        transition: all .25s;
        font-family: var(--lg-font-head);
    }
    .lg-btn-primary {
        background: var(--lg-gold);
        color: #080808;
        border-color: var(--lg-gold);
    }
    .lg-btn-primary:hover {
        background: var(--lg-gold-deep);
        border-color: var(--lg-gold-deep);
        color: #080808;
    }
    .lg-btn-outline {
        background: transparent;
        color: var(--lg-dark);
        border-color: var(--lg-border);
    }
    .lg-btn-outline:hover {
        border-color: var(--lg-gold);
        color: var(--lg-gold);
    }
    @media (max-width: 768px) {
        .lg-hero { flex-direction: column; padding: 48px 24px; gap: 40px; }
        .lg-hero-visual { width: 100%; font-size: 80px; }
    }
</style>

<div style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--lg-bg, #080808);">
    <div class="container">
        <div class="lg-hero">
            <div class="lg-hero-content">
                @if(!empty($data['badge_text']))
                    <div class="lg-hero-badge">
                        <i class="mdi mdi-diamond-outline"></i>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h1 class="lg-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
                @endif

                @if(!empty($data['subtitle']))
                    <p class="lg-hero-sub">{{ $data['subtitle'] }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}" class="lg-btn lg-btn-primary">
                            {{ $data['button_text'] }}
                            <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}" class="lg-btn lg-btn-outline">
                            {{ $data['button2_text'] }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="lg-hero-visual">
                @if(!empty($hero_img_url))
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}"
                         style="max-width:100%; max-height:360px; object-fit:contain;">
                @else
                    <span>💎</span>
                @endif
            </div>
        </div>
    </div>
</div>
