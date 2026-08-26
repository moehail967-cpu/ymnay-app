<div class="space-y-4">
    <h4 class="product-section-title">{{ __("Product Shipping and Return Policy") }}</h4>

    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Policy Description") }}</label>
        <textarea class="summernote" name="policy_description">{!! isset($product) ? purify_html($product?->return_policy?->shipping_return_description) : "" !!}</textarea>
        <span class="product-error-msg policy_description-error"></span>
    </div>
</div>

<div class="product-nav-buttons">
    <button type="button" class="prev-step inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-dark bg-secondary border border-main hover:border-hover transition">
        <i class="mdi mdi-arrow-left"></i> {{ __('Previous') }}
    </button>
    <button type="button" class="next-step inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold text-white bg-primary hover:opacity-90 transition">
        {{ __('Next') }} <i class="mdi mdi-arrow-right"></i>
    </button>
    <button type="submit" class="submit-form inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-success hover:opacity-90 transition" style="display: none;">
        <i class="mdi mdi-check"></i> {{ isset($product) ? __('Update Product') : __('Create Product') }}
    </button>
</div>
