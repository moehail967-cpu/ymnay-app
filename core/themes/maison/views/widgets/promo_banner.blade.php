<section class="ms-widget-promo">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h2 class="ms-promo-title">{!! nl2br(e($title)) !!}</h2>
                <p class="ms-promo-text">{{ $text }}</p>
                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="ms-btn ms-btn-white">
                        <i class="las la-compass"></i> {{ $button_text }}
                    </a>
                @endif
            </div>
            <div class="col-lg-5 text-center d-none d-lg-flex align-items-center justify-content-center">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="{{ strip_tags($title) }}" class="ms-promo-img" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="ms-promo-visual">{{ $promo_icon ?? '🛋️' }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
