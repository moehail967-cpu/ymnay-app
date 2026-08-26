<section class="bk-promo-section" style="background-color: {{ $background_color ?? 'var(--bk-rose)' }}; color: #ffffff; padding-top:{{ $padding_top }}px; padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                @if(!empty($heading))
                    <h2>{!! $heading !!}</h2>
                @endif
                @if(!empty($text))
                    <p>{{ $text }}</p>
                @endif
                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="bk-btn bk-btn-cream">
                        <i class="mdi mdi-cake-variant-outline"></i> {{ $button_text }}
                    </a>
                @endif
            </div>
            <div class="col-lg-6 text-center bk-promo-section__right">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="{{ strip_tags($heading ?? '') }}" class="bk-promo-section__image" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @elseif(!empty($emoji))
                    <div class="bk-promo-section__emoji">{{ $emoji }}</div>
                @endif
            </div>
        </div>
    </div>
</section>
