{{-- SportZone: Digital product grid partial --}}
@foreach($products as $product)
@php
    $dp           = get_digital_product_dynamic_price($product);
    $sale_price   = $dp['sale_price'];
    $regular_price = $dp['regular_price'];
    $discount     = $dp['discount'];
    $price        = $sale_price > 0 ? $sale_price : $regular_price;
    $img_url      = theme_product_image($product->image_id ?? null, 'grid');
    $product_url  = theme_product_url($product->slug);
    $badge        = $product->additionalFields?->badge?->name ?? null;
@endphp
<div class="col-6 col-md-4">
    <div class="sz-card h-100 d-flex flex-column" style="height:100%;display:flex;flex-direction:column;">
        {{-- Image --}}
        <div class="sz-card-img" style="height:200px;flex-shrink:0;position:relative;">
            @if($img_url)
                <a href="{{ $product_url }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                         style="width:100%;height:200px;object-fit:cover;display:block;">
                </a>
            @else
                <a href="{{ $product_url }}" class="sz-img-ph" style="height:200px;display:flex;align-items:center;justify-content:center;">
                    <i class="mdi mdi-file-download-outline" style="font-size:56px;color:var(--sz-red);opacity:.3;"></i>
                </a>
            @endif

            {{-- Discount / badge --}}
            @if($discount > 0)
                <span class="sz-card-badge sz-badge-sale">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span class="sz-card-badge" style="background:var(--sz-navy);">{{ $badge }}</span>
            @endif
        </div>

        {{-- Body --}}
        <div class="sz-card-body d-flex flex-column flex-grow-1" style="flex:1;display:flex;flex-direction:column;">
            @if($product->category)
                <div class="sz-card-cat">{{ $product->category->name }}</div>
            @endif
            <div class="sz-card-name flex-grow-1" style="flex:1;">
                <a href="{{ $product_url }}">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
            </div>
            <div class="sz-stars mt-auto" style="margin-bottom:6px;">{!! theme_star_rating($product) !!}</div>
            <div class="sz-card-price" style="margin-bottom:12px;">
                <span class="sz-price-sale">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $regular_price)
                    <span class="sz-price-orig">{{ amount_with_currency_symbol($regular_price) }}</span>
                @endif
            </div>
            <a href="{{ $product_url }}"
               class="sz-btn sz-btn-navy sz-btn-sm w-100"
               style="display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;">
                <i class="mdi mdi-eye"></i> {{ __('View') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;gap:6px;justify-content:center;margin-top:16px;flex-wrap:wrap;">
        @foreach($links as $page => $url)
            <button class="sz-btn sz-btn-sm {{ $page == $current_page ? 'sz-btn-red' : 'sz-btn-outline' }}"
                    style="min-width:36px;"
                    onclick="szDpFilter({{ $page }})">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
