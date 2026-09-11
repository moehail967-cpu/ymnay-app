{{-- Medicom: Featured Products --}}
<section class="mc-featured-products" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        
        <div class="mc-fp-header">
            <h2 class="mc-fp-title">{{ $title }}</h2>
            @if(!empty($view_all_url))
                <a href="{{ $view_all_url }}" class="mc-fp-view-all">View All &rarr;</a>
            @endif
        </div>

        <div class="mc-fp-slider">
            @forelse($products as $product)
                @php
                    $pd       = get_product_dynamic_price($product);
                    $price    = $pd['sale_price'];
                    $oldPrice = $pd['regular_price'];
                    $discount = $pd['discount'];
                    $pImg     = theme_product_image($product->image_id ?? null, 'grid');
                    $pUrl     = theme_product_url($product->slug ?? $product->id);
                @endphp
                <div class="mc-fp-item" style="padding: 10px;">
                    <div class="mc-card">
                        <div class="mc-card-img-wrap">
                            <a href="{{ $pUrl }}">
                                @if($pImg)
                                <img src="{{ $pImg }}" alt="{{ $product->name }}" class="mc-card-img" loading="lazy">
                                @else
                                <div class="mc-card-img-ph"><i class="las la-laptop"></i></div>
                                @endif
                            </a>
                            <button class="add-to-wishlist-btn mc-card-wish" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}">
                                <i class="las la-heart"></i>
                            </button>
                            @if($discount)
                            <span class="mc-card-badge">{{ $discount }}% {{ __('off') }}</span>
                            @endif
                        </div>
                        <div class="mc-card-body">
                            <span class="mc-card-cat">{{ $product->category?->name ?? 'Category' }}</span>
                            <a href="{{ $pUrl }}" class="mc-card-name">{{ Str::limit($product->name, 45) }}</a>
                            <div class="mc-card-prices">
                                <span class="mc-card-price">{{ amount_with_currency_symbol($price) }}</span>
                                @if($oldPrice)
                                <span class="mc-card-old">{{ amount_with_currency_symbol($oldPrice) }}</span>
                                @endif
                            </div>
                            <button class="add-to-cart-btn mc-card-atc" data-product_id="{{ $product->id }}" title="{{ __('Add to Cart') }}">
                                <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12"><p class="text-center text-muted py-5">{{ __('No featured products found') }}</p></div>
            @endforelse
        </div>
    </div>
</section>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof jQuery !== 'undefined' && jQuery.fn.slick) {
            jQuery('.mc-fp-slider').not('.slick-initialized').slick({
                dots: true,
                arrows: false,
                infinite: true,
                speed: 300,
                slidesToShow: 4,
                slidesToScroll: 1,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        }
    });
</script>
