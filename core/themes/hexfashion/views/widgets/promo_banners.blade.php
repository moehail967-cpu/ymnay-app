{{-- Electro: Promo Banners --}}
<section class="hf-promo-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-promo-row">
            @foreach($cards as $card)
            <div class="hf-promo-card">
                {{-- Decorative blob --}}
                <div class="hf-promo-blob" aria-hidden="true"></div>

                <div class="hf-promo-text">
                    <span class="hf-promo-eyebrow">{{ $card['eyebrow'] }}</span>
                    <h3 class="hf-promo-title">
                        <span class="hf-promo-accent">{{ $card['title_accent'] }}</span>
                        <span class="hf-promo-dark">{{ $card['title_dark'] }}</span>
                    </h3>
                    <a href="{{ $card['link_url'] }}" class="hf-promo-btn">
                        <span class="hf-promo-btn-circle"><i class="las la-arrow-right"></i></span>
                        <span>{{ $card['link_text'] }}</span>
                    </a>
                </div>

                @if($card['image'])
                <div class="hf-promo-img-wrap">
                    <img src="{{ $card['image'] }}" alt="{{ $card['title_accent'] }} {{ $card['title_dark'] }}" class="hf-promo-img" loading="lazy">
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
