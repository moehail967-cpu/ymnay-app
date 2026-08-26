{{-- Electro: Promo Banners --}}
<section class="el-promo-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="el-promo-row">
            @foreach($cards as $card)
            <div class="el-promo-card">
                {{-- Decorative blob --}}
                <div class="el-promo-blob" aria-hidden="true"></div>

                <div class="el-promo-text">
                    <span class="el-promo-eyebrow">{{ $card['eyebrow'] }}</span>
                    <h3 class="el-promo-title">
                        <span class="el-promo-accent">{{ $card['title_accent'] }}</span>
                        <span class="el-promo-dark">{{ $card['title_dark'] }}</span>
                    </h3>
                    <a href="{{ $card['link_url'] }}" class="el-promo-btn">
                        <span class="el-promo-btn-circle"><i class="las la-arrow-right"></i></span>
                        <span>{{ $card['link_text'] }}</span>
                    </a>
                </div>

                @if($card['image'])
                <div class="el-promo-img-wrap">
                    <img src="{{ $card['image'] }}" alt="{{ $card['title_accent'] }} {{ $card['title_dark'] }}" class="el-promo-img" loading="lazy">
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
