<section class="ar-promo-widget" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="ar-promo-inner">
            {{-- Left: Title, Price, CTA --}}
            <div class="ar-promo-content">
                <h2 class="ar-promo-title">{!! $title !!}</h2>
                @php $priceVal = is_numeric($price) ? (float)$price : null; @endphp
                @if($priceVal !== null && $priceVal > 0)
                    <span class="ar-promo-price">{{ amount_with_currency_symbol($priceVal) }}</span>
                @endif
                @if(!empty($button_text) && !empty($button_url))
                    <a href="{{ $button_url }}" class="ar-btn ar-btn-dark" style="align-self:flex-start;">{{ $button_text }}</a>
                @endif
            </div>
            {{-- Right: Product Image --}}
            <div class="ar-promo-media">
                @if(!empty($image_url))
                    <img src="{{ $image_url }}" alt="{{ strip_tags($title) }}" class="ar-promo-img">
                @else
                    <div class="ar-promo-placeholder">
                        <i class="las la-flask"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
