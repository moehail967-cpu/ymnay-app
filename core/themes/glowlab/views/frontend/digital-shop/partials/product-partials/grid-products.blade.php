{{-- GlowLab: Digital product grid partial --}}
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
    <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);display:flex;flex-direction:column;height:100%;transition:box-shadow .2s,transform .2s;"
         onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(184,150,90,.2)';this.style.transform='translateY(-3px)'"
         onmouseout="this.style.boxShadow='var(--gl-shadow)';this.style.transform='translateY(0)'">

        {{-- Cover image --}}
        <div style="position:relative;aspect-ratio:3/4;overflow:hidden;background:var(--gl-gold-pale);">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"
                         style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
                         onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}"
                   style="display:flex;align-items:center;justify-content:center;height:100%;font-size:52px;color:var(--gl-gold);text-decoration:none;">
                    <i class="mdi mdi-file-download-outline"></i>
                </a>
            @endif

            @if($discount > 0)
                <span style="position:absolute;top:10px;left:10px;background:var(--gl-gold);color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:50px;letter-spacing:.5px;">
                    {{ $discount }}% {{ __('OFF') }}
                </span>
            @elseif($badge)
                <span style="position:absolute;top:10px;left:10px;background:var(--gl-dark);color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:50px;letter-spacing:.5px;">
                    {{ $badge }}
                </span>
            @endif
        </div>

        {{-- Card body --}}
        <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
            @if($category)
                <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:var(--gl-gold);margin-bottom:4px;">{{ $category }}</div>
            @endif
            @if($author)
                <div style="font-size:11px;color:var(--gl-muted);margin-bottom:4px;">
                    <i class="mdi mdi-account-outline"></i> {{ $author }}
                </div>
            @endif
            <a href="{{ dynamicRoute($product->slug) }}"
               style="font-size:14px;font-weight:600;color:var(--gl-dark);text-decoration:none;line-height:1.4;margin-bottom:10px;flex:1;"
               onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='var(--gl-dark)'">
                {{ \Illuminate\Support\Str::words($product->name, 7) }}
            </a>
            <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:12px;">
                <span style="font-size:16px;font-weight:700;color:var(--gl-gold);">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span style="font-size:12px;color:var(--gl-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}"
               style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px 16px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
               onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                <i class="mdi mdi-eye-outline"></i> {{ __('View Product') }}
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
            <button class="gl-dp-page-btn"
                    data-page="{{ $page }}"
                    style="min-width:36px;height:36px;padding:0 10px;border:1.5px solid {{ $page == $current_page ? 'var(--gl-dark)' : 'var(--gl-border)' }};border-radius:var(--gl-radius);background:{{ $page == $current_page ? 'var(--gl-dark)' : '#fff' }};color:{{ $page == $current_page ? '#fff' : 'var(--gl-muted)' }};font-size:13px;font-weight:{{ $page == $current_page ? '700' : '400' }};cursor:pointer;transition:all .2s;">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
