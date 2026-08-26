@extends('tenant.frontend.frontend-page-master')
@section('title') {!! __('Compare Products') !!} @endsection
@section('page-title') {!! __('Compare Products') !!} @endsection
@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{{ __('Compare Products') }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ __('Compare') }}</span>
        </div>
    </div>
</div>
<section style="padding:60px 0 80px;">
    <div class="container">
        <div class="row g-4">
            @forelse(\Gloudemans\Shoppingcart\Facades\Cart::instance("compare")->content() as $product)
                @php
                    $data = get_product_dynamic_price($product);
                    $sale_price = $data['sale_price'];
                    $product_slug = \Modules\Product\Entities\Product::find($product->id)?->slug;
                @endphp
                <div class="col-lg-4 col-md-6">
                    <div class="bp-compare-card">
                        <div class="bp-compare-img">
                            <a href="{{ theme_product_url($product_slug) }}">
                                {!! render_image_markup_by_attachment_id($product->options->image, '', 'grid') !!}
                            </a>
                        </div>
                        <div class="bp-compare-body">
                            <h5 class="bp-compare-title">
                                <a href="{{ theme_product_url($product_slug) }}">{{ $product->name }}</a>
                            </h5>
                            <p class="bp-compare-price">{{ amount_with_currency_symbol($sale_price) }}</p>
                            @if(!empty($product?->options['sku']))
                            <p class="bp-compare-sku">{{ __('SKU:') }} <strong>{{ $product->options->sku }}</strong></p>
                            @endif
                            <ul class="bp-compare-attrs">
                                @if(!empty($product->options['color_name'] ?? ''))
                                <li><strong>{{ __('Color:') }}</strong> {{ $product->options['color_name'] }}</li>
                                @endif
                                @if(!empty($product->options['size_name'] ?? ''))
                                <li><strong>{{ __('Size:') }}</strong> {{ $product->options['size_name'] }}</li>
                                @endif
                                @forelse($product->options['attributes'] ?? [] as $k => $v)
                                <li><strong>{{ $k }}:</strong> {{ $v }}</li>
                                @empty @endforelse
                                @if(!empty($product->options['description'] ?? ''))
                                <li><strong>{{ __('Description:') }}</strong> {!! Str::limit($product->options['description'], 120) !!}</li>
                                @endif
                            </ul>
                            <a href="javascript:void(0)" class="bp-btn bp-btn-danger bp-btn-sm compare-remove-btn mt-3" data-product_id="{{ $product->rowId }}">
                                <i class="las la-times"></i> {{ __('Remove') }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="las la-not-equal" style="font-size:64px;color:#ccc;display:block;margin-bottom:16px;"></i>
                    <h4 style="color:#888;">{{ __('No Products to Compare') }}</h4>
                    <a href="{{ theme_shop_url() }}" class="bp-btn bp-btn-green mt-3">{{ __('Browse Products') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
$(function(){
    $(document).on('click', '.compare-remove-btn', function () {
        var product_id = $(this).data('product_id');
        $.ajax({
            url: '{{ theme_compare_remove_url() }}', type: 'GET', data: { product_id: product_id },
            beforeSend: function () { $('.loader').show(); },
            success: function (data) {
                $('.loader').hide();
                var sessionData = sessionStorage;
                if (sessionData['products']) {
                    var ids = sessionData['products'].split(',');
                    $.each(ids, function (index, value) { if (value == product_id) { ids.splice(index, 1); } });
                    sessionStorage.clear();
                    if (ids.join(',') !== '') { sessionStorage.setItem('products', ids.join(',')); }
                }
                location.reload();
            },
            error: function () { $('.loader').hide(); }
        });
    });
});
</script>
@endsection
