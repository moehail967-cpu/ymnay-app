<div>
    <h4 class="product-section-title">{{ __("Refund Policy") }}</h4>
    <div class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Policy Description") }}</label>
            <textarea class="lnd-input summernote" name="policy_description" placeholder="{{ __("Type Description") }}">{!! isset($product) ? purify_html($product?->refund_policy?->refund_description) : "" !!}</textarea>
        </div>
    </div>
</div>
