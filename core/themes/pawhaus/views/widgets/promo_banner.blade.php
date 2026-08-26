{{-- PawHaus: Promo Banner --}}
<section class="ph-promo-widget">
    <div class="container">
        <div class="ph-promo-inner">
            <div class="ph-promo-content">
                <h2 class="ph-promo-title">{!! nl2br(e($title)) !!}</h2>
                <p class="ph-promo-text">{{ $text }}</p>
                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="ph-btn ph-promo-btn">
                        <i class="las la-sync-alt"></i> {{ $button_text }}
                    </a>
                @endif
            </div>
            <div class="ph-promo-visual-wrap">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="{{ strip_tags($title) }}" class="ph-promo-img" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="ph-promo-emoji">{{ $promo_emoji ?? '🐕 🐈 🐾' }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
