@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Compare Products') }} @endsection
@section('page-title') {{ __('Compare Products') }} @endsection

@section('content')
<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ __('Compare Products') }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ __('Compare Products') }}</span>
        </div>
    </div>
</div>

<div class="cs-compare-section">
    <div class="container">
        <div class="row g-4">
            @forelse(\Gloudemans\Shoppingcart\Facades\Cart::instance("compare")->content() as $product)
                @php
                    $data          = get_product_dynamic_price($product);
                    $regular_price = $data['regular_price'];
                    $sale_price    = $data['sale_price'];
                    $discount      = $data['discount'];
                    $product_slug  = \Modules\Product\Entities\Product::find($product->id)?->slug;
                @endphp

                <div class="col-lg-4 col-md-6">
                    <div class="cs-compare-card">
                        <div class="cs-compare-thumb">
                            <a href="{{ theme_product_url($product_slug) }}">
                                {!! render_image_markup_by_attachment_id($product->options->image, '', 'grid') !!}
                            </a>
                        </div>

                        <div class="cs-compare-body">
                            <h4 class="cs-compare-name">
                                <a href="{{ theme_product_url($product_slug) }}">{{ $product->name }}</a>
                            </h4>

                            <div class="cs-compare-price">{{ amount_with_currency_symbol($sale_price ?? $product->price) }}</div>

                            <ul class="cs-compare-list">
                                @if(!empty($product->options['sku']))
                                <li>
                                    <span class="cs-compare-key">{{ __('SKU') }}</span>
                                    <span class="cs-compare-val">{{ $product->options['sku'] }}</span>
                                </li>
                                @endif

                                @if(!empty($product->options['description']))
                                <li class="cs-compare-desc">
                                    <span class="cs-compare-key">{{ __('Description') }}</span>
                                    <span class="cs-compare-val">{!! $product->options['description'] !!}</span>
                                </li>
                                @endif

                                @if(!empty($product->options['color_name']))
                                <li>
                                    <span class="cs-compare-key">{{ __('Color') }}</span>
                                    <span class="cs-compare-val">{{ $product->options['color_name'] }}</span>
                                </li>
                                @endif

                                @if(!empty($product->options['size_name']))
                                <li>
                                    <span class="cs-compare-key">{{ __('Size') }}</span>
                                    <span class="cs-compare-val">{{ $product->options['size_name'] }}</span>
                                </li>
                                @endif

                                @forelse($product->options['attributes'] ?? [] as $attrKey => $attrVal)
                                <li>
                                    <span class="cs-compare-key">{{ $attrKey }}</span>
                                    <span class="cs-compare-val">{{ $attrVal }}</span>
                                </li>
                                @empty
                                @endforelse
                            </ul>

                            <button type="button" class="cs-compare-remove compare-remove-btn"
                                    data-product_id="{{ $product->rowId }}">
                                <i class="las la-times-circle"></i> {{ __('Remove') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center cs-no-data">
                    <i class="las la-balance-scale"></i>
                    <p>{{ __('No products to compare.') }}</p>
                    <a href="{{ theme_digital_shop_url() }}" class="cs-dash-submit-btn">
                        <i class="las la-store"></i> {{ __('Continue Shopping') }}
                    </a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(function () {
    $(document).on('click', '.compare-remove-btn', function () {
        var productId = $(this).data('product_id');
        var card      = $(this).closest('.col-lg-4');

        $.ajax({
            url: '{{ theme_compare_remove_url() }}',
            type: 'GET',
            data: { product_id: productId },
            beforeSend: function () { $('.loader').show(); },
            success: function () {
                $('.loader').hide();
                card.fadeOut(300, function () { $(this).remove(); });

                var ids = (sessionStorage.getItem('products') || '').split(',').filter(function (v) { return v && v != productId; });
                sessionStorage.setItem('products', ids.join(','));
            },
            error: function () { $('.loader').hide(); }
        });
    });
});
</script>
@endsection
