<section class="gl-widget-hero">
<div class="container py-4">
    <div class="gl-hero">
        <div class="gl-hero-content">
            @if(!empty($badge_text))
                <div class="gl-hero-pill">
                    <i class="las la-flask"></i> {{ $badge_text }}
                </div>
            @endif
            <h1 class="gl-hero-title">{!! $title !!}</h1>
            @if(!empty($subtitle))
                <p class="gl-hero-sub">{{ $subtitle }}</p>
            @endif
            <div class="d-flex gap-3 flex-wrap mb-4">
                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="gl-btn gl-btn-primary">
                        <i class="las la-shopping-bag"></i> {{ $button_text }}
                    </a>
                @endif
                @if(!empty($button2_text))
                    <a href="{{ $button2_url }}" class="gl-btn gl-btn-outline">
                        {{ $button2_text }}
                    </a>
                @endif
            </div>
            @if(!empty($trust1) || !empty($trust2) || !empty($trust3))
            <div class="gl-hero-trust">
                @if(!empty($trust1))<span class="gl-trust-pill">{{ $trust1 }}</span>@endif
                @if(!empty($trust2))<span class="gl-trust-pill">{{ $trust2 }}</span>@endif
                @if(!empty($trust3))<span class="gl-trust-pill">{{ $trust3 }}</span>@endif
            </div>
            @endif
        </div>
        <div class="gl-hero-img-wrap">
            <div class="gl-hero-deco"></div>
            <div class="gl-hero-img-circle">
                @if(!empty($hero_image))
                    <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}">
                @else
                    <div class="gl-hero-placeholder">✨</div>
                @endif
            </div>
        </div>
    </div>
</div>
</section>
