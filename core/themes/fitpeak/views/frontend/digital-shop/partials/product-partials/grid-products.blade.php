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
    <div class="fp-card h-100 d-flex flex-column">
        <div class="fp-card-img">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}" class="fp-img-ph">
                    <i class="mdi mdi-file-document-outline" style="font-size:48px;color:var(--fp-green);opacity:.3;"></i>
                </a>
            @endif
            @if($discount > 0)
                <span class="fp-card-badge">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="fp-card-badge" style="background:var(--fp-green-dim);">{{ $badge }}</span>
            @endif
        </div>
        <div class="fp-card-body d-flex flex-column flex-grow-1">
            @if($author)
                <div class="fp-card-cat">{{ $author }}</div>
            @endif
            <div class="fp-card-title flex-grow-1">
                <a href="{{ dynamicRoute($product->slug) }}">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
            </div>
            <div class="fp-card-footer">
                <div>
                    <div class="fp-price">{{ amount_with_currency_symbol($price) }}</div>
                    @if($discount > 0 && $original)
                        <div class="fp-price-old">{{ amount_with_currency_symbol($original) }}</div>
                    @endif
                </div>
                <a href="{{ dynamicRoute($product->slug) }}" class="fp-atc-btn fp-btn-sm" style="gap:4px;padding:8px 12px;">
                    <i class="mdi mdi-eye-outline"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="fp-pagination">
        @foreach($links as $page => $link)
            <button class="fp-page-btn {{ $page === $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}"
                    onclick="fpDpFilter({{ $page }})">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
