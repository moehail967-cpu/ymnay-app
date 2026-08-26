@extends('tenant.frontend.frontend-page-master')

@section('title')
    {!! __('Compare Product') !!}
@endsection

@section('page-title')
    {!! __('Compare Product') !!}
@endsection

@section('content')
    <!-- Compare Area (Digital Shop) -->
    <section class="hf-page-section">
        <div class="container">

            <div class="hf-compare-wrap">
                <h2 class="hf-compare-heading">{{ __('Product Comparison') }}</h2>

                @php
                    $compare_items = \Gloudemans\Shoppingcart\Facades\Cart::instance("compare")->content();
                @endphp

                @if($compare_items->isEmpty())
                    <div class="hf-empty-wrap text-center" style="padding:60px 0;">
                        <i class="las la-retweet" style="font-size:56px;color:#e0d0cc;display:block;margin-bottom:16px;"></i>
                        <h4 class="hf-empty-title">{{ __('No Product Available') }}</h4>
                        <a href="{{ theme_shop_url() }}" class="hf-btn hf-btn-primary mt-4">{{ __('Browse Products') }}</a>
                    </div>
                @else
                    <div class="hf-compare-grid">
                        @foreach($compare_items as $product)
                            @php
                                $data = get_product_dynamic_price($product);
                                $campaign_name = $data['campaign_name'];
                                $regular_price = $data['regular_price'];
                                $sale_price = $data['sale_price'];
                                $discount = $data['discount'];

                                $product_slug = \Modules\Product\Entities\Product::find($product->id);
                                $product_slug = $product_slug->slug;
                            @endphp

                            <div class="hf-compare-card">
                                <div class="hf-compare-img-wrap">
                                    <a href="{{ theme_product_url($product_slug) }}">
                                        {!! render_image_markup_by_attachment_id($product->options->image, 'hf-compare-img', 'grid') !!}
                                    </a>
                                </div>

                                <div class="hf-compare-body">
                                    <h3 class="hf-compare-name">
                                        <a href="{{ theme_product_url($product_slug) }}">{{ $product->name }}</a>
                                    </h3>

                                    <div class="hf-compare-price-row">
                                        <span class="hf-compare-price">{{ amount_with_currency_symbol($product->price) }}</span>
                                        @if(!empty($discount))
                                            <span class="hf-card-badge" style="position:static;display:inline-block;">{{ $discount }}% {{ __('off') }}</span>
                                        @endif
                                    </div>

                                    @if(!empty($product?->options['description']))
                                        <p class="hf-compare-sku">{{ __('SKU:') }} <strong>{{ $product?->options?->sku }}</strong></p>
                                    @endif

                                    <ul class="hf-compare-attrs">
                                        @if(!empty($product?->options['description']))
                                            <li>
                                                <span class="hf-compare-attr-key">{{ __('Description:') }}</span>
                                                <span class="hf-compare-attr-val">{!! $product?->options['description'] !!}</span>
                                            </li>
                                        @endif

                                        @if(!empty($product->options["color_name"] ?? ''))
                                            <li>
                                                <span class="hf-compare-attr-key">{{ __('Color:') }}</span>
                                                <span class="hf-compare-attr-val">{{ $product->options['color_name'] }}</span>
                                            </li>
                                        @endif

                                        @if(!empty($product->options["size_name"]))
                                            <li>
                                                <span class="hf-compare-attr-key">{{ __('Size:') }}</span>
                                                <span class="hf-compare-attr-val">{{ $product->options['size_name'] }}</span>
                                            </li>
                                        @endif

                                        @forelse($product->options["attributes"] ?? [] as $key => $value)
                                            <li>
                                                <span class="hf-compare-attr-key">{{ $key }}</span>
                                                <span class="hf-compare-attr-val">{{ $value }}</span>
                                            </li>
                                        @empty
                                        @endforelse
                                    </ul>
                                </div>

                                <button class="hf-compare-remove compare-remove-btn"
                                        data-product_id="{{ $product->rowId }}">
                                    <i class="las la-times"></i> {{ __('Remove') }}
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </section>

    @include(include_theme_path('shop.partials.shop-footer'))
@endsection

@section('scripts')
    <script>
        $(function () {
            $(document).on('click', '.compare-remove-btn', function () {
                let product_id = $(this).data('product_id');
                let $card = $(this).closest('.hf-compare-card');

                $.ajax({
                    url: '{{ theme_compare_remove_url() }}',
                    type: 'GET',
                    data: { product_id: product_id },
                    beforeSend: function () { $('.loader').show(); },
                    success: function (data) {
                        $('.loader').hide();
                        $card.fadeOut(300, function () { $(this).remove(); });

                        let sessionData = sessionStorage;
                        if (sessionData['products']) {
                            let ids = sessionData['products'].split(',');
                            $.each(ids, function (index, value) {
                                if (value == product_id) { ids.splice(index, 1); }
                            });
                            let new_items = String(ids.join(","));
                            sessionStorage.clear();
                            if (new_items !== '') { sessionStorage.setItem('products', new_items); }
                        }
                    },
                    error: function () { $('.loader').hide(); }
                });
            });
        });
    </script>
@endsection
