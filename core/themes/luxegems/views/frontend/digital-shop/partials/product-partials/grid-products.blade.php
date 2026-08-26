{{-- LuxeGems: Digital product grid partial --}}
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
    <div class="lx-card h-100 d-flex flex-column" style="height:100%;display:flex;flex-direction:column;">
        <div class="lx-card-img">
            @if($img_url)
                <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
            @else
                <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:48px;color:var(--lx-gold);background:var(--lx-surface);">
                    <i class="las la-file-download"></i>
                </div>
            @endif

            @if($discount > 0)
                <span class="lx-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="lx-card-badge">{{ $badge }}</span>
            @endif

            <div class="lx-card-actions">
                <a href="{{ dynamicRoute($product->slug) }}" class="lx-card-act-btn">
                    <i class="las la-eye"></i> {{ __('View') }}
                </a>
            </div>
        </div>
        <div class="lx-card-body d-flex flex-column flex-grow-1" style="flex:1;display:flex;flex-direction:column;">
            @if($category)
                <div class="lx-card-material">{{ $category }}</div>
            @endif
            @if($author)
                <div style="font-size:11px;color:var(--lx-muted);margin-bottom:4px;">
                    <i class="las la-user"></i> {{ $author }}
                </div>
            @endif
            <div class="lx-card-name flex-grow-1" style="flex:1;">
                <a href="{{ dynamicRoute($product->slug) }}" style="color:inherit;text-decoration:none;">
                    {{ \Illuminate\Support\Str::words($product->name, 7) }}
                </a>
            </div>
            <div class="lx-card-price-row">
                <div>
                    <span class="lx-price">{{ amount_with_currency_symbol($price) }}</span>
                    @if($discount > 0 && $original)
                        <span class="lx-price-old ms-2">{{ amount_with_currency_symbol($original) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12 mt-3">
    <div class="lg-pagination">
        @foreach($links as $page => $url)
            <button class="lg-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="lxDpFilter({{ $page }})">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
