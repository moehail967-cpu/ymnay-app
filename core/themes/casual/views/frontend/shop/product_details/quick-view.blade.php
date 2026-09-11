@php
    $data = get_product_dynamic_price($product);
    $campaign_name      = $data['campaign_name'];
    $data_regular_price = $data['regular_price'];
    $data_sale_price    = $data['sale_price'];
    $discount           = $data['discount'];
    $campaign_product   = $product?->campaign_product;
    $sale_price         = $data_sale_price;
    $deleted_price      = $data_regular_price;
    $campaign_percentage = $discount;

    $stock_count = $campaign_product
        ? ($campaign_product->units_for_sale !== null
            ? max(0, $campaign_product->units_for_sale - (int) $campaign_product->sold_count)
            : null)
        : optional($product->inventory)->stock_count;
    $stock_count = $stock_count > 0 ? $stock_count : 0;

    if ($campaign_product) {
        $campaign_title  = \Modules\Campaign\Entities\Campaign::select('id','title')->where('id', $campaign_product?->id)->first();
        $campaign_active = $data['campaign_active'];
    }

    $quickView   = true;
    $image_details = get_attachment_image_by_id($product->image_id, 'full');
@endphp

<div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content cs-qv-modal">
        <button class="cs-qv-close quick-view-close-btn" type="button">
            <i class="las la-times"></i>
        </button>
        <div class="row g-0">

            {{-- Image --}}
            <div class="col-lg-5">
                <div class="cs-qv-img-wrap" id="shop_details_gallery_slider">
                    <div class="shop-details-thums long-img" data-src="{{ $image_details['img_url'] ?? '' }}">
                        <img id="cs-qv-main-img" src="{{ $image_details['img_url'] ?? '' }}" alt="{{ $product->name }}" loading="lazy">
                    </div>
                    @if(!empty($discount))
                    <span class="cs-product-badge cs-product-badge-sale">{{ $discount }}% {{ __('Off') }}</span>
                    @endif
                    @if(!empty($product->badge))
                    <span class="cs-product-badge cs-product-badge-new">{{ $product->badge->name }}</span>
                    @endif
                </div>
            </div>

            {{-- Details --}}
            <div class="col-lg-7">
                <div class="cs-qv-body quick-view-shop-wrapper">
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
