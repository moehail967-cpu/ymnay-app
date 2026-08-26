@foreach($product_object as $product)
    @php
        $data = get_product_dynamic_price($product);
        $campaign_name = $data['campaign_name'];
        $regular_price = $data['regular_price'];
        $sale_price = $data['sale_price'];
        $discount = $data['discount'];
    @endphp

    <div class="col-xxl-4 col-lg-6 col-sm-6">
        <div class="casual-new-product-card">
            <div class="casual-new-product-card-thumb">
                <a href="{{ theme_product_url($product->slug) }}">
                    {!! render_image_markup_by_attachment_id($product->image_id, '', 'grid') !!}
                </a>

                <button class="casual-new-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}">
                    <i class="lar la-heart"></i>
                </button>

                <div class="cs-product-badges">
                    @if($discount != null)
                        <span class="cs-product-badge cs-product-badge-sale">{{ $discount }}% {{ __('Off') }}</span>
                    @endif
                    @if(!empty($product->badge))
                        <span class="cs-product-badge cs-product-badge-new">{{ $product?->badge?->name }}</span>
                    @endif
                    @if(!is_null($campaign_name))
                        <span class="cs-product-badge cs-product-badge-campaign">{{ $campaign_name }}</span>
                    @endif
                </div>
            </div>

            <div class="casual-new-product-card-contents">
                <div class="casual-new-product-category">
                    {{ $product?->category?->name ? strtoupper($product?->category?->name) : '' }}
                </div>

                <h5 class="casual-new-product-title">
                    <a href="{{ theme_product_url($product->slug) }}"> {!! Str::words($product->name, 15) !!} </a>
                </h5>

                <div class="casual-new-product-price">
                    <span class="casual-new-price-sale">{{ amount_with_currency_symbol($sale_price) }}</span>
                    @if($regular_price != null)
                        <span class="casual-new-price-regular">{{ amount_with_currency_symbol($regular_price) }}</span>
                    @endif
                </div>

                <a href="javascript:void(0)" data-product_id="{{ $product->id }}" class="add-to-cart-btn casual-new-add-to-cart">
                    <i class="las la-shopping-cart"></i> {{ __('Add to Cart') }}
                </a>
            </div>
        </div>
    </div>
@endforeach

{{ $product_object->links() }}
