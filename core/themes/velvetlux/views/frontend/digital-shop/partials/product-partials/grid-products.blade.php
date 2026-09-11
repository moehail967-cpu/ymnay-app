{{-- VelvetLux: Digital product grid partial --}}
@foreach($products as $product)
@php
    $dp       = get_digital_product_dynamic_price($product);
    $sale     = $dp['sale_price'];
    $original = $dp['regular_price'];
    $discount = $dp['discount'];
    $price    = $sale > 0 ? $sale : $original;
    $img      = get_attachment_image_by_id($product->image_id ?? null, 'grid');
    $img_url  = $img['img_url'] ?? null;
    $badge    = $product->additionalFields?->badge?->name ?? null;
    $author   = $product->additionalFields?->author?->name ?? null;
    $category = $product->category?->name ?? null;
@endphp
<div class="col-6 col-md-4">
    <div class="vl-card h-100 d-flex flex-column" style="height:100%;display:flex;flex-direction:column;">
        <div class="vl-card-img" style="aspect-ratio:4/3;">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}"
                   style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;text-decoration:none;">
                    <i class="mdi mdi-file-download-outline" style="font-size:56px;color:var(--vl-muted);opacity:.4;"></i>
                </a>
            @endif

            @if($discount > 0)
                <span class="vl-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="vl-card-badge">{{ $badge }}</span>
            @endif

            <div class="vl-card-overlay">
                <a href="{{ dynamicRoute($product->slug) }}" class="vl-card-act-btn">
                    <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
                </a>
            </div>
        </div>

        <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
            @if($category)
                <div style="font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:4px;font-family:'Inter',sans-serif;">{{ $category }}</div>
            @endif
            @if($author)
                <div style="font-size:11px;color:var(--vl-muted);margin-bottom:6px;">
                    <i class="mdi mdi-account-outline"></i> {{ $author }}
                </div>
            @endif
            <a href="{{ dynamicRoute($product->slug) }}"
               style="font-size:14px;font-weight:300;color:var(--vl-ivory);text-decoration:none;font-style:italic;line-height:1.4;margin-bottom:10px;flex:1;">
                {{ \Illuminate\Support\Str::words($product->name, 7) }}
            </a>
            <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px;">
                <span style="font-size:15px;color:var(--vl-champagne);font-family:'Inter',sans-serif;">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span style="font-size:12px;color:var(--vl-muted);text-decoration:line-through;font-family:'Inter',sans-serif;">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}" class="vl-btn vl-btn-outline"
               style="font-size:9px;padding:10px 16px;justify-content:center;">
                {{ __('View Product') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="vl-pagination">
        @foreach($links as $page => $url)
            <button class="vl-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="vlDpFilter({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
