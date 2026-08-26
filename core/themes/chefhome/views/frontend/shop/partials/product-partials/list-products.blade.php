{{-- ChefHome: Product list partial — table view for AJAX filter response --}}
<table class="ch-list-table" style="width:100%;border-collapse:collapse;">
    <thead style="background:#f8f8f8;border-bottom:1px solid var(--ch-border);">
        <tr>
            <th style="padding:12px;text-align:left;font-size:13px;font-weight:700;color:var(--ch-dark);">{{ __('Product') }}</th>
            <th style="padding:12px;text-align:center;font-size:13px;font-weight:700;color:var(--ch-dark);width:80px;">{{ __('Price') }}</th>
            <th style="padding:12px;text-align:center;font-size:13px;font-weight:700;color:var(--ch-dark);width:100px;">{{ __('Rating') }}</th>
            <th style="padding:12px;text-align:center;font-size:13px;font-weight:700;color:var(--ch-dark);width:80px;">{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        @php
            $data          = theme_product_price($product);
            $sale_price    = $data['sale_price'];
            $regular_price = $data['regular_price'];
            $discount      = $data['discount'];
            $campaign_name = $data['campaign_name'];
            $badge         = $product->badge?->name ?? null;
            $img_url       = theme_product_image($product->image_id ?? null, 'grid');
            $product_url   = theme_product_url($product->slug);
        @endphp
        <tr style="border-bottom:1px solid var(--ch-border);">
            <td style="padding:16px 12px;">
                <div style="display:flex;gap:12px;align-items:flex-start;">
                    <div style="width:48px;height:48px;border-radius:var(--ch-radius-sm);background:var(--ch-warm);flex-shrink:0;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                        @if($img_url)
                            <img src="{{ $img_url }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <span style="font-size:24px;">🍽️</span>
                        @endif
                    </div>
                    <div style="flex:1;min-width:0;">
                        <a href="{{ $product_url }}" style="font-size:14px;font-weight:700;color:var(--ch-dark);text-decoration:none;word-break:break-word;">
                            {{ \Illuminate\Support\Str::words($product->name, 6) }}
                        </a>
                        @if($discount)
                            <span class="ch-card-badge ch-badge-sale" style="display:inline-block;margin-top:4px;">{{ $discount }}% {{ __('off') }}</span>
                        @elseif($badge)
                            <span class="ch-card-badge ch-badge-hot" style="display:inline-block;margin-top:4px;">{{ $badge }}</span>
                        @elseif($campaign_name)
                            <span class="ch-card-badge ch-badge-new" style="display:inline-block;margin-top:4px;">{{ $campaign_name }}</span>
                        @endif
                    </div>
                </div>
            </td>
            <td style="padding:16px 12px;text-align:center;">
                <div style="font-size:14px;font-weight:700;color:var(--ch-red);">
                    {{ amount_with_currency_symbol($sale_price) }}
                </div>
                @if($regular_price && $regular_price != $sale_price)
                <div style="font-size:12px;color:var(--ch-muted);text-decoration:line-through;">
                    {{ amount_with_currency_symbol($regular_price) }}
                </div>
                @endif
            </td>
            <td style="padding:16px 12px;text-align:center;">
                {!! theme_star_rating($product) !!}
            </td>
            <td style="padding:16px 12px;text-align:center;">
                <div style="display:inline-flex;gap:6px;align-items:center;">
                    <button class="ch-add-btn ch-atc-btn add-to-cart-btn"
                            style="width:36px;height:36px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;"
                            data-product_id="{{ $product->id }}"
                            aria-label="{{ __('Add to cart') }}">
                        <i class="las la-shopping-cart"></i>
                    </button>
                    <button class="add-to-wishlist-btn"
                            style="width:36px;height:36px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#fff;border:1px solid var(--ch-border,#e0e0e0);cursor:pointer;color:var(--ch-muted,#888);"
                            data-product_id="{{ $product->id }}"
                            aria-label="{{ __('Wishlist') }}">
                        <i class="mdi mdi-heart-outline"></i>
                    </button>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- Pagination --}}
@if(count($links) > 1)
<div style="margin-top:24px;text-align:center;">
    <div class="ch-pagination" style="display:inline-flex;gap:4px;">
        @foreach($links as $page => $url)
            <button class="ch-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    style="padding:8px 12px;border:1px solid var(--ch-border);background:#fff;border-radius:4px;font-size:13px;cursor:pointer;transition:all .2s;{{ $page == $current_page ? 'background:var(--ch-red);color:#fff;border-color:var(--ch-red);' : '' }}"
                    data-page="{{ $page }}"
                    onclick="chFilterRequest({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
