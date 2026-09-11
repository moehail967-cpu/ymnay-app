@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare') }} @endsection

@section('content')

{{-- Page Banner --}}
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:44px 0 32px;text-align:center;">
    <div class="container">
        <div style="font-size:10px;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:8px;">{{ __('Product') }}</div>
        <h1 style="font-size:clamp(22px,4vw,36px);font-weight:300;color:var(--ms-dark);margin:0 auto 12px;line-height:1.2;">{{ __('Compare Products') }}</h1>
        <div style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:12px;color:var(--ms-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;"
               onmouseover="this.style.color='var(--ms-olive)'" onmouseout="this.style.color='var(--ms-linen-d)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('Compare') }}</span>
        </div>
    </div>
</div>

<section style="background:var(--ms-cream);padding:48px 0 72px;">
<div class="container">

@php
    $compare_items    = \Gloudemans\Shoppingcart\Facades\Cart::instance("compare")->content();
    $compare_products = [];
    $compare_row_ids  = [];
    foreach ($compare_items as $item) {
        $p = \Modules\Product\Entities\Product::with(['inventory', 'category'])->find($item->id);
        if ($p) {
            $compare_products[]      = $p;
            $compare_row_ids[$p->id] = $item->rowId;
        }
    }
@endphp

@if(count($compare_products) > 0)

<div class="table-responsive">
<table style="width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--ms-border);border-radius:var(--ms-radius);overflow:hidden;box-shadow:var(--ms-shadow);">

    {{-- Product header row --}}
    <thead>
        <tr style="border-bottom:1px solid var(--ms-border);">
            <th style="padding:16px 20px;text-align:left;font-size:10px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--ms-muted);background:var(--ms-warm);width:160px;">
                {{ __('Product') }}
            </th>
            @foreach($compare_products as $product)
            <th style="padding:20px;text-align:center;border-left:1px solid var(--ms-border);background:#fff;vertical-align:top;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                    @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                    <div style="width:100px;height:100px;border-radius:var(--ms-radius);overflow:hidden;border:1px solid var(--ms-border);background:var(--ms-warm);">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--ms-muted);"><i class="mdi mdi-home-outline"></i></div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}"
                       style="font-size:13px;font-weight:500;color:var(--ms-dark);text-decoration:none;line-height:1.3;transition:color .2s;"
                       onmouseover="this.style.color='var(--ms-linen-d)'"
                       onmouseout="this.style.color='var(--ms-dark)'">
                        {{ \Illuminate\Support\Str::words($product->name, 6) }}
                    </a>
                    <button class="compare-remove-btn"
                            data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}"
                            style="background:transparent;border:1px solid var(--ms-border);color:var(--ms-muted);border-radius:var(--ms-radius);padding:5px 12px;font-size:11px;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:4px;"
                            onmouseover="this.style.borderColor='#e57373';this.style.color='#e57373'"
                            onmouseout="this.style.borderColor='var(--ms-border)';this.style.color='var(--ms-muted)'">
                        <i class="mdi mdi-close"></i> {{ __('Remove') }}
                    </button>
                </div>
            </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        {{-- Price --}}
        <tr style="border-bottom:1px solid var(--ms-border);">
            <td style="padding:14px 20px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);background:var(--ms-warm);">{{ __('Price') }}</td>
            @foreach($compare_products as $product)
            @php $pdata = theme_product_price($product); @endphp
            <td style="padding:14px 20px;text-align:center;border-left:1px solid var(--ms-border);">
                <span style="font-size:16px;font-weight:600;color:var(--ms-dark);">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                @if($pdata['regular_price'])
                    <span style="display:block;font-size:12px;color:var(--ms-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Rating --}}
        <tr style="border-bottom:1px solid var(--ms-border);">
            <td style="padding:14px 20px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);background:var(--ms-warm);">{{ __('Rating') }}</td>
            @foreach($compare_products as $product)
            <td style="padding:14px 20px;text-align:center;border-left:1px solid var(--ms-border);">{!! theme_star_rating($product) !!}</td>
            @endforeach
        </tr>

        {{-- SKU --}}
        <tr style="border-bottom:1px solid var(--ms-border);">
            <td style="padding:14px 20px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);background:var(--ms-warm);">{{ __('SKU') }}</td>
            @foreach($compare_products as $product)
            <td style="padding:14px 20px;text-align:center;border-left:1px solid var(--ms-border);font-size:13px;color:var(--ms-charcoal);">{{ $product->sku ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Category --}}
        <tr style="border-bottom:1px solid var(--ms-border);">
            <td style="padding:14px 20px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);background:var(--ms-warm);">{{ __('Category') }}</td>
            @foreach($compare_products as $product)
            <td style="padding:14px 20px;text-align:center;border-left:1px solid var(--ms-border);font-size:13px;color:var(--ms-charcoal);">{{ $product->category?->name ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Availability --}}
        <tr style="border-bottom:1px solid var(--ms-border);">
            <td style="padding:14px 20px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);background:var(--ms-warm);">{{ __('Availability') }}</td>
            @foreach($compare_products as $product)
            @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
            <td style="padding:14px 20px;text-align:center;border-left:1px solid var(--ms-border);font-size:13px;">
                @if($in_stock)
                    <span style="color:#2e7d32;font-weight:600;"><i class="mdi mdi-check-circle"></i> {{ __('In Stock') }}</span>
                @else
                    <span style="color:#c62828;font-weight:600;"><i class="mdi mdi-close-circle"></i> {{ __('Out of Stock') }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Action --}}
        <tr>
            <td style="padding:14px 20px;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--ms-muted);background:var(--ms-warm);">{{ __('Action') }}</td>
            @foreach($compare_products as $product)
            <td style="padding:14px 20px;text-align:center;border-left:1px solid var(--ms-border);">
                <button class="add-to-cart-btn ms-btn ms-btn-dark ms-btn-sm"
                        data-product_id="{{ $product->id }}">
                    <i class="mdi mdi-cart-plus"></i>
                    {{ __('Add to Cart') }}
                </button>
            </td>
            @endforeach
        </tr>
    </tbody>
