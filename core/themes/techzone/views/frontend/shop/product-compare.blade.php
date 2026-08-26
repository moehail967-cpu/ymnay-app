@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')
<div style="background:var(--tz-surface);border-bottom:1px solid var(--tz-border);padding:28px 0 20px;">
    <div class="container">
        <h2 style="font-size:20px;font-weight:700;color:#fff;margin-bottom:8px;">{{ __('Compare Products') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--tz-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--tz-muted);text-decoration:none;" onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-muted)'">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Compare') }}</span>
        </div>
    </div>
</div>

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

<div class="container" style="padding:40px 0 72px;">
    @if(count($compare_products) === 0)
    <div style="text-align:center;padding:80px 0;">
        <div style="width:100px;height:100px;border-radius:50%;background:var(--tz-blue-glow);border:1px solid var(--tz-blue);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:44px;color:var(--tz-blue);">
            <i class="mdi mdi-compare-horizontal"></i>
        </div>
        <h3 style="font-size:20px;font-weight:700;color:#fff;margin-bottom:10px;">{{ __('Nothing to Compare') }}</h3>
        <p style="font-size:14px;color:var(--tz-muted);margin-bottom:24px;">{{ __('Add products to your comparison list from the shop.') }}</p>
        <a href="{{ theme_shop_url() }}"
           style="display:inline-flex;align-items:center;gap:8px;background:var(--tz-blue);color:#fff;padding:11px 24px;border-radius:var(--tz-radius-sm);font-size:14px;font-weight:700;text-decoration:none;">
            <i class="mdi mdi-arrow-left"></i> {{ __('Browse Products') }}
        </a>
    </div>
    @else
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:600px;">
            <thead>
                <tr>
                    <td style="padding:12px 16px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--tz-muted);background:var(--tz-surface);border:1px solid var(--tz-border);width:160px;">{{ __('Product') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:16px;text-align:center;background:var(--tz-card);border:1px solid var(--tz-border);">
                        @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:90px;height:90px;object-fit:contain;margin-bottom:8px;">
                        @else
                            <div style="font-size:44px;color:var(--tz-blue);margin-bottom:8px;"><i class="las la-laptop"></i></div>
                        @endif
                        <div style="font-size:13px;font-weight:600;color:var(--tz-text);margin-bottom:8px;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</div>
                        <button class="compare-remove-btn"
                                data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}"
                                style="background:transparent;border:1px solid var(--tz-border);color:var(--tz-muted);padding:4px 10px;border-radius:var(--tz-radius-sm);font-size:11px;cursor:pointer;font-family:var(--tz-font);transition:all .2s;"
                                onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444'" onmouseout="this.style.borderColor='var(--tz-border)';this.style.color='var(--tz-muted)'">
                            <i class="mdi mdi-close"></i> {{ __('Remove') }}
                        </button>
                    </td>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--tz-muted);background:var(--tz-surface);border:1px solid var(--tz-border);">{{ __('Price') }}</td>
                    @foreach($compare_products as $product)
                    @php $pd = theme_product_price($product); @endphp
                    <td style="padding:14px;text-align:center;border:1px solid var(--tz-border);">
                        <span style="font-size:15px;font-weight:700;color:var(--tz-blue);">{{ amount_with_currency_symbol($pd['sale_price']) }}</span>
                        @if($pd['regular_price'])
                            <span style="display:block;font-size:12px;color:var(--tz-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pd['regular_price']) }}</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--tz-muted);background:var(--tz-surface);border:1px solid var(--tz-border);">{{ __('Rating') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:14px;text-align:center;border:1px solid var(--tz-border);">
                        {!! theme_star_rating($product) !!}
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--tz-muted);background:var(--tz-surface);border:1px solid var(--tz-border);">{{ __('SKU') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:14px;text-align:center;border:1px solid var(--tz-border);font-size:13px;color:var(--tz-muted);">{{ $product->sku ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--tz-muted);background:var(--tz-surface);border:1px solid var(--tz-border);">{{ __('Category') }}</td>
                    @foreach($compare_products as $product)
                    <td style="padding:14px;text-align:center;border:1px solid var(--tz-border);font-size:13px;color:var(--tz-muted);">{{ $product->category?->name ?? '—' }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--tz-muted);background:var(--tz-surface);border:1px solid var(--tz-border);">{{ __('Stock') }}</td>
                    @foreach($compare_products as $product)
                    @php $in = optional($product->inventory)->stock_count > 0; @endphp
                    <td style="padding:14px;text-align:center;border:1px solid var(--tz-border);">
                        @if($in)
                            <span style="color:#22c55e;font-size:13px;font-weight:600;"><i class="mdi mdi-check-circle"></i> {{ __('In Stock') }}</span>
                        @else
                            <span style="color:#ef4444;font-size:13px;font-weight:600;"><i class="mdi mdi-close-circle"></i> {{ __('Out of Stock') }}</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <td style="padding:12px 16px;font-size:13px;font-weight:600;color:var(--tz-muted);background:var(--tz-surface);border:1px solid var(--tz-border);"></td>
                    @foreach($compare_products as $product)
                    <td style="padding:14px;text-align:center;border:1px solid var(--tz-border);">
                        <button type="button" class="add-to-cart-btn" data-product_id="{{ $product->id }}"
                                style="display:inline-flex;align-items:center;gap:6px;background:var(--tz-blue);color:#fff;border:0;padding:9px 16px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:600;cursor:pointer;font-family:var(--tz-font);transition:background .2s;"
                                onmouseover="this.style.background='var(--tz-blue-deep)'" onmouseout="this.style.background='var(--tz-blue)'">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px;">
        <a href="{{ theme_shop_url() }}"
           style="display:inline-flex;align-items:center;gap:6px;background:transparent;color:var(--tz-muted);border:1px solid var(--tz-border);padding:9px 16px;border-radius:var(--tz-radius-sm);font-size:13px;font-weight:600;text-decoration:none;transition:all .2s;"
           onmouseover="this.style.borderColor='var(--tz-blue)';this.style.color='var(--tz-blue)'" onmouseout="this.style.borderColor='var(--tz-border)';this.style.color='var(--tz-muted)'">
            <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
        </a>
    </div>
    @endif
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
