@php
    use Illuminate\Support\Str;
    $pt          = !empty($data['padding_top'])    ? (int)$data['padding_top']    : 80;
    $pb          = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url    = $data['view_all_url'] ?? route('tenant.shop');
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<section style="background:#fff;padding-top:{{$pt}}px;padding-bottom:{{$pb}}px;">
    <div class="container">

        @if(!empty($data['title']))
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:36px;flex-wrap:wrap;">
                <div>
                    <div style="display:inline-block;width:40px;height:4px;background:var(--tc-terra);border-radius:2px;margin-bottom:10px;"></div>
                    <h2 style="font-size:clamp(22px,3vw,32px);font-weight:900;color:var(--tc-dark);margin:0;letter-spacing:-.01em;">
                        {{ $data['title'] }}
                    </h2>
                </div>
                <a href="{{ $shop_url }}"
                   style="display:inline-flex;align-items:center;gap:6px;color:var(--tc-olive);font-weight:700;font-size:14px;text-decoration:none;border:2px solid var(--tc-olive);padding:8px 18px;border-radius:6px;transition:background .2s,color .2s;"
                   onmouseover="this.style.background='var(--tc-olive)';this.style.color='#fff'"
                   onmouseout="this.style.background='transparent';this.style.color='var(--tc-olive)'">
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
                        <div style="background:#fff;border:2px solid var(--tc-border);border-radius:10px;overflow:hidden;transition:border-color .2s,box-shadow .2s;height:100%;display:flex;flex-direction:column;"
                             onmouseover="this.style.borderColor='var(--tc-terra)';this.style.boxShadow='0 8px 28px rgba(196,124,90,.12)'"
                             onmouseout="this.style.borderColor='var(--tc-border)';this.style.boxShadow='none'">

                            {{-- Image --}}
                            <div style="position:relative;overflow:hidden;background:var(--tc-bg);">
                                <a href="{{ $product_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                                         style="width:100%;height:200px;object-fit:cover;display:block;transition:transform .3s;"
                                         onmouseover="this.style.transform='scale(1.05)'"
                                         onmouseout="this.style.transform='scale(1)'"
                                         loading="lazy">
                                </a>

                                {{-- Badge --}}
                                @if($badge)
                                    <span style="position:absolute;top:10px;left:10px;background:var(--tc-terra);color:#fff;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;padding:3px 10px;border-radius:4px;">
                                        {{ $badge->name }}
                                    </span>
                                @endif

                                {{-- Wishlist --}}
                                <button onclick="addToWishlist({{ $product->id }}, this)"
                                        title="{{ __('Add to Wishlist') }}"
                                        style="position:absolute;top:10px;right:10px;width:34px;height:34px;border-radius:50%;background:#fff;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.12);transition:background .2s;"
                                        onmouseover="this.style.background='var(--tc-terra-light)'"
                                        onmouseout="this.style.background='#fff'">
                                    <i class="mdi mdi-heart-outline" style="color:var(--tc-terra);font-size:16px;"></i>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div style="padding:14px;flex:1;display:flex;flex-direction:column;gap:8px;">
                                @if($product->category)
                                    <span style="font-size:11px;font-weight:700;color:var(--tc-olive);text-transform:uppercase;letter-spacing:.06em;">{{ $product->category->name }}</span>
                                @endif

                                <a href="{{ $product_url }}" style="font-size:14px;font-weight:700;color:var(--tc-dark);text-decoration:none;line-height:1.4;flex:1;">
                                    {{ Str::limit($product->name, 40) }}
                                </a>

                                @if($product->rating_count > 0)
                                    <div style="display:flex;align-items:center;gap:4px;">
                                        @for($i=1;$i<=5;$i++)
                                            <i class="mdi mdi-star" style="font-size:12px;color:{{ $i <= round($product->average_rating) ? '#F59E0B' : 'var(--tc-border)' }};"></i>
                                        @endfor
                                        <span style="font-size:11px;color:var(--tc-muted);">({{ $product->rating_count }})</span>
                                    </div>
                                @endif

                                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:auto;padding-top:8px;border-top:1px solid var(--tc-border);">
                                    <div>
                                        <span style="font-size:16px;font-weight:800;color:var(--tc-dark);">{{ amount_with_currency_symbol($sale_price) }}</span>
                                        @if($has_discount)
                                            <span style="font-size:13px;color:var(--tc-muted);text-decoration:line-through;margin-left:6px;">{{ amount_with_currency_symbol($reg_price) }}</span>
                                        @endif
                                    </div>
                                    <button onclick="addToCart({{ $product->id }})"
                                            title="{{ __('Add to Cart') }}"
                                            style="width:36px;height:36px;border-radius:6px;background:var(--tc-olive);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s;"
                                            onmouseover="this.style.background='var(--tc-olive-deep)'"
                                            onmouseout="this.style.background='var(--tc-olive)'">
                                        <i class="mdi mdi-cart-plus" style="color:#fff;font-size:16px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--tc-muted);">{{ __('No products found.') }}</p>
        @endif

    </div>
</section>
