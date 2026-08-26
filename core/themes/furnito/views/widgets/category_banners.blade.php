{{-- Furnito: Category Banners — two cards, left text + right bottom-aligned image --}}
<section class="fn-catb" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px; background-color:#fff ;">
    <div class="container">
        <div class="fn-catb-grid">

            {{-- Banner 1 --}}
            <div class="fn-catb-card" style="background-color:{{ $banner_1['bg_color'] }};height:{{ $card_height }}px;">
                <div class="fn-catb-text">
                    @if($banner_1['title'])
                    <h3 class="fn-catb-title">{{ $banner_1['title'] }}</h3>
                    @endif
                    @if($banner_1['subtitle'])
                    <p class="fn-catb-sub">{{ $banner_1['subtitle'] }}</p>
                    @endif
                    @if($banner_1['btn_text'])
                    <a href="{{ $banner_1['btn_url'] }}" class="fn-catb-link">
                        <span>{{ $banner_1['btn_text'] }}</span>
                    </a>
                    @endif
                </div>
                <div class="fn-catb-visual">
                    @if($banner_1['image'])
                        <img src="{{ $banner_1['image'] }}" alt="{{ $banner_1['title'] }}" class="fn-catb-img" loading="lazy">
                    @else
                        <div class="fn-catb-placeholder"><i class="las la-couch"></i></div>
                    @endif
                </div>
            </div>

            {{-- Banner 2 --}}
            <div class="fn-catb-card" style="background-color:{{ $banner_2['bg_color'] }};height:{{ $card_height }}px;">
                <div class="fn-catb-text">
                    @if($banner_2['title'])
                    <h3 class="fn-catb-title">{{ $banner_2['title'] }}</h3>
                    @endif
                    @if($banner_2['subtitle'])
                    <p class="fn-catb-sub">{{ $banner_2['subtitle'] }}</p>
                    @endif
                    @if($banner_2['btn_text'])
                    <a href="{{ $banner_2['btn_url'] }}" class="fn-catb-link">
                        <span>{{ $banner_2['btn_text'] }}</span>
                    </a>
                    @endif
                </div>
                <div class="fn-catb-visual">
                    @if($banner_2['image'])
                        <img src="{{ $banner_2['image'] }}" alt="{{ $banner_2['title'] }}" class="fn-catb-img" loading="lazy">
                    @else
                        <div class="fn-catb-placeholder"><i class="las la-leaf"></i></div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>

<style>
/* ── Furnito Category Banners ── */
.fn-catb-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.fn-catb-card {
    display: flex;
    align-items: stretch;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
}

/* Left: text column */
.fn-catb-text {
    flex: 0 0 52%;
    max-width: 52%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 36px 28px 36px 36px;
    z-index: 2;
}

.fn-catb-title {
    font-size: clamp(1.3rem, 2.2vw, 1.8rem);
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1.2;
    margin: 0 0 10px;
    letter-spacing: -0.3px;
}

.fn-catb-sub {
    font-size: 13px;
    color: #666;
    line-height: 1.65;
    margin: 0 0 22px;
}

/* Text link with decorative underline dash */
.fn-catb-link {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    text-decoration: none;
    gap: 5px;
    width: fit-content;
}

.fn-catb-link span {
    font-size: 13px;
    font-weight: 700;
    color: #1a1a1a;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    line-height: 1;
}

.fn-catb-link::after {
    content: '';
    display: block;
    width: 32px;
    height: 2px;
    background: #1a1a1a;
    transition: width .25s ease;
}

.fn-catb-link:hover span {
    color: #3D8870;
}

.fn-catb-link:hover::after {
    width: 56px;
    background: #3D8870;
}

/* Right: image column — bottom-aligned */
.fn-catb-visual {
    flex: 1;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    overflow: hidden;
    padding: 0 16px;
}

.fn-catb-img {
    display: block;
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    object-position: bottom center;
}

.fn-catb-placeholder {
    display: flex;
    align-items: flex-end;
    justify-content: center;
    width: 100%;
    height: 80%;
    font-size: 5rem;
    color: #3D8870;
    opacity: 0.2;
}

/* Responsive */
@media (max-width: 768px) {
    .fn-catb-grid { grid-template-columns: 1fr; }

    .fn-catb-card {
        height: 240px !important;
    }

    .fn-catb-text {
        flex: 0 0 56%;
        max-width: 56%;
        padding: 24px 16px 24px 24px;
    }

    .fn-catb-title { font-size: 1.25rem; }
}

@media (max-width: 480px) {
    .fn-catb-card {
        height: auto !important;
        min-height: 200px;
        flex-direction: column;
    }

    .fn-catb-text {
        flex: none;
        max-width: 100%;
        padding: 24px 20px 12px;
    }

    .fn-catb-visual {
        width: 100%;
        height: 160px;
        padding: 0 20px;
    }
}
</style>
