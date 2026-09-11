<section class="bp-single-promo-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="bp-single-promo-card">
            <div class="bp-single-promo-content">
                <h2 class="bp-single-promo-title">
                    <span class="bp-single-promo-discount">{{ $discount_text }}</span> {!! $title !!}
                </h2>
                @if(!empty($description))
                    <p class="bp-single-promo-desc">{!! $description !!}</p>
                @endif
                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="bp-btn-primary">{{ $button_text }}</a>
                @endif
            </div>
            @if(!empty($banner_image_url))
            <div class="bp-single-promo-media">
                <img src="{{ $banner_image_url }}" alt="{{ strip_tags($title) }}" loading="lazy">
            </div>
            @endif
        </div>
    </div>
</section>
