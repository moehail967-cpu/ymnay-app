{{-- Electro: Hero Section --}}
<section class="el-hero" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    {{-- Decorative blob SVG background --}}
    <div class="el-hero-bg-blobs" aria-hidden="true">
        <svg viewBox="0 0 800 600" xmlns="http://www.w3.org/2000/svg" class="el-hero-blob el-hero-blob-1">
            <ellipse cx="400" cy="300" rx="340" ry="260" fill="rgba(232,96,60,0.08)"/>
        </svg>
        <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg" class="el-hero-blob el-hero-blob-2">
            <ellipse cx="250" cy="200" rx="200" ry="160" fill="rgba(232,96,60,0.05)"/>
        </svg>
    </div>

    <div class="container el-hero-inner">
        {{-- Left: hero image or floating product circles --}}
        <div class="el-hero-left">
            @if(!empty($hero_img))
            <div class="el-hero-image-wrap">
                <img src="{{ $hero_img }}" alt="{{ $title }}" class="el-hero-image" loading="eager">
            </div>
            @else
            @php
                $circleColors = ['#E8603C','#f87171','#fbbf24','#86efac','#c4b5fd'];
            @endphp
            <div class="el-hero-orbit">
                @foreach($products as $pi => $product)
                @php
                    $pImg = theme_product_image($product->image_id ?? null, 'grid');
                    $bg = $circleColors[$pi % count($circleColors)];
                    $sizes = ['large','small','medium','small','medium'];
                    $sz = $sizes[$pi % count($sizes)];
                @endphp
                <div class="el-hero-circle el-hero-circle-{{ $sz }} el-hero-pos-{{ $pi + 1 }}" style="background:{{ $bg }};">
                    @if($pImg)
                    <img src="{{ $pImg }}" alt="{{ $product->name }}" class="el-hero-circle-img" loading="lazy">
                    @else
                    <i class="las la-laptop el-hero-circle-icon"></i>
                    @endif
                </div>
                @endforeach

                {{-- Dashed orbit rings --}}
                <div class="el-hero-ring el-hero-ring-outer" aria-hidden="true"></div>
                <div class="el-hero-ring el-hero-ring-inner" aria-hidden="true"></div>
            </div>
            @endif
        </div>

        {{-- Right: text content --}}
        <div class="el-hero-right">
            <span class="el-hero-eyebrow">{{ $eyebrow }}</span>
            <h1 class="el-hero-title">{{ $title }}</h1>
            @if($description)
            <p class="el-hero-desc">{{ $description }}</p>
            @endif
            <a href="{{ $button_url }}" class="el-hero-btn">{{ $button_text }}</a>
        </div>
    </div>
</section>
