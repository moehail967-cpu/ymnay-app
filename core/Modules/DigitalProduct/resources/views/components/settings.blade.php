@php
    if(!isset($product)){
        $product = null;
    }
@endphp

<div>
    <h4 class="product-section-title">{{ __("Product Settings") }}</h4>
    <div class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Minimum quantity of Purchase") }}</label>
            <input id="min_purchase" name="min_purchase" class="lnd-input" value="{{ $product?->min_purchase }}" placeholder="{{ __("Minimum quantity of purchase") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Maximum quantity of Purchase") }}</label>
            <input id="max_purchase" name="max_purchase" class="lnd-input" value="{{ $product?->max_purchase }}" placeholder="{{ __("Maximum quantity of Purchase") }}">
        </div>
    </div>
</div>
