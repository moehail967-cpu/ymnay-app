<section class="bk-hero-section" style="padding-top:{{ $padding_top }}px; padding-bottom:{{ $padding_bottom }}px;">
    <div class="bk-hero-inner">

        {{-- Left: Content --}}
        <div class="bk-hero-content">
            @if(!empty($eyebrow))
                <div class="bk-hero-eyebrow">
                    <span class="bk-hero-eyebrow-icon">🌿</span> {{ $eyebrow }}
                </div>
            @endif

            <h1 class="bk-hero-title">{!! $title !!}</h1>

            @if(!empty($subtitle))
                <p class="bk-hero-subtitle">{{ $subtitle }}</p>
            @endif

            <div class="bk-hero-buttons">
                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="bk-btn bk-btn-rose">
                        <i class="mdi mdi-shopping-outline"></i> {{ $button_text }}
                    </a>
                @endif
                @if(!empty($button2_text))
                    <a href="{{ $button2_url }}" class="bk-btn bk-btn-outline">
                        <i class="mdi mdi-calendar-check-outline"></i> {{ $button2_text }}
                    </a>
                @endif
            </div>

            <div class="bk-hero-trust">
                <span class="bk-hero-trust-item"><i class="mdi mdi-leaf"></i> Natural Ingredients</span>
                <span class="bk-hero-trust-item"><i class="mdi mdi-clock-fast"></i> Same Day Delivery</span>
                <span class="bk-hero-trust-item"><i class="mdi mdi-star"></i> 4.9 Rating</span>
            </div>
        </div>

        {{-- Right: Image --}}
        <div class="bk-hero-image">
            @if(!empty($hero_image))
                <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}" class="bk-hero-img">
            @else
                <div class="bk-hero-placeholder">🥐</div>
            @endif
        </div>

    </div>
</section>
