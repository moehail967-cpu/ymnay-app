<section class="bp-promo-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="bp-promo-card bp-promo-pink">
                    <div class="bp-promo-text">
                        @if(!empty($tag1))
                            <div class="bp-promo-tag">{{ $tag1 }}</div>
                        @endif
                        <h3 class="bp-promo-title">{{ $title1 }}</h3>
                        <a href="{{ $link1_url }}" class="bp-promo-link bp-promo-link-pink">
                            {{ $link1_text }} <i class="las la-angle-right"></i>
                        </a>
                    </div>
                    @if(!empty($image1_url))
                    <div class="bp-promo-img">
                        <img src="{{ $image1_url }}" alt="{{ $title1 }}" loading="lazy">
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="bp-promo-card bp-promo-blue">
                    <div class="bp-promo-text">
                        @if(!empty($tag2))
                            <div class="bp-promo-tag">{{ $tag2 }}</div>
                        @endif
                        <h3 class="bp-promo-title">{{ $title2 }}</h3>
                        <a href="{{ $link2_url }}" class="bp-promo-link bp-promo-link-blue">
                            {{ $link2_text }} <i class="las la-angle-right"></i>
                        </a>
                    </div>
                    @if(!empty($image2_url))
                    <div class="bp-promo-img">
                        <img src="{{ $image2_url }}" alt="{{ $title2 }}" loading="lazy">
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
