{{-- TrailCo: Digital product grid partial --}}
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
    <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;height:100%;display:flex;flex-direction:column;box-shadow:var(--tr-shadow);transition:box-shadow .2s;">
        {{-- Image --}}
        <div style="position:relative;aspect-ratio:4/3;overflow:hidden;background:var(--tr-cream);">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                         style="width:100%;height:100%;object-fit:contain;transition:transform .3s;padding:8px;">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}"
                   style="display:flex;align-items:center;justify-content:center;height:100%;text-decoration:none;">
                    <i class="mdi mdi-file-download-outline" style="font-size:56px;color:var(--tr-stone);opacity:.4;"></i>
                </a>
            @endif

            @if($discount > 0)
                <span style="position:absolute;top:10px;left:10px;background:var(--tr-terra);color:#fff;font-size:11px;font-weight:800;padding:3px 10px;border-radius:20px;">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span style="position:absolute;top:10px;left:10px;background:var(--tr-olive);color:#fff;font-size:11px;font-weight:800;padding:3px 10px;border-radius:20px;">{{ $badge }}</span>
            @endif
        </div>

        {{-- Body --}}
        <div style="padding:14px;flex:1;display:flex;flex-direction:column;">
            @if($category)
                <div style="font-size:10px;font-weight:700;color:var(--tr-olive);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">{{ $category }}</div>
            @endif
            @if($author)
                <div style="font-size:11px;color:var(--tr-stone);margin-bottom:4px;">
                    <i class="mdi mdi-account-outline" style="color:var(--tr-olive);"></i> {{ $author }}
                </div>
            @endif
            <a href="{{ dynamicRoute($product->slug) }}"
               style="font-size:14px;font-weight:700;color:var(--tr-bark);text-decoration:none;line-height:1.4;margin-bottom:8px;flex:1;">
                {{ \Illuminate\Support\Str::words($product->name, 7) }}
            </a>
            <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px;">
                <span style="font-size:16px;font-weight:800;color:var(--tr-olive);">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span style="font-size:12px;color:var(--tr-stone);text-decoration:line-through;">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}"
               style="display:flex;align-items:center;justify-content:center;gap:6px;padding:8px 14px;background:var(--tr-olive);color:#fff;border-radius:var(--tr-radius);font-size:12px;font-weight:700;text-decoration:none;">
                <i class="mdi mdi-eye-outline"></i> {{ __('View Product') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;">
        @foreach($links as $page => $url)
            <button class="tr-dp-page-btn" data-page="{{ $page }}"
                    style="padding:7px 14px;border:1.5px solid {{ $page == $current_page ? 'var(--tr-olive)' : 'var(--tr-border)' }};background:{{ $page == $current_page ? 'var(--tr-olive)' : '#fff' }};color:{{ $page == $current_page ? '#fff' : 'var(--tr-bark)' }};border-radius:var(--tr-radius);font-size:13px;font-weight:700;cursor:pointer;">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
