{{-- Electro: New Release Banner --}}
<section class="hf-release-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-release-card">
            {{-- Decorative blobs --}}
            <div class="hf-release-blob hf-release-blob-1" aria-hidden="true"></div>
            <div class="hf-release-blob hf-release-blob-2" aria-hidden="true"></div>

            {{-- Left: product image + badge --}}
            <div class="hf-release-img-side">
                @if($img_url)
                <img src="{{ $img_url }}" alt="{{ $title_accent }} {{ $title_dark }}" class="hf-release-img" loading="lazy">
                @else
                <div class="hf-release-img-ph"><i class="las la-laptop"></i></div>
                @endif
                @if($badge_text)
                <div class="hf-release-badge">{{ $badge_text }}</div>
                @endif
            </div>

            {{-- Right: text content --}}
            <div class="hf-release-text-side">
                <h2 class="hf-release-title">
                    <span class="hf-release-accent">{{ $title_accent }}</span>
                    <span class="hf-release-dark">{{ $title_dark }}</span>
                </h2>
                @if($price)
                <div class="hf-release-price">{{ $price }}</div>
                @endif
                <a href="{{ $button_url }}" class="hf-release-btn">{{ $button_text }}</a>
            </div>
        </div>
    </div>
</section>
