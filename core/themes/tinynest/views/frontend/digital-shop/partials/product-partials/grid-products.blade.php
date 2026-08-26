@if(empty($products))
<div class="col-12">
    <div class="tn-dp-empty">
        <div class="tn-dp-empty-icon"><i class="las la-baby"></i></div>
        <h4 class="tn-dp-empty-title">{{ __('No Products Found') }}</h4>
        <p class="tn-dp-empty-sub">{{ __('Try adjusting your filters or search term.') }}</p>
    </div>
</div>
@else
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
    <div class="tn-dp-card h-100 d-flex flex-column">
        <div class="tn-dp-cover">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}" class="tn-dp-cover-link">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}" class="tn-dp-cover-placeholder">
                    <i class="las la-file-download"></i>
                </a>
            @endif

            @if($discount > 0)
                <span class="tn-dp-discount-badge">{{ $discount }}% {{ __('OFF') }}</span>
            @elseif($badge)
                <span class="tn-dp-name-badge">{{ $badge }}</span>
            @endif
        </div>

        <div class="tn-dp-body">
            @if($category)
                <div class="tn-dp-cat-label">{{ $category }}</div>
            @endif
            @if($author)
                <div class="tn-dp-author"><i class="las la-user"></i> {{ $author }}</div>
            @endif
            <a href="{{ dynamicRoute($product->slug) }}" class="tn-dp-name">
                {{ \Illuminate\Support\Str::words($product->name, 7) }}
            </a>
            <div class="tn-dp-price-row">
                <span class="tn-dp-price">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span class="tn-dp-price-old">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}" class="tn-dp-view-btn">
                <i class="las la-eye"></i> {{ __('View Product') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="tn-dp-pagination">
        @foreach($links as $page => $url)
            <button class="tn-dp-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    onclick="tnDpFilter({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
@endif
