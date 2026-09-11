@php
    if(!isset($selectedDeliveryOption)){
        $selectedDeliveryOption = [];
    }
@endphp

<div class="space-y-4 px-4">
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
