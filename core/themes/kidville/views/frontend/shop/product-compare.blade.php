@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} — {{ theme_site_name() }} @endsection

@section('content')

<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('Compare Products') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Compare') }}</span>
        </div>
    </div>
</div>

<div style="padding:48px 0 80px;">
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
<table style="width:100%;border-collapse:collapse;background:#fff;border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;box-shadow:var(--kv-shadow);">
    <thead>
        <tr>
            <td style="padding:20px 24px;background:var(--kv-light);border-bottom:2px solid var(--kv-border);width:160px;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--kv-muted);"></td>
            @foreach($compare_products as $product)
            <th style="padding:20px 24px;background:var(--kv-light);border-bottom:2px solid var(--kv-border);border-left:2px solid var(--kv-border);text-align:center;font-weight:700;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                    @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                    <div style="width:100px;height:100px;border-radius:var(--kv-radius);overflow:hidden;background:var(--kv-bg);border:2px solid var(--kv-border);">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--kv-muted);"><i class="las la-child"></i></div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}" style="font-size:13px;font-weight:700;color:var(--kv-dark);text-decoration:none;line-height:1.4;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                    <button class="compare-remove-btn" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 14px;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);background:transparent;color:var(--kv-muted);font-size:11px;font-weight:700;cursor:pointer;transition:all .2s;"
                            onmouseover="this.style.borderColor='var(--kv-red)';this.style.color='var(--kv-red)'" onmouseout="this.style.borderColor='var(--kv-border)';this.style.color='var(--kv-muted)'">
                        <i class="las la-times"></i> {{ __('Remove') }}
                    </button>
                </div>
            </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @php $row_style = 'padding:16px 24px;border-bottom:2px solid var(--kv-border);font-size:13px;color:var(--kv-dark);'; @endphp

        {{-- Price --}}
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:800;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);background:var(--kv-light);">{{ __('Price') }}</td>
            @foreach($compare_products as $product)
            @php $pdata = theme_product_price($product); @endphp
            <td style="{{ $row_style }}border-left:2px solid var(--kv-border);text-align:center;">
                <span style="font-size:20px;font-weight:800;color:var(--kv-red);">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                @if($pdata['regular_price'])
                    <span style="display:block;font-size:12px;color:var(--kv-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Rating --}}
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:800;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);background:var(--kv-light);">{{ __('Rating') }}</td>
            @foreach($compare_products as $product)
            <td style="{{ $row_style }}border-left:2px solid var(--kv-border);text-align:center;">{!! theme_star_rating($product) !!}</td>
            @endforeach
        </tr>

        {{-- SKU --}}
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:800;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);background:var(--kv-light);">{{ __('SKU') }}</td>
            @foreach($compare_products as $product)
            <td style="{{ $row_style }}border-left:2px solid var(--kv-border);text-align:center;color:var(--kv-muted);">{{ $product->sku ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Category --}}
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:800;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);background:var(--kv-light);">{{ __('Category') }}</td>
            @foreach($compare_products as $product)
            <td style="{{ $row_style }}border-left:2px solid var(--kv-border);text-align:center;color:var(--kv-muted);">{{ $product->category?->name ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Availability --}}
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:800;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);background:var(--kv-light);">{{ __('Availability') }}</td>
            @foreach($compare_products as $product)
            @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
            <td style="{{ $row_style }}border-left:2px solid var(--kv-border);text-align:center;">
                @if($in_stock)
                    <span style="color:#2e7d32;font-weight:700;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                @else
                    <span style="color:#c62828;font-weight:700;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Action --}}
        <tr>
            <td style="{{ $row_style }}font-size:11px;font-weight:800;letter-spacing:.5px;text-transform:uppercase;color:var(--kv-muted);background:var(--kv-light);">{{ __('Action') }}</td>
            @foreach($compare_products as $product)
            <td style="{{ $row_style }}border-left:2px solid var(--kv-border);text-align:center;">
                <button class="add-to-cart-btn kv-btn kv-btn-red kv-btn-sm" data-product_id="{{ $product->id }}">
                    <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
                </button>
            </td>
            @endforeach
        </tr>
    </tbody>
</table>
</div>

<div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
    <a href="{{ theme_shop_url() }}" class="kv-btn kv-btn-outline">
        <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
    </a>
</div>

@else

<div style="text-align:center;padding:80px 0;">
    <div style="font-size:64px;margin-bottom:20px;color:var(--kv-muted);"><i class="las la-balance-scale"></i></div>
    <h3 style="font-size:24px;font-weight:800;color:var(--kv-dark);margin-bottom:10px;">{{ __('No Products to Compare') }}</h3>
    <p style="font-size:14px;color:var(--kv-muted);margin-bottom:32px;">{{ __('Add products to compare by clicking the compare button on any product.') }}</p>
    <a href="{{ theme_shop_url() }}" class="kv-btn kv-btn-red">
        <i class="las la-store"></i> {{ __('Browse Products') }}
    </a>
</div>

@endif

</div>
</div>

@endsection

@section('scripts')
<script>
(function ($) {
    'use strict';
    $(document).on('click', '.compare-remove-btn', function () {
        var rowId = $(this).data('product_id');
        var btn = $(this);
        btn.prop('disabled', true).css('opacity', '.5');
        $.ajax({
            url: '{{ theme_compare_remove_url() }}',
            type: 'GET',
            data: { product_id: rowId },
            success: function () {
                var ids = (sessionStorage.getItem('products') || '').split(',').filter(function (v) { return v && v !== String(rowId); });
                sessionStorage.setItem('products', ids.join(','));
                if (!ids.length) sessionStorage.removeItem('products');
                location.reload();
            },
            error: function () { btn.prop('disabled', false).css('opacity', '1'); }
        });
    });
})(jQuery);
</script>
@endsection
