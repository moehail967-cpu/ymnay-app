<section class="fm-promo-banner-widget">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2 class="fm-promo-banner-title">{!! $title !!}</h2>
                @if(!empty($text))
                    <p class="fm-promo-banner-text">{{ $text }}</p>
                @endif
                <div class="fm-promo-banner-actions">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="fm-promo-banner-btn-primary">
                            <i class="las la-sync-alt"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="fm-promo-banner-btn-outline">{{ $button2_text }}</a>
                    @endif
                </div>
            </div>
            <div class="col-lg-5 text-center">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="fm-promo-banner-icons">
                    @if(!empty($icon1))
                        <span class="fm-promo-emoji">{{ $icon1 }}</span>
                    @endif
                    @if(!empty($icon2))
                        <span class="fm-promo-emoji fm-promo-emoji-mid">{{ $icon2 }}</span>
                    @endif
                    @if(!empty($icon3))
                        <span class="fm-promo-emoji">{{ $icon3 }}</span>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
