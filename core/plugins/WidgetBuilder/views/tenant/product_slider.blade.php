<section class="xgpb-product-slider-section" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        @if(!empty($section_title))
            <h2 class="xgpb-section-title mb-4">{{ $section_title }}</h2>
        @endif

        @if($products->isNotEmpty())
            <div class="xgpb-ps-track">
                @foreach($products as $product)
                    @php
                        $img_data = get_attachment_image_by_id($product->image);
                        $img_url  = !empty($img_data) ? $img_data['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                        $price    = $product->sale_price ?? 0;
                        $regular  = $product->regular_price ?? $price;
                    @endphp
                    <div class="xgpb-ps-card" style="border-radius:{{$card_radius}}px;">
                        <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}"
                           class="xgpb-ps-img-wrap d-block overflow-hidden">
                            <img src="{{ $img_url }}" alt="{{ $product->name }}"
                                 class="xgpb-ps-img w-100" loading="lazy">
                        </a>
                        <div class="p-3">
                            <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}"
                               class="xgpb-ps-name d-block mb-2">{{ $product->name }}</a>
                            @if($show_price)
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="xgpb-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                                    @if($regular > $price)
                                        <span class="text-muted text-decoration-line-through small">
                                            {{ amount_with_currency_symbol($regular) }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                            @if($show_cart_btn)
                                <button class="btn btn-primary btn-sm w-100 add-to-cart-btn"
                                        data-product_id="{{ $product->id }}">
                                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>

<style>
.xgpb-ps-track { display: flex; gap: 1rem; overflow-x: auto; scroll-snap-type: x mandatory; padding-bottom: .5rem; scrollbar-width: thin; }
.xgpb-ps-track::-webkit-scrollbar { height: 4px; }
.xgpb-ps-track::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 2px; }
.xgpb-ps-card { flex: 0 0 220px; scroll-snap-align: start; background: #fff; border: 1px solid #e9ecef; overflow: hidden; transition: box-shadow .3s, transform .3s; }
.xgpb-ps-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.12); transform: translateY(-3px); }
.xgpb-ps-img-wrap { height: 200px; background: #f5f5f5; }
.xgpb-ps-img { height: 100%; object-fit: cover; transition: transform .4s; }
.xgpb-ps-card:hover .xgpb-ps-img { transform: scale(1.05); }
.xgpb-ps-name { font-size: .85rem; font-weight: 500; color: #1a1a2e; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.xgpb-section-title { font-size: 1.75rem; font-weight: 700; }
.xgpb-price-sale { font-weight: 700; color: var(--main-color, #0d6efd); }
</style>
