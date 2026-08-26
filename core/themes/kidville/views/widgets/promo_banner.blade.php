<section class="kv-promo-widget">
    <div class="container">
        <div class="kv-promo-inner">
            <div class="kv-promo-left">
                <h2 class="kv-promo-title">{!! $title !!}</h2>
                @if(!empty($text))
                    <p class="kv-promo-text">{{ $text }}</p>
                @endif
                <div class="kv-promo-actions">
                    @if(!empty($button_text))
                        <a href="{{ $button_url }}" class="kv-btn kv-btn-yellow">
                            <i class="las la-gift"></i> {{ $button_text }}
                        </a>
                    @endif
                    @if(!empty($button2_text))
                        <a href="{{ $button2_url }}" class="kv-btn kv-btn-ghost">{{ $button2_text }}</a>
                    @endif
                </div>
            </div>
            <div class="kv-promo-emojis d-none d-lg-flex">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    @if(!empty($icon1))<span class="kv-promo-emoji">{{ $icon1 }}</span>@endif
                @if(!empty($icon2))<span class="kv-promo-emoji">{{ $icon2 }}</span>@endif
                @if(!empty($icon3))<span class="kv-promo-emoji">{{ $icon3 }}</span>@endif
                @endif
            </div>
        </div>
    </div>
</section>