</table>
</div>

<div style="margin-top:28px;display:flex;gap:12px;flex-wrap:wrap;">
    <a href="{{ theme_shop_url() }}" class="ms-btn ms-btn-border">
        <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
    </a>
</div>

@else

{{-- Empty state --}}
<div style="text-align:center;padding:80px 0;">
    <div style="width:80px;height:80px;border-radius:50%;background:#fff;border:1px solid var(--ms-border);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;box-shadow:var(--ms-shadow);">
        <i class="mdi mdi-compare-horizontal" style="font-size:36px;color:var(--ms-border);"></i>
    </div>
    <h3 style="font-size:20px;font-weight:300;color:var(--ms-dark);margin-bottom:10px;">{{ __('No Products to Compare') }}</h3>
    <p style="color:var(--ms-muted);font-size:14px;margin-bottom:24px;max-width:380px;margin-left:auto;margin-right:auto;line-height:1.7;">
        {{ __('Add products to your compare list by clicking the compare button on any product card.') }}
    </p>
    <a href="{{ theme_shop_url() }}" class="ms-btn ms-btn-dark">
        <i class="mdi mdi-storefront-outline"></i> {{ __('Browse Products') }}
    </a>
</div>

@endif

</div>
</section>

@endsection

@section('scripts')
<script>
(function($){
    'use strict';
    $(document).on('click', '.compare-remove-btn', function(){
        var rowId = $(this).data('product_id');
        var btn = $(this);
        btn.prop('disabled', true).css('opacity', '.5');
        $.ajax({
            url: '{{ theme_compare_remove_url() }}',
            type: 'GET',
            data: { product_id: rowId },
            success: function(){
                var ids = (sessionStorage.getItem('products') || '').split(',').filter(function(v){ return v && v !== String(rowId); });
                sessionStorage.setItem('products', ids.join(','));
                if (!ids.length) sessionStorage.removeItem('products');
                location.reload();
            },
            error: function(){ btn.prop('disabled', false).css('opacity', '1'); }
        });
    });
})(jQuery);
</script>
@endsection
