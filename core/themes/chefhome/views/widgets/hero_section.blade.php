<section class="ch-hero-widget" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container my-4">
        <div class="ch-hero">

            @if(!empty($dish_image))
                <img src="{{ $dish_image }}" alt="{{ strip_tags($title) }}" class="ch-hero-fill">
            @endif

            <div class="ch-hero-content">
                @if(!empty($badge_text))
                    <div class="ch-hero-tag">
                        <i class="las la-fire" style="color:var(--ch-amber);"></i>
                        {{ $badge_text }}
                    </div>
                @endif

                <h1 class="ch-hero-title">
                    {!! $title !!}
                    @if(!empty($title_accent))
                        <span class="accent">{{ $title_accent }}</span>
                    @endif
                </h1>

                @if(!empty($subtitle))
                    <p class="ch-hero-sub">{{ $subtitle }}</p>
                @endif

                <div class="d-flex gap-3 flex-wrap">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="ch-btn ch-btn-primary">
                            <i class="las la-shopping-cart"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="ch-btn ch-btn-outline">{{ $button2_text }}</a>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>
