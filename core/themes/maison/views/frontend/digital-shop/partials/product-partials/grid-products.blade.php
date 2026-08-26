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
    <div style="background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;box-shadow:var(--ms-shadow);transition:box-shadow .25s,transform .25s;height:100%;display:flex;flex-direction:column;"
         onmouseover="this.style.boxShadow='0 18px 50px -10px rgba(200,184,154,.4)';this.style.transform='translateY(-3px)'"
         onmouseout="this.style.boxShadow='var(--ms-shadow)';this.style.transform='none'">

        {{-- Image --}}
        <div style="aspect-ratio:4/3;overflow:hidden;background:var(--ms-warm);position:relative;flex-shrink:0;">
            @if($img_url)
                <a href="{{ dynamicRoute($product->slug) }}">
                    <img src="{{ $img_url }}" alt="{{ $product->name }}"
                         style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .4s;"
                         onmouseover="this.style.transform='scale(1.05)'"
                         onmouseout="this.style.transform='scale(1)'">
                </a>
            @else
                <a href="{{ dynamicRoute($product->slug) }}"
                   style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:var(--ms-border);">
                    <i class="mdi mdi-file-download-outline" style="font-size:48px;"></i>
                </a>
            @endif
            @if($discount > 0)
                <span style="position:absolute;top:10px;left:10px;background:var(--ms-olive);color:#fff;font-size:10px;font-weight:700;padding:3px 9px;border-radius:2px;letter-spacing:.05em;">
                    {{ $discount }}% {{ __('off') }}
                </span>
            @elseif($badge)
                <span style="position:absolute;top:10px;left:10px;background:var(--ms-linen);color:#fff;font-size:10px;font-weight:700;padding:3px 9px;border-radius:2px;letter-spacing:.05em;">
                    {{ $badge }}
                </span>
            @endif
        </div>

        {{-- Body --}}
        <div style="padding:16px;flex:1;display:flex;flex-direction:column;">
            @if($author)
                <div style="font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:6px;">{{ $author }}</div>
            @endif
            <h3 style="font-size:14px;font-weight:500;color:var(--ms-dark);margin-bottom:10px;line-height:1.4;flex:1;">
                <a href="{{ dynamicRoute($product->slug) }}"
                   style="color:inherit;text-decoration:none;transition:color .2s;"
                   onmouseover="this.style.color='var(--ms-linen-d)'"
                   onmouseout="this.style.color='var(--ms-dark)'">
                    {{ \Illuminate\Support\Str::words($product->name, 8) }}
                </a>
            </h3>
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                <span style="font-size:15px;font-weight:600;color:var(--ms-dark);">{{ amount_with_currency_symbol($price) }}</span>
                @if($discount > 0 && $original)
                    <span style="font-size:12px;color:var(--ms-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($original) }}</span>
                @endif
            </div>
            <a href="{{ dynamicRoute($product->slug) }}"
               style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;background:var(--ms-warm);border:1px solid var(--ms-border);border-radius:var(--ms-radius);font-size:12px;font-weight:600;color:var(--ms-charcoal);text-decoration:none;letter-spacing:.04em;transition:all .2s;"
               onmouseover="this.style.background='var(--ms-dark)';this.style.color='#fff';this.style.borderColor='var(--ms-dark)'"
               onmouseout="this.style.background='var(--ms-warm)';this.style.color='var(--ms-charcoal)';this.style.borderColor='var(--ms-border)'">
                <i class="mdi mdi-eye-outline"></i> {{ __('View') }}
            </a>
        </div>
    </div>
</div>
@endforeach

{{-- Pagination --}}
@if(count($links) > 1)
<div class="col-12">
    <div style="display:flex;flex-wrap:wrap;gap:6px;align-items:center;justify-content:center;margin-top:12px;">
        @foreach($links as $page => $url)
            <button onclick="msDpFilter({{ $page }})"
                    class="ms-page-btn {{ $page === $current_page ? 'active' : '' }}">
                {{ $page }}
            </button>
        @endforeach
    </div>
</div>
@endif
