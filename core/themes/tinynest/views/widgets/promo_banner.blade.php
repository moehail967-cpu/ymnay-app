{{-- TinyNest: Promo Banner Widget --}}
<section class="tn-promo-banner-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="tn-promo-banner-wrap">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between gap-4 flex-wrap">
                <div class="tn-promo-banner-content">
                    <h2 class="tn-promo-banner-title">{{ $title }}</h2>
                    @if($subtitle)
                        <p class="tn-promo-banner-sub">{{ $subtitle }}</p>
                    @endif
                    @if($button_text)
                        <a href="{{ $button_url }}" class="tn-btn tn-btn-mint">
                            <i class="mdi mdi-gift-outline"></i> {{ $button_text }}
                        </a>
                    @endif
                </div>
                <div class="tn-promo-banner-emoji" aria-hidden="true">
                    @if(!empty($promo_image))
                        <img src="{{ $promo_image }}" alt="Promo" class="img-fluid rounded" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                    @else
                        <span>👶</span><span>🎁</span><span>🤍</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
