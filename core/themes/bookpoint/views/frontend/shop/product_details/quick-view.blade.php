@php
    $data               = get_product_dynamic_price($product);
    $campaign_name      = $data['campaign_name'];
    $data_regular_price = $data['regular_price'];
    $data_sale_price    = $data['sale_price'];
    $discount           = $data['discount'];
    $campaign_product   = $product?->campaign_product;
    $sale_price         = $data_sale_price;
    $deleted_price      = $data_regular_price;
    $campaign_percentage = $discount;
    $stock_count        = $campaign_product
        ? ($campaign_product->units_for_sale !== null
            ? max(0, $campaign_product->units_for_sale - (int) $campaign_product->sold_count)
            : null)
        : optional($product->inventory)->stock_count;
    $stock_count        = $stock_count > 0 ? $stock_count : 0;
    if ($campaign_product) {
        $campaign_title  = \Modules\Campaign\Entities\Campaign::select('id','title')->where('id', $campaign_product?->id)->first();
        $campaign_active = $data['campaign_active'];
    }
    $quickView    = true;
    $image_details = get_attachment_image_by_id($product->image_id, 'full');
    $img_url       = $image_details['img_url'] ?? null;
@endphp

<div class="modal-dialog modal-xl">
    <div class="modal-content bp-qv-modal">

        {{-- Close --}}
        <button class="bp-qv-close quick-view-close-btn" aria-label="{{ __('Close') }}">
            <i class="las la-times"></i>
        </button>

        <div class="row g-0">

            {{-- Image --}}
            <div class="col-lg-5 bp-qv-img-col">
                <div class="global-slick-init shop-details-top-slider quick-view-long-img"
                     id="shop_details_gallery_slider"
                     data-asNavFor=".shop-details-click-img"
                     data-fade="true" data-infinite="true"
                     data-autoplaySpeed="3000" data-autoplay="true"
                     data-src="{{ $img_url }}">
                    <div class="bp-qv-thumb-wrap position-relative">
                        @if($img_url)
                            <img src="{{ $img_url }}" alt="{{ $product->name }}" class="bp-qv-main-img">
                        @else
                            <div class="bp-qv-img-placeholder"><i class="las la-book la-3x"></i></div>
                        @endif

                        @if($discount || !empty($product->badge))
                        <div class="bp-qv-badges">
                            @if($discount)
                                <span class="bp-card-badge" style="background:#e94560;">{{ $discount }}% {{ __('off') }}</span>
                            @endif
                            @if(!empty($product->badge))
                                <span class="bp-card-badge">{{ $product->badge->name }}</span>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Info --}}
            <div class="col-lg-7 bp-qv-info-col">

                {{-- Title --}}
                <h2 class="bp-qv-title">{{ $product->name }}</h2>

                {{-- Stars + stock --}}
                <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                    {!! theme_star_rating($product) !!}
                    <span id="quick_view_stock"
                          data-stock-text='{!! $stock_count > 0 ? "<span class=\"text-success\">".__("In Stock")."</span>" : "<span class=\"text-danger\">".__("Out of Stock")."</span>" !!}'>
                        {!! $stock_count > 0
                            ? '<span class="text-success"><i class="las la-check-circle"></i> '.__('In Stock').'</span>'
                            : '<span class="text-danger"><i class="las la-times-circle"></i> '.__('Out of Stock').'</span>' !!}
                    </span>
                </div>

                {{-- Price --}}
                <div class="mb-4 d-flex align-items-baseline gap-2 flex-wrap">
                    <span class="bp-pd-price"
                          id="quick-view-price"
                          data-main-price="{{ $sale_price }}"
                          data-currency-symbol="{{ site_currency_symbol() }}">
                        {{ amount_with_currency_symbol($sale_price) }}
                    </span>
                    @if($deleted_price)
                        <span class="bp-pd-was">{{ amount_with_currency_symbol($deleted_price) }}</span>
                    @endif
                    @if($discount)
                        <span class="bp-pd-discount-badge">{{ $discount }}% {{ __('OFF') }}</span>
                    @endif
                </div>

                {{-- Campaign --}}
                @if($campaign_product !== null && $campaign_product->status !== 'draft')
                    <div class="mb-3">
                        <h6 class="bp-pd-campaign-title">{{ $campaign_name }}</h6>
                    </div>
                @endif

                {{-- Product options (sizes / colors / qty / buttons) --}}
                <div class="bp-product-form quick-view-shop-wrapper">
                    @include(include_theme_path('shop.product_details.partials.product-options'))
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    window.quick_view_attribute_store      = JSON.parse('{!! json_encode($product_inventory_set) !!}');
    window.quick_view_additional_info_store = JSON.parse('{!! json_encode($additional_info_store) !!}');
    window.quick_view_available_options    = $('.quick-view-value-input-area');
    window.quick_view_has_campaign         = '{{ empty($campaign_product) ? 0 : 1 }}';
    window.quick_view_campaign_expired     = '{{ isset($campaign_active) ? $campaign_active : 0 }}';
    window.quick_view_product_id           = {{ $product->id }};
    window.quick_view_selected_variant     = '';
</script>
