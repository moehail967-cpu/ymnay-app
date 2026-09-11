<style>
/* ── Section Header ── */
.hf-section-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:36px; flex-wrap:wrap; gap:12px; }
.hf-section-title { font-family:'Outfit',sans-serif; font-size:22px; font-weight:700; color:#1a1a1a; letter-spacing:.04em; text-transform:uppercase; margin:0; }
.hf-section-view-all { font-size:13px; font-weight:600; color:#ff7857; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:gap .2s; }
.hf-section-view-all:hover { gap:8px; }

/* ── Casual New Product Card Design ── */
.hf-product-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    background-color: #fff;
    overflow: hidden;
    transition: box-shadow 0.25s ease, transform 0.25s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}
.hf-product-card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}
.hf-product-card-thumb {
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 200px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hf-product-card-thumb > a:first-child {
    display: block;
    width: 100%;
    height: 100%;
}
.hf-product-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.hf-product-card:hover .hf-product-card-thumb img {
    transform: scale(1.06);
}
.hf-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #ff7857;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    border-radius: 20px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
    z-index: 2;
}
.hf-wishlist {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 34px;
    height: 34px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    z-index: 2;
    color: #888;
    font-size: 16px;
}
.hf-wishlist:hover, .hf-wishlist .active {
    background: #ff7857;
    border-color: #ff7857;
    color: #fff;
}
.hf-wishlist i { color: inherit; }
.hf-product-card-contents {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex: 1;
}
.hf-product-category {
    color: #ff7857;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.8px;
    margin-bottom: 6px;
    text-transform: uppercase;
}
.hf-product-title {
    font-size: 14px;
    font-weight: 600;
    color: #222;
    margin-bottom: 6px;
    line-height: 1.4;
    flex: 1;
}
.hf-product-title a {
    color: inherit;
    text-decoration: none;
}
.hf-product-title a:hover {
    color: #ff7857;
}
.hf-stars {
    margin-bottom: 10px;
    font-size: 12px;
    color: #f59e0b;
}
.hf-product-price {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.hf-price-sale {
    color: #ff7857;
    font-size: 16px;
    font-weight: 700;
}
.hf-price-regular {
    color: #888;
    font-size: 13px;
    text-decoration: line-through;
}
.hf-add-to-cart {
    background-color: #ff7857 !important;
    color: #fff !important;
    border-radius: 8px !important;
    padding: 9px 16px !important;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-weight: 600 !important;
    font-size: 13px !important;
    border: none !important;
    text-decoration: none;
    cursor: pointer;
    transition: background-color 0.2s, transform 0.2s;
}
.hf-add-to-cart:hover {
    background-color: #e66646 !important;
    color: #fff !important;
    transform: translateY(-1px);
}

/* ── Carousel Overrides ── */
.hf-featured-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; }
.hf-featured-slider { position:relative; }
.hf-featured-slide { display:none; }
.hf-featured-slide.active { display:block; }
.hf-dots { display:flex; justify-content:center; gap:8px; margin-top:20px; }
.hf-dot { width:10px; height:10px; border-radius:50%; border:none; background:#ccc; cursor:pointer; }
.hf-dot.active { background:#ff7857; }
@media(max-width:992px) { .hf-featured-grid { grid-template-columns:repeat(3,1fr); } }
@media(max-width:768px) { .hf-featured-grid { grid-template-columns:repeat(2,1fr); } }
@media(max-width:576px) { .hf-featured-grid { grid-template-columns:1fr; } }
</style>

{{-- HexFashion: Featured Products (Popular Collection Style) --}}
@php $uid = 'hf_feat_' . substr(md5(uniqid()), 0, 8); @endphp
<section class="hf-featured-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-section-head">
            <h2 class="hf-section-title">{{ $title }}</h2>
            <a href="#" class="hf-section-view-all">{{ __('View All') }} &rarr;</a>
        </div>

        @if($slides->isNotEmpty())
        <div class="hf-featured-slider" id="{{ $uid }}-slider">
            @foreach($slides as $si => $slide)
            <div class="hf-featured-slide{{ $si === 0 ? ' active' : '' }}" data-slide="{{ $si }}">
                <div class="hf-featured-grid">
                    @foreach($slide as $product)
                    @php
                        $pImg = null;
                        if (!empty($product->image_id)) {
                            $pd = get_attachment_image_by_id($product->image_id);
                            $pImg = $pd['img_url'] ?? null;
                        }
                        $pd       = get_product_dynamic_price($product);
                        $price    = $pd['sale_price'];
                        $oldPrice = $pd['regular_price'];
                        $discount = $pd['discount'];
                        $catSlug  = $product->category?->slug ?? '';
                        $pUrl     = theme_product_url($product->slug ?? $product->id);
                    @endphp
                    <div class="hf-product-card h-100">
                        <div class="hf-product-card-thumb">
                            <a href="{{ $pUrl }}">
                                @if($pImg)
                                    <img src="{{ $pImg }}" alt="{{ $product->name }}" loading="lazy">
                                @else
                                    <div class="hf-thumb-placeholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#ccc;font-size:36px;"><i class="las la-shopping-bag"></i></div>
                                @endif
                            </a>
                            
                            <div class="hf-wishlist">
                                <a class="add-to-wishlist-btn cart-loading" href="javascript:void(0)" data-product_id="{{ $product->id }}">
                                    <i class="lar la-heart"></i>
                                </a>
                            </div>
                            
                            @if($discount)
                                <span class="hf-badge">{{ $discount }}% {{ __('Off') }}</span>
                            @endif
                        </div>

                        <div class="hf-product-card-contents">
                            <div class="hf-product-category">
                                {{ $product->category?->name ? strtoupper($product->category?->name) : 'FEATURED' }}
                            </div>
                            
                            <h5 class="hf-product-title">
                                <a href="{{ $pUrl }}"> {!! Str::words($product->name, 8) !!} </a>
                            </h5>
                            
                            <div class="hf-product-price">
                                <span class="hf-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                                @if($oldPrice)
                                    <span class="hf-price-regular">{{ amount_with_currency_symbol($oldPrice) }}</span>
                                @endif
                            </div>
                            
                            <div class="hf-product-action mt-auto">
                                <a href="javascript:void(0)" data-product_id="{{ $product->id }}" class="add-to-cart-btn hf-add-to-cart">
                                    <i class="las la-shopping-cart"></i> {{__('Add to Cart')}}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{-- Dots --}}
        @if($slides->count() > 1)
        <div class="hf-dots" id="{{ $uid }}-dots">
            @foreach($slides as $si => $slide)
            <button class="hf-dot{{ $si === 0 ? ' active' : '' }}" data-index="{{ $si }}" aria-label="{{ __('Slide') }} {{ $si + 1 }}"></button>
            @endforeach
        </div>
        @endif

        @else
        <p class="hf-no-data">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>

<script>
(function(){
    var total   = {{ $slides->count() }};
    if (total < 2) return;
    var current = 0;
    var slides  = document.querySelectorAll('#{{ $uid }}-slider .hf-featured-slide');
    var dots    = document.querySelectorAll('#{{ $uid }}-dots .hf-dot');

    function goTo(idx) {
        slides[current].classList.remove('active');
        dots[current] && dots[current].classList.remove('active');
        current = (idx + total) % total;
        slides[current].classList.add('active');
        dots[current] && dots[current].classList.add('active');
    }

    dots.forEach(function(dot, i){
        dot.addEventListener('click', function(){ goTo(i); });
    });
})();
</script>
