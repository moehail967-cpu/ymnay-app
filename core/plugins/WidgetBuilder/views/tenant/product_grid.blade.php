@php
    $col_class = match((int)$columns) {
        2 => 'col-6',
        4 => 'col-6 col-md-3',
        default => 'col-6 col-md-4',
    };
@endphp

<section class="xgpb-product-grid-section" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        @if(!empty($section_title))
            <h2 class="xgpb-section-title mb-4">{{ $section_title }}</h2>
        @endif

        @if($products->isNotEmpty())
            <div class="row g-4">
                @foreach($products as $product)
                    @php
                        $img_data = get_attachment_image_by_id($product->image);
                        $img_url  = !empty($img_data) ? $img_data['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                        $price    = $product->sale_price ?? 0;
                        $regular  = $product->regular_price ?? $price;
                    @endphp
                    <div class="{{ $col_class }}">
                        <div class="xgpb-pg-card card h-100 border-0" style="border-radius:{{$card_radius}}px;">
                            <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}"
                               class="xgpb-pg-img-wrap d-block overflow-hidden">
                                <img src="{{ $img_url }}" alt="{{ $product->name }}"
                                     class="card-img-top xgpb-pg-img w-100" loading="lazy">
                            </a>
                            <div class="card-body p-3">
                                <a href="{{ route('tenant.products.single-quick-view', $product->slug) }}"
                                   class="xgpb-pg-name d-block mb-2">{{ $product->name }}</a>
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
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>

<style>
.xgpb-pg-card { box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; transition: box-shadow .3s, transform .3s; }
.xgpb-pg-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.14); transform: translateY(-4px); }
.xgpb-pg-img-wrap { height: 220px; background: #f5f5f5; }
.xgpb-pg-img { height: 100%; object-fit: cover; transition: transform .4s; }
.xgpb-pg-card:hover .xgpb-pg-img { transform: scale(1.05); }
.xgpb-pg-name { font-size: .9rem; font-weight: 500; color: #1a1a2e; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.xgpb-section-title { font-size: 1.75rem; font-weight: 700; }
.xgpb-price-sale { font-weight: 700; color: var(--main-color, #0d6efd); }
</style>
