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
                <div>
                    <span style="display:inline-block; background:var(--fm-green-light); color:var(--fm-green); border:1px solid var(--fm-border); padding:4px 14px; border-radius:50px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; margin-bottom:8px;">
                        {{ __('Fresh Today') }}
                    </span>
                    <h2 class="fm-section-title mb-0"
                        style="font-size:clamp(20px,2.5vw,28px); font-weight:800; color:var(--fm-dark); font-family:var(--fm-font-head);">
                        {{ $data['title'] }}
                    </h2>
                </div>
                <a href="{{ $shop_url }}"
                   style="color:var(--fm-green);font-weight:700;text-decoration:none;"
                   onmouseover="this.style.color='var(--fm-green-deep)'" onmouseout="this.style.color='var(--fm-green)'">
                    {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>
        @endif

        @if($data['products']->isNotEmpty())
            <div class="row g-4">
                @foreach($data['products'] as $product)
                    @php
                        $img          = get_attachment_image_by_id($product->image);
                        $img_url      = !empty($img) ? $img['img_url'] : $placeholder;
                        $sale_price   = $product->sale_price ?? 0;
                        $reg_price    = $product->regular_price ?? $sale_price;
                        $has_discount = $reg_price > $sale_price;
                        $badge        = $product->badge ?? null;
                        $product_url  = route('tenant.products.single-quick-view', $product->slug);
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="fm-card"
                             style="background:#fff; border:1px solid var(--fm-border); border-radius:16px; overflow:hidden; transition:box-shadow .2s;"
                             onmouseover="this.style.boxShadow='0 6px 24px rgba(46,125,50,.12)'"
                             onmouseout="this.style.boxShadow='none'">

                            {{-- Card Image --}}
                            <div style="position:relative; overflow:hidden; aspect-ratio:1/1; background:var(--fm-green-light);">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                                         loading="lazy"
                                         style="width:100%; height:100%; object-fit:cover; transition:transform .3s;"
                                         onmouseover="this.style.transform='scale(1.04)'"
                                         onmouseout="this.style.transform='scale(1)'">
                                </a>

                                {{-- Badge --}}
                                @if($badge)
                                    <span style="position:absolute; top:10px; left:10px; background:var(--fm-orange); color:#fff; font-size:11px; font-weight:700; padding:3px 10px; border-radius:50px;">
                                        {{ $badge->name }}
                                    </span>
                                @endif

                                {{-- Discount badge --}}
                                @if($has_discount)
                                    @php
                                        $discount_pct = round((($reg_price - $sale_price) / $reg_price) * 100);
                                    @endphp
                                    <span style="position:absolute; top:10px; right:10px; background:var(--fm-green); color:#fff; font-size:11px; font-weight:700; padding:3px 8px; border-radius:50px;">
                                        -{{ $discount_pct }}%
                                    </span>
                                @endif

                                {{-- Wishlist --}}
                                <button title="{{ __('Add to Wishlist') }}"
                                        onclick="addToWishlist({{ $product->id }}, this)"
                                        style="position:absolute; bottom:10px; right:10px; width:34px; height:34px; border-radius:50%; border:none; background:#fff; color:var(--fm-green); display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,.1); transition:background .2s;"
                                        onmouseover="this.style.background='var(--fm-green-light)'" onmouseout="this.style.background='#fff'">
                                    <i class="mdi mdi-heart-outline" style="font-size:16px;"></i>
                                </button>
                            </div>

                            {{-- Card Body --}}
                            <div style="padding:14px 16px;">
                                @if($product->category)
                                    <div style="font-size:11px; font-weight:600; color:var(--fm-green); text-transform:uppercase; letter-spacing:.05em; margin-bottom:4px;">
                                        {{ $product->category->name }}
                                    </div>
                                @endif

                                <a href="{{ $product_url }}"
                                   style="font-size:14px; font-weight:700; color:var(--fm-dark); text-decoration:none; display:block; margin-bottom:6px; line-height:1.4;">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                {{-- Star Rating --}}
                                @if($product->rating_count > 0)
                                    <div style="margin-bottom:8px;">
                                        @for($s = 1; $s <= 5; $s++)
                                            <i class="mdi {{ $s <= round($product->average_rating) ? 'mdi-star' : 'mdi-star-outline' }}"
                                               style="font-size:12px; color:#FFC107;"></i>
                                        @endfor
                                        <span style="font-size:11px; color:var(--fm-muted); margin-left:4px;">
                                            ({{ $product->rating_count }})
                                        </span>
                                    </div>
                                @endif

                                {{-- Price + ATC --}}
                                <div class="d-flex align-items-center justify-content-between gap-2">
                                    <div>
                                        <span style="font-size:16px; font-weight:800; color:var(--fm-orange);">
                                            {{ amount_with_currency_symbol($sale_price) }}
                                        </span>
                                        @if($has_discount)
                                            <span style="font-size:13px; color:var(--fm-muted); text-decoration:line-through; margin-left:4px;">
                                                {{ amount_with_currency_symbol($reg_price) }}
                                            </span>
                                        @endif
                                    </div>
                                    <button title="{{ __('Add to Cart') }}"
                                            data-product-id="{{ $product->id }}"
                                            onclick="addToCart({{ $product->id }})"
                                            style="width:36px; height:36px; border-radius:50%; border:none; background:var(--fm-green); color:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0; transition:background .2s;"
                                            onmouseover="this.style.background='var(--fm-green-deep)'" onmouseout="this.style.background='var(--fm-green)'">
                                        <i class="mdi mdi-plus" style="font-size:18px;"></i>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--fm-muted);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
