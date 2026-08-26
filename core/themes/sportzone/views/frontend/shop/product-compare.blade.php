@extends('tenant.frontend.frontend-page-master')
@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ __('Compare Products') }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('Compare') }}</span>
        </div>
    </div>
</div>

<div style="background:var(--sz-bg);padding:48px 0 72px;">
    <div class="container">

        @php
            $compare_items    = \Gloudemans\Shoppingcart\Facades\Cart::instance('compare')->content();
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

        @if(count($compare_products) === 0)
        <div style="text-align:center;padding:72px 0;">
            <i class="mdi mdi-compare-horizontal" style="font-size:72px;color:var(--sz-muted);opacity:.3;display:block;margin-bottom:20px;"></i>
            <h2 style="font-family:var(--sz-font-head);font-size:26px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-dark);margin-bottom:10px;">{{ __('Nothing to Compare') }}</h2>
            <p style="font-size:15px;color:var(--sz-muted);margin-bottom:28px;">{{ __('Add products to compare their features side by side.') }}</p>
            <a href="{{ theme_shop_url() }}" class="sz-btn sz-btn-red" style="gap:8px;">
                <i class="mdi mdi-storefront-outline"></i> {{ __('Browse Products') }}
            </a>
        </div>
        @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;min-width:640px;background:var(--sz-white);border:2px solid var(--sz-border);">
                {{-- Images --}}
                <tr style="border-bottom:2px solid var(--sz-border);">
                    <td style="padding:16px 20px;font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:#fff;background:var(--sz-navy);width:160px;">{{ __('Product') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:20px;text-align:center;border-left:2px solid var(--sz-border);">
                        @php $img = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                        <div style="height:140px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;">
                            @if($img)
                                <img src="{{ $img }}" alt="{{ $product->name }}" style="max-height:100%;max-width:100%;object-fit:contain;">
                            @else
                                <i class="mdi mdi-dumbbell" style="font-size:56px;color:var(--sz-red);opacity:.3;"></i>
                            @endif
                        </div>
                        <div style="font-family:var(--sz-font-head);font-size:14px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;color:var(--sz-dark);margin-bottom:6px;">{{ \Illuminate\Support\Str::words($product->name, 5) }}</div>
                        <button type="button" class="compare-remove-btn sz-btn sz-btn-outline sz-btn-sm" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}" style="gap:4px;font-size:11px;">
                            <i class="mdi mdi-close"></i> {{ __('Remove') }}
                        </button>
                    </td>
                    @endforeach
                </tr>

                {{-- Price --}}
                <tr style="border-bottom:1px solid var(--sz-border);">
                    <td style="padding:14px 20px;font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);background:var(--sz-bg);">{{ __('Price') }}</td>
                    @foreach($compare_products as $product)
                    @php $pd = theme_product_price($product); @endphp
                    <td style="padding:14px 20px;text-align:center;border-left:2px solid var(--sz-border);">
                        <span style="font-family:var(--sz-font-head);font-size:20px;font-weight:700;color:var(--sz-red);">{{ amount_with_currency_symbol($pd['sale_price']) }}</span>
                        @if($pd['discount'])
                            <span style="display:block;font-size:12px;color:var(--sz-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pd['regular_price']) }}</span>
                        @endif
                    </td>
                    @endforeach
                </tr>

                {{-- Rating --}}
                <tr style="border-bottom:1px solid var(--sz-border);">
                    <td style="padding:14px 20px;font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);background:var(--sz-bg);">{{ __('Rating') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:14px 20px;text-align:center;border-left:2px solid var(--sz-border);color:#FDD835;font-size:15px;">
                        {!! theme_star_rating($product) !!}
                    </td>
                    @endforeach
                </tr>

                {{-- SKU --}}
                <tr style="border-bottom:1px solid var(--sz-border);">
                    <td style="padding:14px 20px;font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);background:var(--sz-bg);">{{ __('SKU') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:14px 20px;text-align:center;border-left:2px solid var(--sz-border);font-size:13px;color:var(--sz-muted);">{{ $product->sku ?? '—' }}</td>
                    @endforeach
                </tr>

                {{-- Category --}}
                <tr style="border-bottom:1px solid var(--sz-border);">
                    <td style="padding:14px 20px;font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);background:var(--sz-bg);">{{ __('Category') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:14px 20px;text-align:center;border-left:2px solid var(--sz-border);font-size:13px;color:var(--sz-muted);">{{ $product->category?->name ?? '—' }}</td>
                    @endforeach
                </tr>

                {{-- Stock --}}
                <tr style="border-bottom:1px solid var(--sz-border);">
                    <td style="padding:14px 20px;font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);background:var(--sz-bg);">{{ __('Stock') }}</td>
                    @foreach($compare_products as $product)
                    @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
                    <td style="padding:14px 20px;text-align:center;border-left:2px solid var(--sz-border);font-size:13px;">
                        @if($in_stock)
                            <span style="color:#2E7D32;"><i class="mdi mdi-check-circle"></i> {{ __('In Stock') }}</span>
                        @else
                            <span style="color:var(--sz-red);"><i class="mdi mdi-close-circle"></i> {{ __('Out of Stock') }}</span>
                        @endif
                    </td>
                    @endforeach
                </tr>

                {{-- Add to Cart --}}
                <tr>
                    <td style="padding:14px 20px;font-family:var(--sz-font-head);font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:var(--sz-muted);background:var(--sz-bg);">{{ __('Action') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:14px 20px;text-align:center;border-left:2px solid var(--sz-border);">
                        <button type="button" class="sz-btn sz-btn-red sz-btn-sm add-to-cart-btn" data-product_id="{{ $product->id }}" style="gap:6px;justify-content:center;width:100%;">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </td>
                    @endforeach
                </tr>
            </table>
        </div>

        <div style="margin-top:20px;">
            <a href="{{ theme_shop_url() }}" class="sz-btn sz-btn-outline-dark">
                <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
        </div>
        @endif

    </div>
</div>
@endsection

@section('scripts')
<script>
$(function(){
    $(document).on('click','.compare-remove-btn',function(){
        var rowId = $(this).data('product_id');
        var btn = $(this);
        btn.prop('disabled', true).css('opacity', '.5');
        $.get('{{ theme_compare_remove_url() }}', { product_id: rowId }, function(){
            var ids = (sessionStorage.getItem('products') || '').split(',').filter(function(v){ return v && v !== String(rowId); });
            sessionStorage.setItem('products', ids.join(','));
            if (!ids.length) sessionStorage.removeItem('products');
            location.reload();
        }).fail(function(){ btn.prop('disabled', false).css('opacity', '1'); });
    });
});
</script>
@endsection
