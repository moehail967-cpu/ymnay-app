@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} — {{ theme_site_name() }} @endsection

@section('content')

<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ __('Compare Products') }}</h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <span>{{ __('Compare') }}</span>
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
<table style="width:100%;border-collapse:collapse;background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);">
    <thead>
        <tr>
            <td style="padding:20px 24px;background:var(--gc-warm);border-bottom:1px solid var(--gc-border);width:160px;font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-muted);"></td>
            @foreach($compare_products as $product)
            <th style="padding:20px 24px;background:var(--gc-warm);border-bottom:1px solid var(--gc-border);border-left:1px solid var(--gc-border);text-align:center;font-weight:400;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                    @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                    <div style="width:100px;height:100px;border-radius:var(--gc-radius);overflow:hidden;background:var(--gc-warm);border:1px solid var(--gc-border);">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:36px;color:var(--gc-muted);"><i class="las la-ring"></i></div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}" style="font-size:13px;font-weight:400;color:var(--gc-dark);text-decoration:none;line-height:1.5;font-family:Georgia,serif;font-style:italic;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                    <button class="compare-remove-btn" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 14px;border:1px solid var(--gc-border);border-radius:50px;background:transparent;color:var(--gc-muted);font-size:11px;cursor:pointer;transition:all .2s;font-family:Georgia,serif;"
                            onmouseover="this.style.borderColor='#e53e3e';this.style.color='#e53e3e'" onmouseout="this.style.borderColor='var(--gc-border)';this.style.color='var(--gc-muted)'">
                        <i class="las la-times"></i> {{ __('Remove') }}
                    </button>
                </div>
            </th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @php $row_style = 'padding:16px 24px;border-bottom:1px solid var(--gc-border);font-size:13px;color:var(--gc-dark);font-family:Georgia,serif;'; @endphp

        {{-- Price --}}
        <tr>
            <td style="{{ $row_style }}font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);background:var(--gc-warm);">{{ __('Price') }}</td>
            @foreach($compare_products as $product)
            @php $pdata = theme_product_price($product); @endphp
            <td style="{{ $row_style }}border-left:1px solid var(--gc-border);text-align:center;">
                <span style="font-size:18px;font-weight:400;color:var(--gc-rose);font-style:italic;">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                @if($pdata['regular_price'])
                    <span style="display:block;font-size:12px;color:var(--gc-muted);text-decoration:line-through;font-style:italic;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Rating --}}
        <tr>
            <td style="{{ $row_style }}font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);background:var(--gc-warm);">{{ __('Rating') }}</td>
            @foreach($compare_products as $product)
            <td style="{{ $row_style }}border-left:1px solid var(--gc-border);text-align:center;">{!! theme_star_rating($product) !!}</td>
            @endforeach
        </tr>

        {{-- SKU --}}
        <tr>
            <td style="{{ $row_style }}font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);background:var(--gc-warm);">{{ __('SKU') }}</td>
            @foreach($compare_products as $product)
            <td style="{{ $row_style }}border-left:1px solid var(--gc-border);text-align:center;color:var(--gc-muted);font-style:italic;">{{ $product->sku ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Category --}}
        <tr>
            <td style="{{ $row_style }}font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);background:var(--gc-warm);">{{ __('Category') }}</td>
            @foreach($compare_products as $product)
            <td style="{{ $row_style }}border-left:1px solid var(--gc-border);text-align:center;color:var(--gc-muted);font-style:italic;">{{ $product->category?->name ?? '—' }}</td>
            @endforeach
        </tr>

        {{-- Availability --}}
        <tr>
            <td style="{{ $row_style }}font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);background:var(--gc-warm);">{{ __('Availability') }}</td>
            @foreach($compare_products as $product)
            @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
            <td style="{{ $row_style }}border-left:1px solid var(--gc-border);text-align:center;">
                @if($in_stock)
                    <span style="color:#2e7d32;font-weight:600;font-style:italic;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                @else
                    <span style="color:#c62828;font-weight:600;font-style:italic;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                @endif
            </td>
            @endforeach
        </tr>

        {{-- Action --}}
        <tr>
            <td style="{{ $row_style }}font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-muted);background:var(--gc-warm);">{{ __('Action') }}</td>
            @foreach($compare_products as $product)
            <td style="{{ $row_style }}border-left:1px solid var(--gc-border);text-align:center;">
                <button class="add-to-cart-btn gc-btn gc-btn-primary" data-product_id="{{ $product->id }}" style="font-size:11px;">
                    <i class="las la-shopping-bag"></i> {{ __('Add to Cart') }}
                </button>
            </td>
            @endforeach
        </tr>
    </tbody>
</table>
</div>

<div style="margin-top:24px;display:flex;gap:12px;flex-wrap:wrap;">
    <a href="{{ theme_shop_url() }}" class="gc-btn gc-btn-ghost">
        <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
    </a>
</div>

@else

<div style="text-align:center;padding:80px 0;">
    <div style="font-size:52px;margin-bottom:20px;"><i class="las la-balance-scale"></i></div>
    <div style="width:40px;height:1px;background:var(--gc-rose);margin:0 auto 20px;"></div>
    <h3 style="font-size:22px;font-weight:400;color:var(--gc-dark);margin-bottom:10px;font-family:Georgia,serif;font-style:italic;">{{ __('No Products to Compare') }}</h3>
    <p style="font-size:13px;color:var(--gc-muted);margin-bottom:32px;font-style:italic;">{{ __('Add products to compare by clicking the compare button on any product.') }}</p>
    <a href="{{ theme_shop_url() }}" class="gc-btn gc-btn-primary">
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
