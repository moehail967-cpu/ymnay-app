<section class="kv-hero-widget">
    <span class="kv-hero-deco-rainbow">🌈</span>
    <div class="container py-5 py-lg-0" style="min-height:460px;display:flex;align-items:center;">
        <div class="row align-items-center g-4 w-100">
            <div class="col-lg-6">
                @if(!empty($badge_text))
                    <div class="kv-hero-tag mb-3">{{ $badge_text }}</div>
                @endif
                <h1 class="kv-hero-title">{!! $title !!}</h1>
                @if(!empty($subtitle))
                    <p class="kv-hero-sub">{{ $subtitle }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap mt-4">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="kv-btn kv-btn-red">
                            <i class="las la-shopping-cart"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="kv-btn kv-btn-yellow">
                            <i class="las la-gift"></i> {{ $button2_text }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="kv-hero-img-wrap">
                    @if(!empty($hero_image))
                        <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}" class="kv-hero-right-img">
                    @else
                        <div class="kv-hero-img-box">
                            <span style="font-size:48px;">🧸</span>
                            <p class="mb-0 fw-bold" style="font-size:22px;color:#555;">Kids Toys</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
