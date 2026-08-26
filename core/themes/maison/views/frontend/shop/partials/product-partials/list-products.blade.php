@foreach($products as $product)
@php
    $data          = theme_product_price($product);
    $regular_price = $data['regular_price'];
    $sale_price    = $data['sale_price'];
    $discount      = $data['discount'];
    $campaign_name = $data['campaign_name'];
    $img_url       = theme_product_image($product->image_id ?? null, 'grid');
    $product_url   = theme_product_url($product->slug);
@endphp

<div class="col-12">
    <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);padding:16px;display:flex;align-items:center;gap:18px;box-shadow:var(--ms-shadow);transition:box-shadow .25s;">
        {{-- Image --}}
        <div style="flex-shrink:0;width:88px;height:88px;border-radius:var(--ms-radius);overflow:hidden;background:var(--ms-warm);border:1px solid var(--ms-border);">
            @if($img_url)
                <a href="{{ $product_url }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                         style="width:100%;height:100%;object-fit:cover;display:block;">
                </a>
            @else
                <a href="{{ $product_url }}"
                   style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--ms-border);">
                    <i class="mdi mdi-image-outline" style="font-size:30px;"></i>
                </a>
            @endif
        </div>

        {{-- Info --}}
        <div style="flex:1;min-width:0;">
            @if($product->category)
                <div style="font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:4px;">
                    {{ $product->category->name }}
                </div>
            @endif
            <a href="{{ $product_url }}"
               style="font-size:15px;font-weight:500;color:var(--ms-dark);text-decoration:none;display:block;margin-bottom:6px;line-height:1.3;transition:color .2s;"
               onmouseover="this.style.color='var(--ms-linen-d)'"
               onmouseout="this.style.color='var(--ms-dark)'">
                {{ \Illuminate\Support\Str::words($product->name, 10) }}
            </a>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="font-size:16px;font-weight:600;color:var(--ms-dark);">
                    {{ amount_with_currency_symbol($sale_price) }}
                </span>
                @if($regular_price)
                    <span style="font-size:13px;color:var(--ms-muted);text-decoration:line-through;">
                        {{ amount_with_currency_symbol($regular_price) }}
                    </span>
                @endif
                @if($discount)
                    <span style="font-size:10px;font-weight:700;background:var(--ms-olive);color:#fff;padding:2px 8px;border-radius:2px;letter-spacing:.05em;">
                        {{ $discount }}% {{ __('off') }}
                    </span>
                @elseif($campaign_name)
                    <span style="font-size:10px;font-weight:700;background:var(--ms-olive);color:#fff;padding:2px 8px;border-radius:2px;letter-spacing:.05em;">
                        {{ $campaign_name }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div style="flex-shrink:0;display:flex;flex-direction:column;gap:8px;align-items:flex-end;">
            <button class="add-to-cart-btn ms-btn-dark"
                    data-product_id="{{ $product->id }}"
                    style="padding:8px 16px;font-size:12px;white-space:nowrap;">
                <i class="mdi mdi-cart-plus" style="margin-right:4px;"></i>
                {{ __('Add to Cart') }}
            </button>
            <button class="add-to-wishlist-btn"
                    data-product_id="{{ $product->id }}"
                    style="background:transparent;border:1px solid var(--ms-border);color:var(--ms-muted);border-radius:var(--ms-radius);padding:7px 14px;font-size:12px;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:4px;"
                    onmouseover="this.style.borderColor='var(--ms-linen)';this.style.color='var(--ms-linen-d)'"
                    onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-muted)'">
                <i class="mdi mdi-heart-outline"></i>
                {{ __('Wishlist') }}
            </button>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;justify-content:center;margin-top:8px;">
        @foreach($links as $page => $url)
            <button onclick="msFilterRequest({{ $page }})"
                    class="ms-page-btn {{ $page == $current_page ? 'active' : '' }}">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
