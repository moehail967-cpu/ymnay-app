@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--tz-bg);">
    <div class="container">
        @if(!empty($data['title']))
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 style="font-size:24px; font-weight:900; color:var(--tz-text); margin:0;">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" style="font-size:13px; font-weight:700; color:var(--tz-blue); text-decoration:none;">{{ __('View All') }} <i class="mdi mdi-arrow-right"></i></a>
            </div>
        @endif
        @if($data['products']->isNotEmpty())
            <div class="row g-3">
                @foreach($data['products'] as $product)
                    @php
                        $img = get_attachment_image_by_id($product->image);
                        $img_url = !empty($img) ? $img['img_url'] : $placeholder;
                        $product_url = route('tenant.products.single-quick-view', $product->slug);
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius-xl);overflow:hidden;height:100%;display:flex;flex-direction:column;transition:border-color .2s;">
                            <div style="position:relative;height:200px;overflow:hidden;">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;">
                                </a>
                                @if($product->badge)
                                    <span style="position:absolute;top:10px;left:10px;background:var(--tz-blue);color:#fff;font-size:10px;font-weight:700;padding:3px 8px;border-radius:var(--tz-radius);">{{ $product->badge->name }}</span>
                                @endif
                                <button onclick="addToWishlist({{ $product->id }}, this)" style="position:absolute;top:10px;right:10px;width:30px;height:30px;border-radius:50%;background:var(--tz-surface);border:1px solid var(--tz-border);display:flex;align-items:center;justify-content:center;color:var(--tz-muted);cursor:pointer;">
                                    <i class="mdi mdi-heart-outline" style="font-size:14px;"></i>
                                </button>
                            </div>
                            <div style="padding:12px;flex:1;display:flex;flex-direction:column;">
                                <div style="font-size:11px;font-weight:700;color:var(--tz-blue);margin-bottom:4px;">{{ $product->category?->name }}</div>
                                <a href="{{ $product_url }}" style="font-size:13px;font-weight:700;color:var(--tz-text);text-decoration:none;margin-bottom:8px;flex:1;">{{ Str::limit($product->name, 40) }}</a>
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:auto;">
                                    <div>
                                        <span style="font-size:16px;font-weight:800;color:var(--tz-blue);">{{ amount_with_currency_symbol($product->sale_price ?? 0) }}</span>
                                        @if(($product->regular_price ?? 0) > ($product->sale_price ?? 0))
                                            <span style="font-size:11px;color:var(--tz-muted);text-decoration:line-through;margin-left:4px;">{{ amount_with_currency_symbol($product->regular_price) }}</span>
                                        @endif
                                    </div>
                                    <button onclick="addToCart({{ $product->id }})" style="background:var(--tz-blue);color:#fff;border:none;padding:6px 12px;border-radius:var(--tz-radius);font-size:12px;font-weight:700;cursor:pointer;">
                                        <i class="mdi mdi-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
