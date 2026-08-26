<h3 class="cs-digi-related-title">{{ __('Related Products') }}</h3>

@forelse($related_products as $rp)
@php
    $dynamic   = get_digital_product_dynamic_price($rp);
    $rp_sale   = $rp->promotional_date && $rp->promotional_price ? $rp->promotional_price : $rp->sale_price;
    $rp_img    = get_attachment_image_by_id($rp->image_id);
    $rp_imgUrl = !empty($rp_img) ? $rp_img['img_url'] : '';
@endphp
<a href="{{ theme_product_url($rp->slug) }}" class="cs-digi-related-card">
    <div class="cs-digi-related-thumb">
        @if($rp_imgUrl)
            <img src="{{ $rp_imgUrl }}" alt="{{ $rp->name }}" loading="lazy">
        @else
            <div class="casual-new-thumb-placeholder"><i class="las la-book"></i></div>
        @endif
    </div>
    <div class="cs-digi-related-body">
        @if($dynamic['discount'] > 0)
        <span class="cs-digi-related-badge">{{ $dynamic['discount'] }}% {{ __('off') }}</span>
        @endif
        <div class="cs-digi-related-name">{{ Str::words($rp->name, 8) }}</div>
        @if($rp->additionalFields?->author?->name)
        <div class="cs-digi-related-author">{{ __('by') }} {{ $rp->additionalFields->author->name }}</div>
        @endif
        <div>
            @if($rp->accessibility != 'free')
                <span class="cs-digi-related-price-sale">{{ float_amount_with_currency_symbol($rp_sale ?: $rp->regular_price) }}</span>
                @if($rp_sale && $rp->regular_price && $rp_sale < $rp->regular_price)
                    <span class="cs-digi-related-price-regular">{{ float_amount_with_currency_symbol($rp->regular_price) }}</span>
                @endif
            @else
                <span class="cs-digi-price-free cs-digi-related-price-sale">{{ __('Free') }}</span>
            @endif
        </div>
    </div>
</a>
@empty
<p class="cs-no-data">{{ __('No related products.') }}</p>
@endforelse
