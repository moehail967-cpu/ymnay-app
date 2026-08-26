@php
    if(!isset($inventorydetails)){
        $inventorydetails = [];
    }
@endphp

<div class="variant_info_container">
    <h4 class="product-section-title">{{ __('Custom Inventory variant') }}</h4>

    <div class="flex items-start gap-3 p-4 rounded-xl bg-info-soft border border-main text-sm text-dark mb-4">
        <i class="mdi mdi-information-outline text-lg mt-0.5"></i>
        <div>
            {{ __('Inventory will be variant of this product.') }}<br>
            {{ __('All inventory stock count will be merged and replace to main stock of this product.') }}<br>
            {{ __('Stock count filed is required.') }}
        </div>
    </div>

    <div class="inventory_items_container">
        @forelse($inventorydetails as $key => $detail)
            <x-product::variant-info.repeater
                :detail="$detail"
                :is-first='false' :colors="$colors" :sizes="$sizes"
                :all-available-attributes="$allAttributes" :key="$key" />
        @empty
            <x-product::variant-info.repeater
                    :is-first="true" :colors="$colors" :sizes="$sizes"
                    :all-available-attributes="$allAttributes" />
        @endforelse
    </div>
</div>
