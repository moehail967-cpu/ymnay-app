@foreach($products as $product)
@php
    $data          = theme_product_price($product);
    $sale_price    = $data['sale_price'];
    $reg_price     = $data['regular_price'];
    $discount      = $data['discount'];
    $campaign_name = $data['campaign_name'];
    $img           = get_attachment_image_by_id($product->image_id ?? null, 'grid');
    $img_url       = $img['img_url'] ?? null;
    $product_url   = theme_product_url($product->slug);
@endphp
<div class="col-12">
    <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;display:flex;gap:0;box-shadow:var(--tr-shadow);">
        <div style="width:160px;min-width:160px;height:160px;overflow:hidden;position:relative;background:var(--tr-cream);flex-shrink:0;">
            @if($img_url)
                <a href="{{ $product_url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;"></a>
            @else
                <a href="{{ $product_url }}" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;"><i class="mdi mdi-hiking" style="font-size:40px;color:var(--tr-olive);opacity:.3;"></i></a>
            @endif
            @if($discount)
                <span style="position:absolute;top:8px;left:8px;background:var(--tr-terra);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;">-{{ $discount }}%</span>
            @elseif($campaign_name)
                <span style="position:absolute;top:8px;left:8px;background:var(--tr-terra);color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:4px;">{{ $campaign_name }}</span>
            @endif
        </div>
        <div style="flex:1;padding:16px 20px;display:flex;flex-direction:column;justify-content:space-between;min-width:0;">
            <div>
                <a href="{{ $product_url }}" style="font-size:16px;font-weight:800;color:var(--tr-bark);text-decoration:none;display:block;margin-bottom:6px;">{{ $product->name }}</a>
                <div style="margin-bottom:8px;">{!! theme_star_rating($product) !!}</div>
                @if($product->short_description)
                <p style="font-size:13px;color:var(--tr-stone);margin:0 0 10px;line-height:1.6;">{{ \Illuminate\Support\Str::limit(strip_tags($product->short_description), 120) }}</p>
                @endif
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;">
                <div style="display:flex;align-items:baseline;gap:8px;">
                    <span style="font-size:18px;font-weight:900;color:var(--tr-bark);">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($discount && $reg_price)
                        <span style="font-size:13px;color:var(--tr-stone);text-decoration:line-through;">{{ amount_with_currency_symbol($reg_price) }}</span>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <button class="tr-btn tr-btn-primary add-to-cart-btn" data-product_id="{{ $product->id }}" style="font-size:12px;padding:8px 16px;">
                        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                    </button>
                    <button class="add-to-wishlist-btn" data-product_id="{{ $product->id }}" title="{{ __('Wishlist') }}"
                            style="width:36px;height:36px;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);background:#fff;color:var(--tr-stone);cursor:pointer;display:flex;align-items:center;justify-content:center;">
                        <i class="mdi mdi-heart-outline"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:24px;flex-wrap:wrap;">
        @foreach($links as $page => $url)
            <button onclick="tcFilterRequest({{ $page }})"
                    style="width:36px;height:36px;border-radius:var(--tr-radius);border:1.5px solid {{ $page == $current_page ? 'var(--tr-olive)' : 'var(--tr-border)' }};background:{{ $page == $current_page ? 'var(--tr-olive)' : '#fff' }};color:{{ $page == $current_page ? '#fff' : 'var(--tr-bark)' }};font-size:13px;font-weight:700;cursor:pointer;">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
