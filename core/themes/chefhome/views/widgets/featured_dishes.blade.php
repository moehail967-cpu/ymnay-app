<section class="ch-featured-dishes-widget" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        <div class="ch-sec-heading">
            <div class="ch-sec-title">{{ $title }}</div>
            @if(!empty($view_all_url))
                <a href="{{ $view_all_url }}" class="ch-view-all">
                    {{ __('View All') }} <i class="las la-arrow-right"></i>
                </a>
            @endif
        </div>

        @if($products->isNotEmpty())
            <div class="row g-3">
                @foreach($products as $product)
                    @php
                        $img_data  = get_attachment_image_by_id($product->image_id ?? null);
                        $img_url   = !empty($img_data) ? $img_data['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                        $price     = $product->sale_price ?? 0;
                        $regular   = $product->regular_price ?? $price;
                        $badge     = $product->badge ?? null;
                        $prod_url  = theme_product_url($product->slug);
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="ch-card">
                            <div class="ch-card-img">
                                <a href="{{ $prod_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                                </a>
                                @if($badge)
                                    <span class="ch-card-badge ch-badge-hot">{{ $badge->name }}</span>
                                @endif
                            </div>
                            <div class="ch-card-body">
                                <a href="{{ $prod_url }}" class="ch-card-title d-block text-decoration-none">{{ $product->name }}</a>
                                <div class="ch-card-meta">
                                    <span class="ch-stars">★★★★★</span>
                                    <span>·</span>
                                    <span>{{ $product->category->name ?? '' }}</span>
                                </div>
                                <div class="ch-card-footer">
                                    <div>
                                        <div class="ch-price">{{ amount_with_currency_symbol($price) }}</div>
                                        @if($regular > $price)
                                            <div class="ch-price-old">{{ amount_with_currency_symbol($regular) }}</div>
                                        @endif
                                    </div>
                                    <button class="ch-add-btn add-to-cart-btn"
                                            data-product_id="{{ $product->id }}"
                                            title="{{ __('Add to cart') }}">
                                        <i class="las la-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>
