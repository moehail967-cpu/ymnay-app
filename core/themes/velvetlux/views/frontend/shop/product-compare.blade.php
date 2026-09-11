@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Selection') }}</div>
        <h2 style="font-size:28px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('Compare') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--vl-muted);letter-spacing:1px;text-transform:uppercase;">
            <a href="{{ theme_home_url() }}" style="color:var(--vl-champagne);">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{{ __('Compare') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:48px 0 80px;">
    @php
        $compare_items = \Gloudemans\Shoppingcart\Facades\Cart::instance("compare")->content();
    @endphp

    @if($compare_items->isEmpty())
        <div style="text-align:center;padding:80px 24px;">
            <div style="font-size:64px;color:var(--vl-champagne);opacity:.25;margin-bottom:24px;">⊞</div>
            <div style="font-size:10px;letter-spacing:5px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:12px;">{{ __('Nothing to Compare') }}</div>
            <h3 style="font-size:24px;font-weight:300;color:var(--vl-ivory);font-family:'Cormorant Garamond',serif;margin-bottom:16px;">{{ __('Your comparison list is empty') }}</h3>
            <a href="{{ theme_shop_url() }}" class="vl-btn vl-btn-primary">{{ __('Explore Collection') }}</a>
        </div>
    @else
        <div class="table-responsive">
            <table style="width:100%;border-collapse:collapse;background:var(--vl-card);">
                {{-- Header row: product images --}}
                <thead>
                    <tr style="border-bottom:1px solid var(--vl-border);">
                        <th style="padding:20px 24px;font-size:9px;font-weight:400;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);font-family:'Inter',sans-serif;width:160px;border-right:1px solid var(--vl-border);">
                            {{ __('Product') }}
                        </th>
                        @foreach($compare_items as $item)
                        @php
                            $vl_product = is_object($item->model) ? $item->model : \Modules\Product\Entities\Product::find($item->id);
                        @endphp
                        <th style="padding:24px;text-align:center;border-right:1px solid var(--vl-border);vertical-align:top;">
                            @php
                                $img = get_attachment_image_by_id($vl_product->image_id ?? null, 'grid');
                                $img_url = $img['img_url'] ?? null;
                            @endphp
                            <div style="display:flex;flex-direction:column;align-items:center;gap:12px;">
                                <div style="width:120px;height:150px;overflow:hidden;background:var(--vl-surface);display:flex;align-items:center;justify-content:center;">
                                    @if($img_url)
                                        <img src="{{ $img_url }}" alt="{{ $item->name }}" style="width:100%;height:100%;object-fit:cover;">
                                    @else
                                        <span style="font-size:36px;color:var(--vl-champagne);opacity:.3;">◆</span>
                                    @endif
                                </div>
                                <div style="font-family:'Cormorant Garamond',serif;font-size:15px;font-weight:300;color:var(--vl-ivory);">
                                    <a href="{{ theme_product_url($vl_product->slug ?? '') }}" style="color:inherit;text-decoration:none;">{{ $item->name }}</a>
                                </div>
                                <button class="compare-remove-btn"
                                        data-product_id="{{ $item->rowId }}"
                                        style="background:transparent;border:1px solid var(--vl-border);color:var(--vl-muted);padding:6px 14px;font-size:9px;letter-spacing:2px;text-transform:uppercase;cursor:pointer;font-family:'Inter',sans-serif;transition:all .2s;"
                                        onmouseover="this.style.borderColor='#E53E3E';this.style.color='#E53E3E';" onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)';">
                                    <i class="mdi mdi-close"></i> {{ __('Remove') }}
                                </button>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    {{-- Price --}}
                    <tr style="border-bottom:1px solid rgba(58,36,68,.5);">
                        <td style="padding:16px 24px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);font-family:'Inter',sans-serif;background:var(--vl-surface);border-right:1px solid var(--vl-border);">
                            {{ __('Price') }}
                        </td>
                        @foreach($compare_items as $item)
                        @php
                            $vl_product = is_object($item->model) ? $item->model : \Modules\Product\Entities\Product::find($item->id);
                            $pd = theme_product_price($vl_product);
                        @endphp
                        <td style="padding:16px 24px;text-align:center;border-right:1px solid var(--vl-border);">
                            <span style="font-size:16px;color:var(--vl-champagne);font-family:'Inter',sans-serif;">{{ amount_with_currency_symbol($pd['sale_price']) }}</span>
                            @if($pd['regular_price'])
                                <br><span style="font-size:12px;color:var(--vl-muted);text-decoration:line-through;">{{ amount_with_currency_symbol($pd['regular_price']) }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Rating --}}
                    <tr style="border-bottom:1px solid rgba(58,36,68,.5);">
                        <td style="padding:16px 24px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);font-family:'Inter',sans-serif;background:var(--vl-surface);border-right:1px solid var(--vl-border);">
                            {{ __('Rating') }}
                        </td>
                        @foreach($compare_items as $item)
                        @php $vl_product = is_object($item->model) ? $item->model : \Modules\Product\Entities\Product::find($item->id); @endphp
                        <td style="padding:16px 24px;text-align:center;border-right:1px solid var(--vl-border);">
                            {!! theme_star_rating($vl_product) !!}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Category --}}
                    <tr style="border-bottom:1px solid rgba(58,36,68,.5);">
                        <td style="padding:16px 24px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);font-family:'Inter',sans-serif;background:var(--vl-surface);border-right:1px solid var(--vl-border);">
                            {{ __('Category') }}
                        </td>
                        @foreach($compare_items as $item)
                        @php $vl_product = is_object($item->model) ? $item->model : \Modules\Product\Entities\Product::find($item->id); @endphp
                        <td style="padding:16px 24px;text-align:center;font-size:13px;color:var(--vl-muted);border-right:1px solid var(--vl-border);">
                            {{ $vl_product?->category?->name ?? '—' }}
                        </td>
                        @endforeach
                    </tr>

                    {{-- Stock --}}
                    <tr style="border-bottom:1px solid rgba(58,36,68,.5);">
                        <td style="padding:16px 24px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);font-family:'Inter',sans-serif;background:var(--vl-surface);border-right:1px solid var(--vl-border);">
                            {{ __('Stock') }}
                        </td>
                        @foreach($compare_items as $item)
                        @php
                            $vl_product = is_object($item->model) ? $item->model : \Modules\Product\Entities\Product::find($item->id);
                            $stock = optional($vl_product?->inventory)->stock_count ?? 0;
                        @endphp
                        <td style="padding:16px 24px;text-align:center;border-right:1px solid var(--vl-border);">
                            @if($stock > 0)
                                <span style="font-size:12px;color:#48BB78;"><i class="las la-check-circle"></i> {{ __('In Stock') }}</span>
                            @else
                                <span style="font-size:12px;color:#E53E3E;"><i class="las la-times-circle"></i> {{ __('Out of Stock') }}</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>

                    {{-- Add to Cart --}}
                    <tr>
                        <td style="padding:20px 24px;font-size:9px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-champagne);font-family:'Inter',sans-serif;background:var(--vl-surface);border-right:1px solid var(--vl-border);">
                            {{ __('Action') }}
                        </td>
                        @foreach($compare_items as $item)
                        @php $vl_product = is_object($item->model) ? $item->model : \Modules\Product\Entities\Product::find($item->id); @endphp
                        <td style="padding:20px 24px;text-align:center;border-right:1px solid var(--vl-border);">
                            <button class="add-to-cart-btn vl-btn vl-btn-primary"
                                    data-product_id="{{ $vl_product->id ?? $item->id }}"
                                    style="font-size:9px;letter-spacing:2px;padding:10px 20px;">
                                <i class="las la-shopping-bag"></i> {{ __('Add to Bag') }}
                            </button>
                        </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>

        <div style="margin-top:32px;display:flex;align-items:center;gap:16px;">
            <a href="{{ theme_shop_url() }}" class="vl-btn vl-btn-outline-gold">
                <i class="las la-arrow-left"></i> {{ __('Continue Shopping') }}
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
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
</script>
@endsection
