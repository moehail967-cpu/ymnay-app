@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<style>
    :root {
        --gc-rose: #C8956C;
        --gc-rose-deep: #A5724A;
        --gc-rose-light: #F5EAE0;
        --gc-ivory: #F5EFE6;
        --gc-bg: #FAF7F2;
        --gc-dark: #2C1810;
        --gc-muted: #7A6050;
        --gc-border: #E5D5C5;
        --gc-gold: #C8A870;
        --gc-font-head: 'Cormorant Garamond', Georgia, serif;
    }
    .gc-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        background: linear-gradient(135deg, var(--gc-ivory) 0%, var(--gc-bg) 100%);
        border-radius: 20px;
        padding: 56px 60px;
        border: 1px solid var(--gc-border);
        position: relative;
        overflow: hidden;
    }
    .gc-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23C8A870' fill-opacity='0.05'%3E%3Ccircle cx='50' cy='50' r='30'/%3E%3Ccircle cx='50' cy='50' r='20'/%3E%3Ccircle cx='50' cy='50' r='10'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") center/100px;
        pointer-events: none;
    }
    .gc-hero-content {
        flex: 1;
        position: relative;
        z-index: 1;
        max-width: 540px;
    }
    .gc-hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--gc-rose);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        padding: 7px 20px;
        border-radius: 50px;
        margin-bottom: 22px;
    }
    .gc-hero-title {
        font-family: var(--gc-font-head);
        font-size: clamp(36px, 5vw, 64px);
        font-weight: 700;
        line-height: 1.1;
        color: var(--gc-dark);
        margin-bottom: 18px;
    }
    .gc-hero-sub {
        font-size: 16px;
        color: var(--gc-muted);
        line-height: 1.75;
        margin-bottom: 34px;
        max-width: 440px;
    }
    .gc-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 32px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-decoration: none;
        transition: all .25s ease;
        border: 2px solid transparent;
        cursor: pointer;
    }
    .gc-btn-primary {
        background: var(--gc-rose);
        color: #fff;
        border-color: var(--gc-rose);
    }
    .gc-btn-primary:hover {
        background: var(--gc-rose-deep);
        border-color: var(--gc-rose-deep);
        color: #fff;
    }
    .gc-btn-outline {
        background: transparent;
        color: var(--gc-dark);
        border-color: var(--gc-border);
    }
    .gc-btn-outline:hover {
        border-color: var(--gc-rose);
        color: var(--gc-rose);
    }
    .gc-hero-img {
        flex: 0 0 auto;
        width: 380px;
        max-width: 42%;
        position: relative;
        z-index: 1;
    }
    .gc-hero-img img {
        width: 100%;
        height: auto;
        display: block;
        filter: drop-shadow(0 20px 40px rgba(200,149,108,.25));
    }
    @media (max-width: 768px) {
        .gc-hero { padding: 40px 28px; flex-direction: column; text-align: center; }
        .gc-hero-img { width: 260px; max-width: 80%; }
        .gc-hero-sub { margin-left: auto; margin-right: auto; }
    }
</style>

<div class="container" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="gc-hero">
        <div class="gc-hero-content">
            @if(!empty($data['badge_text']))
                <div class="gc-hero-tag">
                    <i class="mdi mdi-diamond-stone"></i>
                    {{ $data['badge_text'] }}
                </div>
            @endif

            @if(!empty($data['title']))
                <h1 class="gc-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
            @endif

            @if(!empty($data['subtitle']))
                <p class="gc-hero-sub">{{ $data['subtitle'] }}</p>
            @endif

            <div class="d-flex gap-3 flex-wrap">
                @if(!empty($data['button_text']))
                    <a href="{{ $data['button_url'] ?? $shop_url }}" class="gc-btn gc-btn-primary">
                        <i class="mdi mdi-diamond-stone"></i>
                        {{ $data['button_text'] }}
                    </a>
                @endif
                @if(!empty($data['button2_text']))
                    <a href="{{ $data['button2_url'] ?? '#' }}" class="gc-btn gc-btn-outline">
                        {{ $data['button2_text'] }}
                        <i class="mdi mdi-arrow-right"></i>
                    </a>
                @endif
            </div>
        </div>

        @if(!empty($hero_img_url))
            <div class="gc-hero-img">
                <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}">
            </div>
        @else
            <div class="gc-hero-img" style="display:flex;align-items:center;justify-content:center;font-size:120px;opacity:.35;">
                💍
            </div>
        @endif
    </div>
</div>
