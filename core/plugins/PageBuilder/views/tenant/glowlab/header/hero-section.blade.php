@php
    $hero_image   = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt           = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb           = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url     = route('tenant.shop');
@endphp

<style>
    :root {
        --gl-gold: #C9A44C;
        --gl-gold-deep: #A8852E;
        --gl-gold-light: #F5E9D0;
        --gl-gold-pale: #FDF8EE;
        --gl-bg: #FAFAFA;
        --gl-dark: #1A1208;
        --gl-muted: #6B5E4A;
        --gl-border: #E8DCC8;
    }

    .gl-hero {
        display: flex;
        align-items: center;
        gap: 56px;
        background: linear-gradient(135deg, var(--gl-gold-pale) 0%, #fff 100%);
        border: 1px solid var(--gl-border);
        padding: 72px 64px;
        position: relative;
        overflow: hidden;
    }
    .gl-hero::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(201,164,76,.12) 0%, transparent 70%);
        pointer-events: none;
    }
    .gl-hero-content { flex: 1; position: relative; z-index: 1; }
    .gl-hero-visual {
        flex-shrink: 0;
        width: 360px;
        text-align: center;
        font-size: 120px;
        line-height: 1;
        position: relative;
        z-index: 1;
    }
    .gl-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--gl-gold-light);
        color: var(--gl-gold-deep);
        border: 1px solid var(--gl-gold);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        padding: 6px 16px;
        border-radius: 100px;
        margin-bottom: 20px;
    }
    .gl-hero-title {
        font-size: clamp(30px, 4vw, 52px);
        font-weight: 800;
        color: var(--gl-dark);
        line-height: 1.15;
        margin-bottom: 18px;
    }
    .gl-hero-sub {
        font-size: 16px;
        color: var(--gl-muted);
        line-height: 1.75;
        max-width: 500px;
        margin-bottom: 36px;
    }
    .gl-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 700;
        padding: 14px 32px;
        text-decoration: none;
        border: 2px solid transparent;
        cursor: pointer;
        border-radius: 100px;
        transition: all .25s;
        letter-spacing: .02em;
    }
    .gl-btn-primary {
        background: var(--gl-gold);
        color: #fff;
        border-color: var(--gl-gold);
    }
    .gl-btn-primary:hover {
        background: var(--gl-gold-deep);
        border-color: var(--gl-gold-deep);
        color: #fff;
    }
    .gl-btn-outline {
        background: transparent;
        color: var(--gl-gold-deep);
        border-color: var(--gl-gold);
    }
    .gl-btn-outline:hover {
        background: var(--gl-gold);
        color: #fff;
    }
    @media (max-width: 768px) {
        .gl-hero { flex-direction: column; padding: 48px 24px; gap: 32px; }
        .gl-hero-visual { width: 100%; font-size: 80px; }
    }
</style>

<div style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--gl-bg, #FAFAFA);">
    <div class="container">
        <div class="gl-hero">
            <div class="gl-hero-content">
                @if(!empty($data['badge_text']))
                    <div class="gl-hero-badge">
                        <i class="mdi mdi-star-four-points"></i>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h1 class="gl-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
                @endif

                @if(!empty($data['subtitle']))
                    <p class="gl-hero-sub">{{ $data['subtitle'] }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}" class="gl-btn gl-btn-primary">
                            <i class="mdi mdi-shopping-outline"></i>
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}" class="gl-btn gl-btn-outline">
                            {{ $data['button2_text'] }}
                            <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="gl-hero-visual">
                @if(!empty($hero_img_url))
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}"
                         style="max-width:100%; max-height:360px; object-fit:contain;">
                @else
                    <span>✨</span>
                @endif
            </div>
        </div>
    </div>
</div>
