{{-- Medicom: Promo Banners (New Style) --}}
<section class="mc-promo-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="mc-promo-row">
            @foreach($cards as $index => $card)
            @php
                // Alternate class for styling the background and layout
                $cardClass = ($index % 2 === 0) ? 'mc-promo-card-left' : 'mc-promo-card-right';
            @endphp
            <div class="mc-promo-card {{ $cardClass }}">
                <div class="mc-promo-text">
                    <h3 class="mc-promo-title">
                        {{ $card['title_accent'] }} <br>
                        {{ $card['title_dark'] }}
                    </h3>
                    <a href="{{ $card['link_url'] }}" class="mc-promo-btn">
                        {{ $card['link_text'] }}
                    </a>
                </div>

                @if($card['image'])
                <div class="mc-promo-img-wrap">
                    <img src="{{ $card['image'] }}" alt="{{ $card['title_accent'] }} {{ $card['title_dark'] }}" class="mc-promo-img" loading="lazy">
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
