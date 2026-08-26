{{-- Casual: Hero Section --}}
<section class="cs-hero" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px; position:relative;">
    {{-- Decorative square at top of section --}}
    <div class="cs-hero-deco cs-hero-deco-square" aria-hidden="true"></div>
    <div class="cs-hero-inner container px-0">

        {{-- Left panel --}}
        <div class="cs-hero-left">
            <span class="cs-hero-eyebrow">{{ $eyebrow }}</span>
            <h1 class="cs-hero-title">{!! nl2br(e($title)) !!}</h1>

            {{-- Circle arrow + text CTA --}}
            <a href="{{ $button_url }}" class="cs-hero-cta">
                <span class="cs-hero-cta-circle"><i class="las la-arrow-right"></i></span>
                <span class="cs-hero-cta-text">{{ $button_text }}</span>
            </a>

            {{-- Mini product cards --}}
            @if($products->isNotEmpty())
            <div class="cs-hero-mini-products">
                @foreach($products as $product)
                @php
                    $pData   = theme_product_price($product);
                    $pImg    = theme_product_image($product->image_id ?? null, 'grid');
                    $pUrl    = theme_product_url($product->slug ?? $product->id);
                    $price   = $pData['sale_price'];
                @endphp
                <div class="cs-hero-mini-card">
                    <div class="cs-hero-mini-img-wrap">
                        @if($pImg)
                        <img src="{{ $pImg }}" alt="{{ $product->name }}" class="cs-hero-mini-img" loading="lazy">
                        @else
                        <div class="cs-hero-mini-ph"><i class="las la-tshirt"></i></div>
                        @endif
                    </div>
                    <div class="cs-hero-mini-info">
                        <div class="cs-hero-mini-text">
                            <span class="cs-hero-mini-name">{{ Str::limit($product->name, 14) }}</span>
                            <span class="cs-hero-mini-price">{{ amount_with_currency_symbol($price) }}</span>
                        </div>
                        <a href="{{ $pUrl }}" class="cs-hero-mini-eye" title="{{ __('Quick View') }}"><i class="las la-eye"></i></a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right panel: model image with decorative elements --}}
        <div class="cs-hero-right">
            {{-- Large background circle --}}
            <div class="cs-hero-bg-circle" aria-hidden="true"></div>

            {{-- Model image --}}
            @if($hero_img)
            <img src="{{ $hero_img }}" alt="{{ $title }}" class="cs-hero-model-img" loading="eager">
            @else
            <div class="cs-hero-model-placeholder"><i class="las la-user-circle"></i></div>
            @endif

            {{-- Geometric decorations --}}
            <div class="cs-hero-deco cs-hero-deco-triangle" aria-hidden="true"></div>
            <div class="cs-hero-deco cs-hero-deco-dot" aria-hidden="true"></div>

            {{-- Vertical social links --}}
            <div class="cs-hero-social">
                @if(!empty($all_social_icons))
                    @foreach(array_slice((array)$all_social_icons, 0, 3) as $social)
                        @if(!empty($social->url) && !empty($social->icon))
                        <a href="{{ $social->url }}" class="cs-hero-social-link" target="_blank" rel="noopener">
                            {{ ucfirst(preg_replace('/^(lab|las)\s+la-|-f$|-square$|-o$/', '', $social->icon)) }}
                        </a>
                        @endif
                    @endforeach
                @else
                <a href="#" class="cs-hero-social-link">Facebook</a>
                <a href="#" class="cs-hero-social-link">Instagram</a>
                @endif
            </div>
        </div>
    </div>
</section>
