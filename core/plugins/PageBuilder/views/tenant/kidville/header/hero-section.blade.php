@php
    $hero_image = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url = route('tenant.shop');
@endphp

<style>
    :root {
        --kv-red: #E53935;
        --kv-red-light: #FFEBEE;
        --kv-blue: #1565C0;
        --kv-blue-light: #E3F2FD;
        --kv-yellow: #FDD835;
        --kv-yellow-lt: #FFFDE7;
        --kv-green: #43A047;
        --kv-green-light: #E8F5E9;
        --kv-bg: #FFFEF8;
        --kv-dark: #1A1A2E;
        --kv-muted: #5A5A6E;
        --kv-border: #E0E0E0;
    }
    .kv-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        background: linear-gradient(135deg, var(--kv-blue-light) 0%, var(--kv-yellow-lt) 100%);
        border-radius: 24px;
        padding: 56px 60px;
        position: relative;
        overflow: hidden;
    }
    .kv-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23E53935' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='10'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") center/60px;
        pointer-events: none;
    }
    .kv-hero-content {
        flex: 1;
        position: relative;
        z-index: 1;
        max-width: 540px;
    }
    .kv-hero-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--kv-red);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.5px;
        padding: 8px 20px;
        border-radius: 50px;
        margin-bottom: 22px;
    }
    .kv-hero-title {
        font-size: clamp(34px, 5vw, 60px);
        font-weight: 900;
        line-height: 1.15;
        color: var(--kv-dark);
        margin-bottom: 18px;
    }
    .kv-hero-sub {
        font-size: 16px;
        color: var(--kv-muted);
        line-height: 1.7;
        margin-bottom: 34px;
        max-width: 440px;
    }
    .kv-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 14px 32px;
        border-radius: 50px;
        font-size: 15px;
        font-weight: 800;
        text-decoration: none;
        transition: all .25s ease;
        border: 3px solid transparent;
        cursor: pointer;
    }
    .kv-btn-primary {
        background: var(--kv-red);
        color: #fff;
        border-color: var(--kv-red);
    }
    .kv-btn-primary:hover {
        background: #C62828;
        border-color: #C62828;
        color: #fff;
        transform: scale(1.04);
    }
    .kv-btn-outline {
        background: var(--kv-yellow);
        color: var(--kv-dark);
        border-color: var(--kv-yellow);
    }
    .kv-btn-outline:hover {
        background: #F9A825;
        border-color: #F9A825;
        color: var(--kv-dark);
        transform: scale(1.04);
    }
    .kv-hero-img {
        flex: 0 0 auto;
        width: 380px;
        max-width: 42%;
        position: relative;
        z-index: 1;
    }
    .kv-hero-img img {
        width: 100%;
        height: auto;
        display: block;
        filter: drop-shadow(0 20px 40px rgba(21,101,192,.2));
        animation: kv-float 4s ease-in-out infinite;
    }
    @keyframes kv-float {
        0%, 100% { transform: translateY(0); }
        50%       { transform: translateY(-10px); }
    }
    @media (max-width: 768px) {
        .kv-hero { padding: 40px 28px; flex-direction: column; text-align: center; }
        .kv-hero-img { width: 240px; max-width: 80%; }
        .kv-hero-sub { margin-left: auto; margin-right: auto; }
    }
</style>

<div class="container" style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="kv-hero">
        <div class="kv-hero-content">
            @if(!empty($data['badge_text']))
                <div class="kv-hero-tag">
                    🧸 {{ $data['badge_text'] }}
                </div>
            @endif

            @if(!empty($data['title']))
                <h1 class="kv-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
            @endif

            @if(!empty($data['subtitle']))
                <p class="kv-hero-sub">{{ $data['subtitle'] }}</p>
            @endif

            <div class="d-flex gap-3 flex-wrap">
                @if(!empty($data['button_text']))
                    <a href="{{ $data['button_url'] ?? $shop_url }}" class="kv-btn kv-btn-primary">
                        {{ $data['button_text'] }}
                        <i class="mdi mdi-arrow-right"></i>
                    </a>
                @endif
                @if(!empty($data['button2_text']))
                    <a href="{{ $data['button2_url'] ?? '#' }}" class="kv-btn kv-btn-outline">
                        {{ $data['button2_text'] }}
                        <i class="mdi mdi-star"></i>
                    </a>
                @endif
            </div>
        </div>

        @if(!empty($hero_img_url))
            <div class="kv-hero-img">
                <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}">
            </div>
        @else
            <div class="kv-hero-img" style="display:flex;align-items:center;justify-content:center;font-size:130px;opacity:.4;">
                🧸
            </div>
        @endif
    </div>
</div>
