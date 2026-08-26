{{-- FreshMart: Digital product grid partial --}}
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
    <div class="fm-dp-card h-100 d-flex flex-column">
        <div class="fm-dp-card-img">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}" class="fm-dp-no-img">
                    <i class="las la-file-download"></i>
                </a>
            @endif

            @if($discount > 0)
                <span class="fm-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="fm-card-badge">{{ $badge }}</span>
            @endif
        </div>

        <div class="fm-dp-card-body d-flex flex-column flex-grow-1">
            @if($category)
                <div class="fm-dp-cat">{{ $category }}</div>
            @endif
            @if($author)
                <div style="font-size:11px;color:var(--fm-muted);margin-bottom:4px;">
                    <i class="las la-user" style="color:var(--fm-green);"></i> {{ $author }}
                </div>
            @endif
            <a href="{{ dynamicRoute($product->slug) }}" class="fm-dp-name">
                {{ \Illuminate\Support\Str::words($product->name, 7) }}
            </a>
            <div class="fm-card-price mt-2">
                <span class="fm-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span class="fm-price-orig">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}" class="fm-btn fm-btn-green fm-btn-sm w-100 justify-content-center mt-2">
                <i class="las la-eye"></i> {{ __('View Product') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="fm-pagination">
        @foreach($links as $page => $url)
            <button class="fm-page-btn fm-dp-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
