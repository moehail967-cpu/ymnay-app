{{-- TechZone: Promo Banner --}}
<section class="tz-promo">
    <div class="container">
        <div class="row align-items-center">

            {{-- Left: Text --}}
            <div class="col-lg-7 mb-4 mb-lg-0">
                <h2 class="tz-promo-title">{{ $title }}</h2>
                @if(!empty($description))
                    <p class="tz-promo-text">{{ $description }}</p>
                @endif
                @if(!empty($button_text))
                    <a href="{{ $button_url }}" class="tz-promo-btn">
                        @if(!empty($button_icon))
                            <i class="{{ $button_icon }}"></i>
                        @endif
                        {{ $button_text }}
                    </a>
                @endif
            </div>

            {{-- Right: Image or device icons --}}
            <div class="col-lg-5 d-flex align-items-center justify-content-center justify-content-lg-end">
                @if(!empty($promo_image))
                    <img src="{{ $promo_image }}" alt="{{ $title }}" class="tz-promo-img" style="width: 300px; height: 300px; object-fit: cover; margin: 0 auto; display: block;">
                @else
                    <div class="tz-promo-devices">
                        <span>💻</span>
                        <span>📱</span>
                        <span>⌚</span>
                    </div>
                @endif
            </div>

        </div>
    </div>
</section>
