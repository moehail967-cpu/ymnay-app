<div class="space-y-4">
    <h4 class="product-section-title">{{ __("Product Shipping and Return Policy") }}</h4>

    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Policy Description") }}</label>
        <textarea class="summernote" name="policy_description">{!! isset($product) ? purify_html($product?->return_policy?->shipping_return_description) : "" !!}</textarea>
    </div>
</div>
