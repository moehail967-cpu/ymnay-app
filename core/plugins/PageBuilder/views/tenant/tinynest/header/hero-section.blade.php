@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<style>
    :root {
        --tn-blush: #F2A7C3;
        --tn-blush-light: #FDE8F1;
        --tn-blue: #A7C8F2;
        --tn-blue-light: #E8F3FD;
        --tn-mint: #A7E2CE;
        --tn-mint-light: #E8F8F3;
        --tn-lavender: #C8A7F2;
        --tn-bg: #FFF9FC;
        --tn-dark: #2D1B2E;
        --tn-muted: #7A5E6E;
        --tn-border: #ECCFE0;
    }

    .tn-hero {
        display: flex;
        align-items: center;
        gap: 48px;
        background: linear-gradient(135deg, var(--tn-blush-light) 0%, var(--tn-blue-light) 100%);
        border-radius: 24px;
        padding: 56px 52px;
        overflow: hidden;
        position: relative;
    }
    .tn-hero::before {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 260px;
        height: 260px;
        background: var(--tn-blush);
        opacity: .08;
        border-radius: 50%;
    }
    .tn-hero::after {
        content: '';
        position: absolute;
        bottom: -40px;
        left: 30%;
        width: 180px;
        height: 180px;
        background: var(--tn-blue);
        opacity: .1;
        border-radius: 50%;
    }
    .tn-hero-content { flex: 1; position: relative; z-index: 1; }
    .tn-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        color: var(--tn-muted);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .5px;
        text-transform: uppercase;
        padding: 6px 14px;
        border-radius: 50px;
        border: 1px solid var(--tn-border);
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(242,167,195,.2);
    }
    .tn-hero-title {
        font-size: clamp(32px, 4.5vw, 56px);
        font-weight: 800;
        color: var(--tn-dark);
        line-height: 1.15;
        margin-bottom: 16px;
    }
    .tn-hero-sub {
        font-size: 16px;
        color: var(--tn-muted);
        line-height: 1.7;
        max-width: 440px;
        margin-bottom: 32px;
    }
    .tn-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 28px;
        border-radius: 50px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all .2s;
        border: 2px solid transparent;
    }
    .tn-btn-primary {
        background: var(--tn-blush);
        color: #fff;
        border-color: var(--tn-blush);
    }
    .tn-btn-primary:hover {
        background: #e890b0;
        border-color: #e890b0;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(242,167,195,.4);
    }
    .tn-btn-outline {
        background: #fff;
        color: var(--tn-dark);
        border-color: var(--tn-border);
    }
    .tn-btn-outline:hover {
        background: var(--tn-blush-light);
        border-color: var(--tn-blush);
        color: var(--tn-dark);
        transform: translateY(-2px);
    }
    .tn-hero-img {
        flex: 0 0 420px;
        max-width: 420px;
        position: relative;
        z-index: 1;
    }
    .tn-hero-img img {
        width: 100%;
        height: auto;
        border-radius: 20px;
        object-fit: cover;
        box-shadow: 0 20px 50px rgba(167,200,242,.3);
    }
    .tn-hero-placeholder {
        width: 100%;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 80px;
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 50px rgba(167,200,242,.2);
    }
    @media (max-width: 991px) {
        .tn-hero { flex-direction: column; padding: 40px 28px; }
        .tn-hero-img { flex: none; max-width: 300px; }
    }
</style>

<div class="container" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="tn-hero">
        <div class="tn-hero-content">
            @if(!empty($data['badge_text']))
                <div class="tn-hero-badge">
                    🍼 {{ $data['badge_text'] }}
                </div>
            @endif

            @if(!empty($data['title']))
                <h1 class="tn-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
            @endif

            @if(!empty($data['subtitle']))
                <p class="tn-hero-sub">{{ $data['subtitle'] }}</p>
            @endif

            <div class="d-flex gap-3 flex-wrap">
                @if(!empty($data['button_text']))
                    <a href="{{ $data['button_url'] ?? $shop_url }}" class="tn-btn tn-btn-primary">
                        {{ $data['button_text'] }}
                        <i class="mdi mdi-arrow-right"></i>
                    </a>
                @endif
                @if(!empty($data['button2_text']))
                    <a href="{{ $data['button2_url'] ?? '#' }}" class="tn-btn tn-btn-outline">
                        {{ $data['button2_text'] }}
                    </a>
                @endif
            </div>
        </div>

        <div class="tn-hero-img">
            @if(!empty($hero_img_url))
                <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}">
            @else
                <div class="tn-hero-placeholder">🍼</div>
            @endif
        </div>
    </div>
</div>
