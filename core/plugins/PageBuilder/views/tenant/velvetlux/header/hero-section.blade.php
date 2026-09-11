@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<style>
    :root {
        --vl-plum: #5C1F6B;
        --vl-plum-deep: #3D1248;
        --vl-plum-light: #F0E8F4;
        --vl-champagne: #C4A45E;
        --vl-champagne-lt: #F5EDD0;
        --vl-bg: #F8F5F2;
        --vl-dark: #1A0820;
        --vl-muted: #7A6882;
        --vl-border: #E0D4E8;
        --vl-font-head: 'Cormorant Garamond', Georgia, serif;
    }
    .vl-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        background: linear-gradient(135deg, var(--vl-plum-light) 0%, var(--vl-champagne-lt) 100%);
        border-radius: 20px;
        padding: 56px 60px;
        position: relative;
        overflow: hidden;
    }
    .vl-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%235C1F6B' fill-opacity='0.04'%3E%3Cpath d='M40 0L42 38H78L48 60L58 96L40 72L22 96L32 60L2 38H38z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") center/80px;
        pointer-events: none;
    }
    .vl-hero-content {
        flex: 1;
        position: relative;
        z-index: 1;
        max-width: 540px;
    }
    .vl-hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--vl-plum);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        padding: 7px 18px;
        border-radius: 50px;
        margin-bottom: 22px;
    }
    .vl-hero-title {
        font-family: var(--vl-font-head);
        font-size: clamp(38px, 5vw, 68px);
        font-weight: 700;
        line-height: 1.1;
        color: var(--vl-dark);
        margin-bottom: 18px;
    }
    .vl-hero-sub {
        font-size: 16px;
        color: var(--vl-muted);
        line-height: 1.7;
        margin-bottom: 34px;
        max-width: 440px;
    }
    .vl-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 32px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-decoration: none;
        transition: all .25s ease;
        border: 2px solid transparent;
        cursor: pointer;
    }
    .vl-btn-primary {
        background: var(--vl-plum);
        color: #fff;
        border-color: var(--vl-plum);
    }
    .vl-btn-primary:hover {
        background: var(--vl-plum-deep);
        border-color: var(--vl-plum-deep);
        color: #fff;
    }
    .vl-btn-outline {
        background: transparent;
        color: var(--vl-plum);
        border-color: var(--vl-plum);
    }
    .vl-btn-outline:hover {
        background: var(--vl-plum);
        color: #fff;
    }
    .vl-hero-img {
        flex: 0 0 auto;
        width: 380px;
        max-width: 42%;
        position: relative;
        z-index: 1;
    }
    .vl-hero-img img {
        width: 100%;
        height: auto;
        display: block;
        filter: drop-shadow(0 20px 40px rgba(92,31,107,.2));
    }
    @media (max-width: 768px) {
        .vl-hero { padding: 40px 28px; flex-direction: column; text-align: center; }
        .vl-hero-img { width: 260px; max-width: 80%; }
        .vl-hero-sub { margin-left: auto; margin-right: auto; }
    }
</style>

<div class="container" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="vl-hero">
        <div class="vl-hero-content">
            @if(!empty($data['badge_text']))
                <div class="vl-hero-tag">
                    <i class="mdi mdi-crown"></i>
                    {{ $data['badge_text'] }}
                </div>
            @endif

            @if(!empty($data['title']))
                <h1 class="vl-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
            @endif

            @if(!empty($data['subtitle']))
                <p class="vl-hero-sub">{{ $data['subtitle'] }}</p>
            @endif

            <div class="d-flex gap-3 flex-wrap">
                @if(!empty($data['button_text']))
                    <a href="{{ $data['button_url'] ?? $shop_url }}" class="vl-btn vl-btn-primary">
                        <i class="mdi mdi-hanger"></i>
                        {{ $data['button_text'] }}
                    </a>
                @endif
                @if(!empty($data['button2_text']))
                    <a href="{{ $data['button2_url'] ?? '#' }}" class="vl-btn vl-btn-outline">
                        {{ $data['button2_text'] }}
                        <i class="mdi mdi-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>

        @if(!empty($hero_img_url))
            <div class="vl-hero-img">
                <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}">
            </div>
        @else
            <div class="vl-hero-img" style="display:flex;align-items:center;justify-content:center;font-size:120px;opacity:.35;">
                👑
            </div>
        @endif
    </div>
</div>
