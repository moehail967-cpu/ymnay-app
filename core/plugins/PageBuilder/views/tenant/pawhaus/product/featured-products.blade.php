@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">
        @if(!empty($data['title']))
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 style="font-size:26px; font-weight:800; color:var(--ph-dark); margin:0;">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" style="font-size:13px; font-weight:700; color:var(--ph-terra); text-decoration:none;">{{ __('View All') }} <i class="mdi mdi-arrow-right"></i></a>
            </div>
        @endif
        @if($data['products']->isNotEmpty())
            <div class="row g-4">
                @foreach($data['products'] as $product)
                    @php
                        $img = get_attachment_image_by_id($product->image);
                        $img_url = !empty($img) ? $img['img_url'] : $placeholder;
                        $product_url = route('tenant.products.single-quick-view', $product->slug);
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div style="background:#fff;border:1.5px solid var(--ph-border);border-radius:var(--ph-radius);overflow:hidden;transition:box-shadow .25s,transform .25s;height:100%;display:flex;flex-direction:column;">
                            <div style="position:relative;height:220px;overflow:hidden;">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
                                </a>
                                @if($product->badge)
                                    <span style="position:absolute;top:10px;left:10px;background:var(--ph-terra);color:#fff;font-size:10px;font-weight:700;padding:4px 10px;border-radius:20px;">{{ $product->badge->name }}</span>
                                @endif
                                <button onclick="addToWishlist({{ $product->id }}, this)" style="position:absolute;top:10px;right:10px;width:32px;height:32px;border-radius:50%;background:#fff;border:1.5px solid var(--ph-border);display:flex;align-items:center;justify-content:center;font-size:15px;color:var(--ph-muted);cursor:pointer;transition:all .2s;">
                                    <i class="mdi mdi-heart-outline"></i>
                                </button>
                            </div>
                            <div style="padding:14px;display:flex;flex-direction:column;flex:1;">
                                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--ph-terra);margin-bottom:5px;">{{ $product->category?->name }}</div>
                                <a href="{{ $product_url }}" style="font-size:14px;font-weight:700;color:var(--ph-dark);text-decoration:none;margin-bottom:10px;line-height:1.4;flex:1;">{{ Str::limit($product->name, 40) }}</a>
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                                    <span style="font-size:16px;font-weight:800;color:var(--ph-terra);">{{ amount_with_currency_symbol($product->sale_price ?? 0) }}</span>
                                    @if(($product->regular_price ?? 0) > ($product->sale_price ?? 0))
                                        <span style="font-size:12px;color:var(--ph-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($product->regular_price) }}</span>
                                    @endif
                                </div>
                                <button onclick="addToCart({{ $product->id }})" style="width:100%;padding:9px;border-radius:var(--ph-radius-xl);border:1.5px solid var(--ph-terra);background:transparent;color:var(--ph-terra);font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;">
                                    <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--ph-muted);">{{ __('No products found.') }}</p>
        @endif
    </div>
</section>
