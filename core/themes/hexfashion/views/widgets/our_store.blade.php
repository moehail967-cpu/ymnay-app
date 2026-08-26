<style>
.hf-section-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:36px; flex-wrap:wrap; gap:12px; }
.hf-section-title { font-family:'Outfit',sans-serif; font-size:22px; font-weight:700; color:#1a1a1a; letter-spacing:.04em; text-transform:uppercase; margin:0; }
.hf-section-view-all { font-size:13px; font-weight:600; color:#ff7857; text-decoration:none; display:inline-flex; align-items:center; gap:4px; transition:gap .2s; }
.hf-section-view-all:hover { gap:8px; }
.hf-no-data { color:#aaa; font-size:14px; text-align:center; padding:40px 0; }
/* ── Our Store ── */
.hf-store-section { background:#fff; }
.hf-store-layout { display:flex; gap:28px; align-items:flex-start; }
.hf-store-sidebar { width:190px; flex-shrink:0; }
.hf-store-catlist { list-style:none; margin:0; padding:0; display:flex; flex-direction:column; gap:2px; }
.hf-store-catlist li { margin:0; }
.hf-store-cat-btn { width:100%; text-align:left; background:none; border:none; padding:10px 16px; font-size:13px; font-weight:500; color:#666; cursor:pointer; border-radius:8px; transition:background .15s,color .15s; border-left:3px solid transparent; }
.hf-store-cat-btn:hover,.hf-store-cat-btn.active { background:#fff5ef; color:#ff7857; font-weight:700; border-left-color:#ff7857; }
.hf-store-grid { flex:1; display:grid; grid-template-columns:repeat(3,1fr); gap:20px; }
.hf-store-item { background:#fff; border-radius:14px; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 1px 8px rgba(0,0,0,.06); transition:box-shadow .2s,transform .2s; }
.hf-store-item:hover { box-shadow:0 6px 24px rgba(0,0,0,.10); transform:translateY(-3px); }
.hf-store-img-wrap { display:block; position:relative; background:#f8f4f0; aspect-ratio:4/5; overflow:hidden; }
.hf-store-img { width:100%; height:100%; object-fit:cover; transition:transform .35s; }
.hf-store-item:hover .hf-store-img { transform:scale(1.05); }
.hf-store-img-ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#ccc; font-size:36px; }
.hf-store-badge { position:absolute; top:10px; left:10px; background:#ff7857; color:#fff; font-size:10px; font-weight:700; border-radius:20px; padding:3px 9px; }
/* add-to-cart slide-up on hover */
.hf-store-hover-bar { position:absolute; bottom:0; left:0; right:0; background:#ff7857; display:flex; align-items:center; justify-content:center; padding:9px; opacity:0; transform:translateY(100%); transition:opacity .2s,transform .2s; }
.hf-store-item:hover .hf-store-hover-bar { opacity:1; transform:translateY(0); }
.hf-store-add-btn { color:#fff; font-size:12px; font-weight:700; text-decoration:none; display:flex; align-items:center; gap:6px; }
.hf-store-info { padding:12px 14px 14px; display:flex; flex-direction:column; gap:5px; }
.hf-store-name { font-size:13px; font-weight:600; color:#1a1a1a; text-decoration:none; display:block; line-height:1.4; }
.hf-store-name:hover { color:#ff7857; }
.hf-store-price-row { display:flex; align-items:center; gap:6px; }
.hf-store-price { font-size:15px; font-weight:800; color:#ff7857; }
.hf-store-old-price { font-size:12px; color:#bbb; text-decoration:line-through; }
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
{{-- Casual: Our Store --}}
@php $uid = 'csstore' . substr(md5(uniqid()), 0, 8); @endphp
<section class="hf-store-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-section-head">
            <h2 class="hf-section-title">{{ $title }}</h2>
        </div>

        <div class="hf-store-layout" id="{{ $uid }}-wrap">
            {{-- Sidebar: Categories --}}
            <aside class="hf-store-sidebar">
                <ul class="hf-store-catlist">
                    <li>
                        <button class="hf-store-cat-btn active" data-cat="all" data-target="{{ $uid }}">
                            {{ __('All') }}
                        </button>
                    </li>
                    @foreach($categories as $cat)
                    <li>
                        <button class="hf-store-cat-btn" data-cat="{{ $cat->slug ?? $cat->id }}" data-target="{{ $uid }}">
                            {{ $cat->name }}
                        </button>
                    </li>
                    @endforeach
                </ul>
            </aside>

            {{-- Products Grid --}}
            <div class="hf-store-grid" id="{{ $uid }}">
                @forelse($products as $product)
                @php
                    $pImg = null;
                    if (!empty($product->image_id)) {
                        $pd = get_attachment_image_by_id($product->image_id);
                        $pImg = $pd['img_url'] ?? null;
                    }
                    $hasDiscount = $product->sale_price && $product->sale_price < $product->price;
                    $price    = $hasDiscount ? $product->sale_price : $product->price;
                    $oldPrice = $hasDiscount ? $product->price : null;
                    $catSlug  = $product->category?->slug ?? '';
                    $pUrl     = theme_product_url($product->slug ?? $product->id);
                    $cartUrl  = theme_add_to_cart_url($product->id);
                @endphp
                <div class="hf-store-item h-100" data-cat="{{ $catSlug }}">
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
                                {{ $product->category?->name ? strtoupper($product->category?->name) : 'BODY CARE' }}
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
                @empty
                <p class="hf-no-data">{{ __('No products found.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>

<script>
(function(){
    var btns = document.querySelectorAll('[data-target="{{ $uid }}"]');
    var grid = document.getElementById('{{ $uid }}');
    if (!grid || !btns.length) return;
    btns.forEach(function(btn){
        btn.addEventListener('click', function(){
            btns.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            var cat = btn.getAttribute('data-cat');
            grid.querySelectorAll('.hf-store-item').forEach(function(item){
                item.style.display = (cat === 'all' || item.getAttribute('data-cat') === cat) ? '' : 'none';
            });
        });
    });
})();
</script>
