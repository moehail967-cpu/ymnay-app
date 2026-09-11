@php
    $hero_image   = get_attachment_image_by_id($data['hero_image'] ?? null);
    $hero_img_url = !empty($hero_image) ? $hero_image['img_url'] : '';
    $pt           = !empty($data['padding_top']) ? (int)$data['padding_top'] : 32;
    $pb           = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 32;
    $shop_url     = route('tenant.shop');
@endphp

<style>
    :root {
        --pf-teal: #00897B;
        --pf-teal-deep: #006358;
        --pf-teal-light: #E0F2F1;
        --pf-blue: #0277BD;
        --pf-blue-light: #E1F5FE;
        --pf-bg: #F7FAFB;
        --pf-dark: #1A2332;
        --pf-muted: #607080;
        --pf-border: #DDE6EA;
    }

    .pf-hero {
        display: flex;
        align-items: center;
        gap: 48px;
        background: linear-gradient(135deg, var(--pf-teal-light) 0%, var(--pf-blue-light) 100%);
        border: 1px solid var(--pf-border);
        padding: 60px 56px;
    }
    .pf-hero-content { flex: 1; }
    .pf-hero-visual {
        flex-shrink: 0;
        width: 340px;
        text-align: center;
        font-size: 120px;
        line-height: 1;
    }
    .pf-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        color: var(--pf-teal);
        border: 1px solid var(--pf-teal);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: 6px 16px;
        margin-bottom: 20px;
    }
    .pf-hero-title {
        font-size: clamp(28px, 4vw, 48px);
        font-weight: 800;
        color: var(--pf-dark);
        line-height: 1.15;
        margin-bottom: 16px;
    }
    .pf-hero-sub {
        font-size: 16px;
        color: var(--pf-muted);
        line-height: 1.7;
        max-width: 520px;
        margin-bottom: 32px;
    }
    .pf-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        padding: 13px 28px;
        text-decoration: none;
        border: 2px solid transparent;
        cursor: pointer;
        transition: all .2s;
    }
    .pf-btn-primary {
        background: var(--pf-teal);
        color: #fff;
        border-color: var(--pf-teal);
    }
    .pf-btn-primary:hover {
        background: var(--pf-teal-deep);
        border-color: var(--pf-teal-deep);
        color: #fff;
    }
    .pf-btn-outline {
        background: transparent;
        color: var(--pf-teal);
        border-color: var(--pf-teal);
    }
    .pf-btn-outline:hover {
        background: var(--pf-teal);
        color: #fff;
    }
    @media (max-width: 768px) {
        .pf-hero { flex-direction: column; padding: 40px 24px; }
        .pf-hero-visual { width: 100%; font-size: 80px; }
    }
</style>

<div style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--pf-bg, #F7FAFB);">
    <div class="container">
        <div class="pf-hero">
            <div class="pf-hero-content">
                @if(!empty($data['badge_text']))
                    <div class="pf-hero-badge">
                        <i class="mdi mdi-shield-check"></i>
                        {{ $data['badge_text'] }}
                    </div>
                @endif

                @if(!empty($data['title']))
                    <h1 class="pf-hero-title">{!! nl2br(e($data['title'])) !!}</h1>
                @endif

                @if(!empty($data['subtitle']))
                    <p class="pf-hero-sub">{{ $data['subtitle'] }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($data['button_text']))
                        <a href="{{ $data['button_url'] ?? $shop_url }}" class="pf-btn pf-btn-primary">
                            <i class="mdi mdi-cart-outline"></i>
                            {{ $data['button_text'] }}
                        </a>
                    @endif
                    @if(!empty($data['button2_text']))
                        <a href="{{ $data['button2_url'] ?? '#' }}" class="pf-btn pf-btn-outline">
                            {{ $data['button2_text'] }}
                            <i class="mdi mdi-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="pf-hero-visual">
                @if(!empty($hero_img_url))
                    <img src="{{ $hero_img_url }}" alt="{{ $data['title'] ?? '' }}"
                         style="max-width:100%; max-height:340px; object-fit:contain;">
                @else
                    <span>💊</span>
                @endif
            </div>
        </div>
    </div>
</div>
