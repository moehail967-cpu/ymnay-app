{{-- GoldCraft: Digital product grid partial --}}
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
    <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);display:flex;flex-direction:column;height:100%;transition:all .25s;"
         onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(200,169,110,.3)';this.style.transform='translateY(-3px)'"
         onmouseout="this.style.boxShadow='var(--gc-shadow)';this.style.transform='translateY(0)'">

        {{-- Cover image --}}
        <div style="position:relative;aspect-ratio:3/4;overflow:hidden;background:var(--gc-warm);">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                         style="width:100%;height:100%;object-fit:cover;transition:transform .5s;"
                         onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}"
                   style="display:flex;align-items:center;justify-content:center;height:100%;font-size:52px;color:var(--gc-rose);text-decoration:none;">
                    <i class="las la-file-download"></i>
                </a>
            @endif

            @if($discount > 0)
                <span style="position:absolute;top:10px;left:10px;background:var(--gc-rose);color:#fff;font-size:9px;font-weight:700;padding:3px 10px;border-radius:3px;letter-spacing:1px;text-transform:uppercase;">
                    {{ $discount }}% {{ __('OFF') }}
                </span>
            @elseif($badge)
                <span style="position:absolute;top:10px;left:10px;background:var(--gc-dark);color:#fff;font-size:9px;font-weight:700;padding:3px 10px;border-radius:3px;letter-spacing:1px;text-transform:uppercase;">
                    {{ $badge }}
                </span>
            @endif
        </div>

        {{-- Card body --}}
        <div style="padding:16px;flex:1;display:flex;flex-direction:column;font-family:Georgia,serif;">
            @if($category)
                <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--gc-rose);margin-bottom:4px;">{{ $category }}</div>
            @endif
            @if($author)
                <div style="font-size:11px;color:var(--gc-muted);margin-bottom:4px;font-style:italic;">
                    <i class="las la-user"></i> {{ $author }}
                </div>
            @endif
            <a href="{{ dynamicRoute($product->slug) }}"
               style="font-size:14px;color:var(--gc-dark);text-decoration:none;line-height:1.5;margin-bottom:10px;flex:1;font-style:italic;"
               onmouseover="this.style.color='var(--gc-rose)'" onmouseout="this.style.color='var(--gc-dark)'">
                {{ \Illuminate\Support\Str::words($product->name, 7) }}
            </a>
            <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px;">
                <span style="font-size:16px;color:var(--gc-rose);font-style:italic;">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span style="font-size:12px;color:var(--gc-muted);text-decoration:line-through;font-style:italic;">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}" class="gc-btn gc-btn-primary" style="font-size:11px;justify-content:center;">
                <i class="las la-eye"></i> {{ __('View Product') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12" style="margin-top:8px;">
    <div style="display:flex;gap:6px;flex-wrap:wrap;justify-content:center;">
        @foreach($links as $page => $url)
            <button class="gc-dp-page-btn"
                    data-page="{{ $page }}"
                    style="min-width:36px;height:36px;padding:0 10px;border:1.5px solid {{ $page == $current_page ? 'var(--gc-rose)' : 'var(--gc-border)' }};border-radius:var(--gc-radius);background:{{ $page == $current_page ? 'var(--gc-rose)' : '#fff' }};color:{{ $page == $current_page ? '#fff' : 'var(--gc-muted)' }};font-size:13px;cursor:pointer;transition:all .2s;font-family:Georgia,serif;">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
