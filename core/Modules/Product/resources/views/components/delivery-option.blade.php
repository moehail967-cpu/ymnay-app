@php
    if(!isset($selectedDeliveryOption)){
        $selectedDeliveryOption = [];
    }
@endphp

<div class="space-y-4">
    <h4 class="product-section-title">{{ __('Delivery Options') }}</h4>

    <div>
        <input type="hidden" value="{{ implode(" , ", $selectedDeliveryOption) }}" name="delivery_option" class="delivery-option-input" />
        <div class="flex flex-wrap gap-2">
            @foreach($deliveryOptions as $deliveryOption)
                <div class="delivery-option-item {{ in_array($deliveryOption->id, $selectedDeliveryOption) ? 'active' : '' }}" data-delivery-option-id="{{ $deliveryOption->id }}">
                    <i class="{{ $deliveryOption->icon }} text-base"></i>
                    <div>
                        <span class="block text-sm font-semibold">{{ $deliveryOption->title }}</span>
                        <span class="block text-xs text-muted">{{ $deliveryOption->sub_title }}</span>
                    </div>
                </div>
            @endforeach
        </div>
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
