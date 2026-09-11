<section class="gc-widget-hero">
<div class="container">
    <div class="gc-hero">
        <div class="gc-hero-content">
            @if(!empty($badge_text))
                <div class="gc-hero-label">{{ $badge_text }}</div>
            @endif
            <h1 class="gc-hero-title">{!! $title !!}</h1>
            <div class="gc-hero-divider"></div>
            @if(!empty($subtitle))
                <p class="gc-hero-sub">{{ $subtitle }}</p>
            @endif
            <div class="d-flex gap-3 flex-wrap">
                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="gc-btn gc-btn-primary">
                        <i class="las la-gem"></i> {{ $button_text }}
                    </a>
                @endif
                @if(!empty($button2_text))
                    <a href="{{ $button2_url }}" class="gc-btn gc-btn-ghost">
                        {{ $button2_text }}
                    </a>
                @endif
            </div>
        </div>
        <div class="gc-hero-media-wrap">
            @if(!empty($hero_image))
                <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}" class="gc-hero-img">
            @else
                <div class="gc-hero-placeholder">
                    <i class="las la-gem"></i>
                </div>
            @endif
        </div>
    </div>
</div>
</section>
