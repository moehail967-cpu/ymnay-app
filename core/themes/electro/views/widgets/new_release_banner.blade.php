{{-- Electro: New Release Banner --}}
<section class="el-release-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="el-release-card">
            {{-- Decorative blobs --}}
            <div class="el-release-blob el-release-blob-1" aria-hidden="true"></div>
            <div class="el-release-blob el-release-blob-2" aria-hidden="true"></div>

            {{-- Left: product image + badge --}}
            <div class="el-release-img-side">
                @if($img_url)
                <img src="{{ $img_url }}" alt="{{ $title_accent }} {{ $title_dark }}" class="el-release-img" loading="lazy">
                @else
                <div class="el-release-img-ph"><i class="las la-laptop"></i></div>
                @endif
                @if($badge_text)
                <div class="el-release-badge">{{ $badge_text }}</div>
                @endif
            </div>

            {{-- Right: text content --}}
            <div class="el-release-text-side">
                <h2 class="el-release-title">
                    <span class="el-release-accent">{{ $title_accent }}</span>
                    <span class="el-release-dark">{{ $title_dark }}</span>
                </h2>
                @if($price)
                <div class="el-release-price">{{ $price }}</div>
                @endif
                <a href="{{ $button_url }}" class="el-release-btn">{{ $button_text }}</a>
            </div>
        </div>
    </div>
</section>
