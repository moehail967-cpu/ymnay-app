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
<div class="col-6 col-md-4">
    <div class="bk-card h-100 d-flex flex-column">
        <div class="bk-card-img" style="height:220px;">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}" class="bk-placeholder" style="height:220px;font-size:52px;">
                    <i class="mdi mdi-file-document-outline"></i>
                </a>
            @endif
            @if($discount > 0)
                <span class="bk-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="bk-card-badge">{{ $badge }}</span>
            @endif
        </div>
        <div class="bk-card-body d-flex flex-column flex-grow-1">
            @if($author)
                <div class="bk-card-cat">{{ $author }}</div>
            @endif
            <div class="bk-card-name flex-grow-1">
                <a href="{{ dynamicRoute($product->slug) }}" style="color:inherit;">
                    {{ \Illuminate\Support\Str::words($product->name, 6) }}
                </a>
            </div>
            <div class="bk-card-price mt-auto">
                <span class="bk-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span class="bk-price-orig">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}" class="bk-card-atc">
                <i class="mdi mdi-eye-outline"></i> {{ __('View Product') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="bk-pagination">
        @foreach($links as $page => $link)
            <a href="{{ $link }}" class="bk-page-btn {{ $page === $current_page ? 'active' : '' }}"
               data-page="{{ $page }}">{{ $page }}</a>
        @endforeach
    </div>
</div>
@endif
