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
    <div class="ch-card h-100 d-flex flex-column">
        <div class="ch-card-img" style="height:220px;">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}" class="ch-img-ph" style="height:220px;font-size:52px;">
                    <i class="las la-file-alt"></i>
                </a>
            @endif
            @if($discount > 0)
                <span class="ch-card-badge ch-badge-sale">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="ch-card-badge ch-badge-hot">{{ $badge }}</span>
            @endif
        </div>
        <div class="ch-card-body d-flex flex-column flex-grow-1">
            @if($author)
                <div style="font-size:11px;color:var(--ch-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">{{ $author }}</div>
            @endif
            <div class="ch-card-title flex-grow-1">
                <a href="{{ dynamicRoute($product->slug) }}">
                    {{ \Illuminate\Support\Str::words($product->name, 6) }}
                </a>
            </div>
            <div class="ch-card-footer">
                <div>
                    <div class="ch-price">{{ amount_with_currency_symbol($price) }}</div>
                    @if($discount > 0 && $original)
                        <div class="ch-price-old">{{ amount_with_currency_symbol($original) }}</div>
                    @endif
                </div>
                <a href="{{ dynamicRoute($product->slug) }}" class="ch-add-btn" style="font-size:11px;padding:6px 10px;">
                    <i class="las la-eye"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="ch-pagination">
        @foreach($links as $page => $link)
            <a href="{{ $link }}" class="ch-page-btn {{ $page === $current_page ? 'active' : '' }}"
               data-page="{{ $page }}">{{ $page }}</a>
        @endforeach
    </div>
</div>
@endif
