{{-- PawHaus: Hero Section --}}
<section class="ph-hero-widget">
    <div class="container">
        <div class="ph-hero-inner">
            <div class="ph-hero-content">
                @if(!empty($eyebrow))
                    <div class="ph-hero-badge">
                        <i class="las la-paw"></i> {{ $eyebrow }}
                    </div>
                @endif

                <h1 class="ph-hero-title">{!! $title !!}</h1>

                @if(!empty($subtitle))
                    <p class="ph-hero-sub">{{ $subtitle }}</p>
                @endif

                <div class="ph-hero-btns">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="ph-btn ph-btn-terra">
                            <i class="las la-paw"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="ph-btn ph-btn-sage">
                            <i class="las la-cat"></i> {{ $button2_text }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="ph-hero-image-wrap">
                @if(!empty($hero_image))
                    <div class="ph-hero-circle">
                        <img src="{{ $hero_image }}" alt="{{ strip_tags($title) }}">
                    </div>

                @elseif(!empty($product_image))
                    <div class="ph-hero-circle">
                        <img src="{{ $product_image }}" alt="{{ strip_tags($title) }}">
                    </div>
                @else
                    <div class="ph-hero-circle ph-hero-circle-ph">
                        <i class="las la-dog"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
