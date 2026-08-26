{{-- VelvetLux: Digital product grid partial --}}
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
    <div style="background:var(--vl-card);border:1px solid var(--vl-border);overflow:hidden;transition:border-color .3s;height:100%;display:flex;flex-direction:column;"
         onmouseover="this.style.borderColor='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)'">

        {{-- Image --}}
        <div style="position:relative;overflow:hidden;background:var(--vl-surface);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                         style="width:100%;height:100%;object-fit:contain;">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}"
                   style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
                    <i class="las la-file-download" style="font-size:48px;color:var(--vl-champagne);opacity:.4;"></i>
                </a>
            @endif

            @if($discount > 0)
                <span style="position:absolute;top:10px;left:10px;background:var(--vl-champagne);color:var(--vl-dark);font-size:9px;letter-spacing:1px;padding:3px 8px;">{{ $discount }}% {{ __('off') }}</span>
            @elseif($badge)
                <span style="position:absolute;top:10px;left:10px;background:var(--vl-plum);color:var(--vl-champagne);font-size:9px;letter-spacing:1px;padding:3px 8px;">{{ $badge }}</span>
            @endif
        </div>

        {{-- Body --}}
        <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
            @if($category)
                <div style="font-size:10px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:4px;">{{ $category }}</div>
            @endif
            @if($author)
                <div style="font-size:11px;color:var(--vl-muted);margin-bottom:6px;">
                    <i class="las la-user" style="color:var(--vl-champagne);"></i> {{ $author }}
                </div>
            @endif
            <a href="{{ dynamicRoute($product->slug) }}"
               style="font-size:14px;color:var(--vl-ivory);text-decoration:none;line-height:1.4;letter-spacing:.2px;display:block;margin-bottom:10px;flex:1;">
                {{ \Illuminate\Support\Str::words($product->name, 7) }}
            </a>
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:auto;">
                <div>
                    <span style="font-size:14px;color:var(--vl-champagne);font-family:'Inter',sans-serif;">{{ amount_with_currency_symbol($price) }}</span>
                    @if($discount > 0 && $original)
                        <span style="font-size:11px;color:var(--vl-muted);text-decoration:line-through;margin-left:4px;">{{ amount_with_currency_symbol($original) }}</span>
                    @endif
                </div>
                <a href="{{ dynamicRoute($product->slug) }}"
                   style="display:inline-flex;align-items:center;gap:4px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);text-decoration:none;padding:6px 10px;border:1px solid var(--vl-champagne);transition:all .2s;"
                   onmouseover="this.style.background='var(--vl-champagne)';this.style.color='var(--vl-dark)'" onmouseout="this.style.background='transparent';this.style.color='var(--vl-champagne)'">
                    <i class="las la-eye"></i> {{ __('View') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div class="vl-pagination">
        @foreach($links as $page => $url)
            <button class="vl-page-btn vl-dp-page-btn {{ $page == $current_page ? 'active' : '' }}"
                    data-page="{{ $page }}">{{ $page }}</button>
        @endforeach
    </div>
</div>
@endif
