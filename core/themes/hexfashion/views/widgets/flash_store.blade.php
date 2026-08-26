<style>
/* ── Flash Store ── */
.hf-flash-store-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; }
.hf-flash-store-arrows { display:flex; gap:8px; }
.hf-flash-arr { width:38px; height:38px; border-radius:50%; border:2px solid #e0d8d0; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#333; font-size:16px; transition:background .2s,border-color .2s,color .2s; }
.hf-flash-arr:hover { background:#ff7857; border-color:#ff7857; color:#fff; }
.hf-flash-slider-wrap { display:flex; gap:20px; overflow-x:auto; scroll-snap-type:x mandatory; -ms-overflow-style:none; scrollbar-width:none; padding-bottom:4px; }
.hf-flash-slider-wrap::-webkit-scrollbar { display:none; }
.hf-flash-item { min-width:280px; max-width:280px; scroll-snap-align:start; background:#fff; border-radius:14px; overflow:hidden; display:flex; flex-direction:column; transition:box-shadow .2s; flex-shrink:0; }
.hf-flash-item:hover { box-shadow:0 4px 20px rgba(0,0,0,.09); }
.hf-flash-img-wrap { display:block; position:relative; aspect-ratio:4/5; overflow:hidden; }
.hf-flash-img { width:100%; height:100%; object-fit:cover; transition:transform .3s; }
.hf-flash-item:hover .hf-flash-img { transform:scale(1.04); }
.hf-flash-img-ph { width:100%; height:100%; background:#f0ebe5; display:flex; align-items:center; justify-content:center; color:#ccc; font-size:36px; }
.hf-flash-sale-tag { position:absolute; top:10px; left:10px; background:#ff7857; color:#fff; font-size:10px; font-weight:700; border-radius:4px; padding:3px 8px; }
.hf-flash-info { padding:14px; display:flex; flex-direction:column; gap:6px; flex:1; }
.hf-flash-name { font-size:13px; font-weight:600; color:#1a1a1a; text-decoration:none; display:block; }
.hf-flash-name:hover { color:#ff7857; }
.hf-flash-price-row { display:flex; align-items:center; gap:6px; }
.hf-flash-price { font-size:14px; font-weight:700; color:#ff7857; }
.hf-flash-old-price { font-size:12px; color:#aaa; text-decoration:line-through; }
.hf-flash-cart-btn { display:inline-flex; align-items:center; gap:6px; background:#1a1a1a; color:#fff; font-size:12px; font-weight:600; padding:8px 14px; border-radius:6px; text-decoration:none; transition:background .2s; margin-top:auto; justify-content:center; }
.hf-flash-cart-btn:hover { background:#ff7857; color:#fff; }

/* Casual New Product Card Design */
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
.hf-wishlist:hover {
    background: #ff7857;
    border-color: #ff7857;
    color: #fff;
}
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
</style>
{{-- Casual: Flash Store --}}
@php $uid = 'csflash' . substr(md5(uniqid()), 0, 8); @endphp
<section class="hf-flash-store-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-flash-store-head">
            <h2 class="hf-section-title">{{ $title }}</h2>
            <div class="hf-flash-store-arrows">
                <button class="hf-flash-arr hf-flash-prev" id="{{ $uid }}-prev" aria-label="{{ __('Previous') }}">
                    <i class="las la-angle-left"></i>
                </button>
                <button class="hf-flash-arr hf-flash-next" id="{{ $uid }}-next" aria-label="{{ __('Next') }}">
                    <i class="las la-angle-right"></i>
                </button>
            </div>
        </div>

        @if($products->isNotEmpty())
        <div class="hf-flash-slider-wrap" id="{{ $uid }}">
            @foreach($products as $product)
            @php
                $pImg = null;
                if (!empty($product->image_id)) {
                    $pd = get_attachment_image_by_id($product->image_id);
                    $pImg = $pd['img_url'] ?? null;
                }
                $hasDiscount = $product->sale_price && $product->sale_price < $product->price;
                $price    = $hasDiscount ? $product->sale_price : $product->price;
                $oldPrice = $hasDiscount ? $product->price : null;
                $pUrl     = theme_product_url($product->slug ?? $product->id);
                $cartUrl  = theme_add_to_cart_url($product->id);
            @endphp
            <div class="hf-flash-item">
                <div class="hf-product-card h-100">
                    <div class="hf-product-card-thumb">
                        <a href="{{ $pUrl }}">
                            @if($pImg)
                                <img src="{{ $pImg }}" alt="{{ $product->name }}" loading="lazy">
                            @else
                                <div class="hf-thumb-placeholder"><i class="las la-shopping-bag"></i></div>
                            @endif
                        </a>

                        <div class="hf-wishlist">
                            <a class="add-to-wishlist-btn cart-loading" href="javascript:void(0)" data-product_id="{{ $product->id }}">
                                <i class="lar la-heart"></i>
                            </a>
                        </div>

                        <div class="hf-product-badges">
                            @if($hasDiscount)
                                @php $discountPercent = round((($product->price - $product->sale_price) / $product->price) * 100); @endphp
                                <span class="hf-product-badge hf-product-badge-sale">{{ $discountPercent }}% {{ __('Off') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="hf-product-card-contents">
                        <div class="hf-product-category">
                            {{ $product->category?->name ? strtoupper($product->category?->name) : 'SKINCARE' }}
                        </div>

                        <h5 class="hf-product-title">
                            <a href="{{ $pUrl }}"> {!! Str::words($product->name, 15) !!} </a>
                        </h5>

                        <div class="hf-product-price">
                            <span class="hf-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                            @if($oldPrice)
                                <span class="hf-price-regular">{{ amount_with_currency_symbol($oldPrice) }}</span>
                            @endif
                        </div>

                        <div class="hf-product-action">
                            <a href="javascript:void(0)" data-product_id="{{ $product->id }}" class="add-to-cart-btn hf-add-to-cart">
                                <i class="las la-shopping-cart"></i> {{__('Add to Cart')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="hf-no-data">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>

<script>
(function(){
    var wrap = document.getElementById('{{ $uid }}');
    var prev = document.getElementById('{{ $uid }}-prev');
    var next = document.getElementById('{{ $uid }}-next');
    if (!wrap) return;
    var scrollAmt = 300;
    if (prev) prev.addEventListener('click', function(){ wrap.scrollBy({ left: -scrollAmt, behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function(){ wrap.scrollBy({ left: scrollAmt, behavior: 'smooth' }); });
})();
</script>
