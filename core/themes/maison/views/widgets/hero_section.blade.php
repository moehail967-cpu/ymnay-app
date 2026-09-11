<section class="ms-widget-hero">
    <div class="container">
        <div class="ms-widget-hero-inner">
            <div class="ms-widget-hero-content">
                @if(!empty($badge_text))
                    <p class="ms-hero-tag">{{ $badge_text }}</p>
                @endif
                <h1 class="ms-widget-hero-title">{!! $title !!}</h1>
                @if(!empty($subtitle))
                    <p class="ms-widget-hero-sub">{{ $subtitle }}</p>
                @endif
                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="ms-btn ms-btn-olive">
                            <i class="las la-shopping-bag"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="ms-btn ms-btn-border-dark">
                            <i class="las la-compass"></i> {{ $button2_text }}
                        </a>
                    @endif
                </div>
            </div>
            <div class="ms-widget-hero-img">
                @if(!empty($hero_image))
                    <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}">
                @else
                    <div class="ms-widget-hero-placeholder"><i class="las la-couch"></i></div>
                @endif
            </div>
        </div>
    </div>
</section>
