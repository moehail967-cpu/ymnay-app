@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<style>
    :root {
        --ms-olive: #7D8C5A;
        --ms-olive-deep: #5A6840;
        --ms-olive-light: #EFF2E8;
        --ms-linen: #F5F0E8;
        --ms-bg: #FAFAF5;
        --ms-warm: #F8F4EE;
        --ms-dark: #2A2416;
        --ms-muted: #6E6048;
        --ms-border: #DDD5C4;
        --ms-accent: #C4956A;
        --ms-font-head: 'Lora', Georgia, serif;
    }

    .ms-hero {
        display: flex;
        align-items: center;
        gap: 60px;
        background: linear-gradient(135deg, var(--ms-linen) 0%, var(--ms-bg) 100%);
        border-radius: 4px;
        padding: 72px 64px;
        position: relative;
        overflow: hidden;
        border-left: 4px solid var(--ms-olive);
    }
    .ms-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        right: 0;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, var(--ms-olive-light) 0%, transparent 70%);
        pointer-events: none;
    }
    .ms-hero-content { flex: 1; position: relative; z-index: 1; }
    .ms-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--ms-olive-light);
        color: var(--ms-olive-deep);
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 6px 14px;
        border-radius: 2px;
        margin-bottom: 20px;
        border-left: 3px solid var(--ms-olive);
    }
    .ms-hero-title {
        font-family: var(--ms-font-head);
        font-size: clamp(36px, 5vw, 64px);
        font-weight: 700;
        color: var(--ms-dark);
        line-height: 1.12;
        margin-bottom: 18px;
        letter-spacing: -.5px;
    }
    .ms-hero-sub {
        font-size: 16px;
        color: var(--ms-muted);
        line-height: 1.75;
        max-width: 420px;
        margin-bottom: 36px;
    }
    .ms-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: .4px;
        text-decoration: none;
        transition: all .2s;
        border: 1.5px solid transparent;
    }
    .ms-btn-primary {
        background: var(--ms-olive);
        color: #fff;
        border-color: var(--ms-olive);
    }
    .ms-btn-primary:hover {
        background: var(--ms-olive-deep);
        border-color: var(--ms-olive-deep);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(125,140,90,.25);
    }
    .ms-btn-outline {
        background: transparent;
        color: var(--ms-dark);
        border-color: var(--ms-border);
    }
    .ms-btn-outline:hover {
        background: var(--ms-warm);
        border-color: var(--ms-olive);
        color: var(--ms-dark);
        transform: translateY(-2px);
    }
    .ms-hero-img {
        flex: 0 0 400px;
        max-width: 400px;
        position: relative;
        z-index: 1;
    }
    .ms-hero-img img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 2px;
        box-shadow: 20px 20px 0 var(--ms-olive-light);
    }
    .ms-hero-placeholder {
        width: 100%;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        background: var(--ms-warm);
        border-radius: 2px;
        box-shadow: 20px 20px 0 var(--ms-olive-light);
    }
    @media (max-width: 991px) {
        .ms-hero { flex-direction: column; padding: 44px 28px; gap: 36px; }
        .ms-hero-img { flex: none; max-width: 320px; }
        .ms-hero-img img, .ms-hero-placeholder { box-shadow: 10px 10px 0 var(--ms-olive-light); }
    }
</style>

<div class="container" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="ms-hero">
        <div class="ms-hero-content">
            @if(!empty($data['badge_text']))
                <div class="ms-hero-badge">
                    🏠 {{ $data['badge_text'] }}
                </div>
            @endif

            @if(!empty($data['title']))
                <h1 class="ms-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
            @endif

            @if(!empty($data['subtitle']))
                <p class="ms-hero-sub">{{ $data['subtitle'] }}</p>
            @endif

            <div class="d-flex gap-3 flex-wrap">
                @if(!empty($data['button_text']))
                    <a href="{{ $data['button_url'] ?? $shop_url }}" class="ms-btn ms-btn-primary">
                        {{ $data['button_text'] }}
                        <i class="mdi mdi-arrow-right"></i>
                    </a>
                @endif
                @if(!empty($data['button2_text']))
                    <a href="{{ $data['button2_url'] ?? '#' }}" class="ms-btn ms-btn-outline">
                        {{ $data['button2_text'] }}
                    </a>
                @endif
            </div>
        </div>

        <div class="ms-hero-img">
            @if(!empty($hero_img_url))
                <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}">
            @else
                <div class="ms-hero-placeholder">🏠</div>
            @endif
        </div>
    </div>
</div>
