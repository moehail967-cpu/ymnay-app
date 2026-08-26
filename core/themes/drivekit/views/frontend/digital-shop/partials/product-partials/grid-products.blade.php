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
@endphp
<div class="col-6 col-md-4 col-lg-3">
    <div class="dk-card h-100 d-flex flex-column">
        <div class="dk-card-img">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}" class="dk-card-no-img">
                    <i class="mdi mdi-download-circle-outline"></i>
                </a>
            @endif
            @if($discount > 0)
                <span class="dk-card-badge dk-badge-sale">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="dk-card-badge dk-badge-hot">{{ $badge }}</span>
            @endif
        </div>
        <div class="dk-card-body d-flex flex-column flex-grow-1">
            @if($author)
                <div class="dk-card-cat">{{ $author }}</div>
            @endif
            <a href="{{ dynamicRoute($product->slug) }}" class="dk-card-name flex-grow-1">
                {{ \Illuminate\Support\Str::words($product->name, 6) }}
            </a>
            <div class="dk-card-price mt-auto">
                <span class="dk-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span class="dk-price-orig">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}" class="dk-card-atc">
                <i class="mdi mdi-eye"></i> {{ __('View Product') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="dk-pagination">
        <ul class="dk-page-list">
            @foreach($links as $page => $link)
                <li>
                    <a href="{{ $link }}" class="dk-page-btn {{ $page === $current_page ? 'active' : '' }}"
                       data-page="{{ $page }}">{{ $page }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endif
