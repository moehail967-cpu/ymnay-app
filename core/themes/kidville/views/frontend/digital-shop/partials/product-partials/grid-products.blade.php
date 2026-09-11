{{-- KidVille: Digital product grid partial --}}
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
<div class="col-6 col-md-3">
    <div class="kv-card h-100 d-flex flex-column" style="display:flex;flex-direction:column;height:100%;">
        <div class="kv-card-img" style="aspect-ratio:3/4;position:relative;">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                         style="width:100%;height:100%;object-fit:cover;">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}" class="kv-img-ph">
                    <i class="las la-file-download" style="font-size:52px;color:var(--kv-red);"></i>
                </a>
            @endif

            @if($discount > 0)
                <span class="kv-card-badge kv-badge-sale">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="kv-card-badge kv-badge-hot">{{ $badge }}</span>
            @endif
        </div>

        <div class="kv-card-body d-flex flex-column flex-grow-1" style="flex:1;display:flex;flex-direction:column;">
            @if($category)
                <div style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--kv-red);margin-bottom:2px;">{{ $category }}</div>
            @endif
            @if($author)
                <div style="font-size:11px;color:var(--kv-muted);margin-bottom:4px;">
                    <i class="las la-user"></i> {{ $author }}
                </div>
            @endif
            <div class="kv-card-title flex-grow-1" style="flex:1;">
                <a href="{{ dynamicRoute($product->slug) }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
            </div>
            <div class="kv-card-footer">
                <div>
                    <div class="kv-price">{{ amount_with_currency_symbol($price) }}</div>
                    @if($discount > 0 && $original)
                        <div class="kv-price-old">{{ amount_with_currency_symbol($original) }}</div>
                    @endif
                </div>
                <a href="{{ dynamicRoute($product->slug) }}" class="kv-add-btn" aria-label="{{ __('View') }}">
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
    <div class="kv-pagination">
        @foreach($links as $page => $url)
            <button class="kv-page-btn kv-dp-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
