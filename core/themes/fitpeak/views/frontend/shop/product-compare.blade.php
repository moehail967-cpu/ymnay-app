@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare') }} @endsection

@section('content')
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{{ __('Compare') }} <span>{{ __('Products') }}</span></h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li>{{ __('Compare') }}</li>
        </ul>
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

<div class="container" style="padding:48px 0 72px;">
    @if(count($compare_products) > 0)
    <div class="table-responsive">
        <table style="width:100%;border-collapse:collapse;min-width:600px;">

            {{-- Header row: product images + names --}}
            <tr>
                <td style="width:180px;padding:16px;background:var(--fp-card);border:1px solid var(--fp-border);font-family:var(--fp-font-head);font-size:12px;font-weight:700;color:var(--fp-green);text-transform:uppercase;letter-spacing:1.5px;">{{ __('Product') }}</td>
                @foreach($compare_products as $product)
                @php $imgUrl = theme_product_image($product->image_id ?? null, 'grid'); @endphp
                <td style="padding:16px;background:var(--fp-card);border:1px solid var(--fp-border);text-align:center;vertical-align:top;">
                    <div style="width:120px;height:120px;margin:0 auto 12px;border:1px solid var(--fp-border);border-radius:var(--fp-radius);overflow:hidden;background:var(--fp-mid);">
                        @if($imgUrl)
                            <img src="{{ $imgUrl }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--fp-border);font-size:36px;"><i class="mdi mdi-dumbbell"></i></div>
                        @endif
                    </div>
                    <a href="{{ theme_product_url($product->slug) }}" style="font-family:var(--fp-font-head);font-size:14px;font-weight:700;color:var(--fp-text);text-decoration:none;display:block;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">{{ \Illuminate\Support\Str::words($product->name, 6) }}</a>
                    <button class="compare-remove-btn" data-product_id="{{ $compare_row_ids[$product->id] ?? '' }}"
                            style="display:inline-flex;align-items:center;gap:4px;padding:5px 12px;background:transparent;border:1px solid var(--fp-border);border-radius:var(--fp-radius);font-size:11px;color:var(--fp-muted);cursor:pointer;font-family:var(--fp-font-head);">
                        <i class="mdi mdi-close"></i> {{ __('Remove') }}
                    </button>
                </td>
                @endforeach
            </tr>

            {{-- Price --}}
            <tr>
                <td style="padding:14px 16px;background:var(--fp-surface);border:1px solid var(--fp-border);font-family:var(--fp-font-head);font-size:12px;font-weight:700;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Price') }}</td>
                @foreach($compare_products as $product)
                @php $pdata = theme_product_price($product); @endphp
                <td style="padding:14px 16px;border:1px solid var(--fp-border);text-align:center;background:var(--fp-card);">
                    <span style="font-family:var(--fp-font-head);font-size:18px;font-weight:800;color:var(--fp-green);">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                    @if($pdata['regular_price'])
                        <span style="display:block;font-size:12px;color:var(--fp-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                    @endif
                </td>
                @endforeach
            </tr>

            {{-- Rating --}}
            <tr>
                <td style="padding:14px 16px;background:var(--fp-surface);border:1px solid var(--fp-border);font-family:var(--fp-font-head);font-size:12px;font-weight:700;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Rating') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:14px 16px;border:1px solid var(--fp-border);text-align:center;background:var(--fp-card);">
                    {!! theme_star_rating($product) !!}
                </td>
                @endforeach
            </tr>

            {{-- SKU --}}
            <tr>
                <td style="padding:14px 16px;background:var(--fp-surface);border:1px solid var(--fp-border);font-family:var(--fp-font-head);font-size:12px;font-weight:700;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('SKU') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:14px 16px;border:1px solid var(--fp-border);text-align:center;background:var(--fp-card);">
                    <span style="font-family:var(--fp-font-head);color:var(--fp-muted);">{{ $product->sku ?? '—' }}</span>
                </td>
                @endforeach
            </tr>

            {{-- Category --}}
            <tr>
                <td style="padding:14px 16px;background:var(--fp-surface);border:1px solid var(--fp-border);font-family:var(--fp-font-head);font-size:12px;font-weight:700;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Category') }}</td>
                @foreach($compare_products as $product)
                <td style="padding:14px 16px;border:1px solid var(--fp-border);text-align:center;background:var(--fp-card);font-size:13px;color:var(--fp-text);">
                    {{ $product->category?->name ?? '—' }}
                </td>
                @endforeach
            </tr>

            {{-- Stock --}}
            <tr>
                <td style="padding:14px 16px;background:var(--fp-surface);border:1px solid var(--fp-border);font-family:var(--fp-font-head);font-size:12px;font-weight:700;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;">{{ __('Stock') }}</td>
                @foreach($compare_products as $product)
                @php $in_stock = optional($product->inventory)->stock_count > 0; @endphp
                <td style="padding:14px 16px;border:1px solid var(--fp-border);text-align:center;background:var(--fp-card);">
                    @if($in_stock)
                        <span style="font-family:var(--fp-font-head);color:var(--fp-green);"><i class="mdi mdi-check-circle"></i> {{ __('In Stock') }}</span>
                    @else
                        <span style="font-family:var(--fp-font-head);color:#ff4d4d;"><i class="mdi mdi-close-circle"></i> {{ __('Out of Stock') }}</span>
                    @endif
                </td>
                @endforeach
            </tr>

            {{-- Add to cart row --}}
            <tr>
                <td style="padding:14px 16px;background:var(--fp-surface);border:1px solid var(--fp-border);font-family:var(--fp-font-head);font-size:12px;color:var(--fp-muted);text-transform:uppercase;letter-spacing:1px;"></td>
                @foreach($compare_products as $product)
                <td style="padding:14px 16px;border:1px solid var(--fp-border);text-align:center;background:var(--fp-card);">
                    <button class="fp-btn fp-btn-primary fp-btn-sm add-to-cart-btn"
                            data-product_id="{{ $product->id }}"
                            style="display:inline-flex;align-items:center;gap:6px;">
                        <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                    </button>
                </td>
                @endforeach
            </tr>

        </table>
    </div>

    <div style="margin-top:24px;">
        <a href="{{ theme_shop_url() }}" class="fp-btn fp-btn-dark">
            <i class="mdi mdi-arrow-left"></i> {{ __('Continue Shopping') }}
        </a>
    </div>

    @else
    <div style="text-align:center;padding:80px 0;color:var(--fp-muted);">
        <i class="mdi mdi-compare-remove" style="font-size:80px;color:var(--fp-border);display:block;margin-bottom:20px;"></i>
        <h2 style="font-family:var(--fp-font-head);font-size:28px;font-weight:800;color:var(--fp-text);text-transform:uppercase;letter-spacing:2px;margin-bottom:12px;">{{ __('Nothing to Compare') }}</h2>
        <p style="color:var(--fp-muted);margin-bottom:28px;">{{ __('Add products to compare from the shop page.') }}</p>
        <a href="{{ theme_shop_url() }}" class="fp-btn fp-btn-primary">
            <i class="mdi mdi-shopping"></i> {{ __('Browse Products') }}
        </a>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(function () {
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
});
</script>
@endsection
