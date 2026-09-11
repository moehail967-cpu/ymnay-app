@php
    $inventory = $inventory ?? null;
    $uom       = $uom ?? null;
    // Auto-detect: treat as "has variants" if the flag is set OR the product already has variant rows
    $hasVariants = old('handle_variants', isset($product)
        ? ($product->handle_variants || ($product->inventoryDetail?->count() > 0))
        : 0);
@endphp

<div class="space-y-4">
    <h4 class="product-section-title">{{ __('Product Inventory') }}</h4>

    {{-- Barcode --}}
    <div>
        @if($inventory)
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($inventory->sku, 'C128') }}" alt="">
        @else
            <p class="text-xs text-muted">{{ __('A barcode will be generated after creating a SKU') }}</p>
        @endif
    </div>

    {{-- SKU --}}
    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('SKU') }} <x-fields.mandatory-indicator/></label>
        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
            <i class="mdi mdi-barcode text-primary"></i>
            <input type="text" name="sku" value="{{ $inventory?->sku }}"
                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                   placeholder="{{ __('Enter SKU') }}">
        </div>
        <span class="product-error-msg sku-error"></span>
    </div>

    {{-- Handle Variants toggle --}}
    <div class="flex items-center gap-3 p-4 rounded-xl bg-surface border border-main">
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="handle_variants" id="handle_variants_toggle" value="1"
                   {{ $hasVariants ? 'checked' : '' }} class="sr-only peer">
            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
        </label>
        <div>
            <p class="text-sm font-semibold text-dark">{{ __('Handle Variants') }}</p>
            <p class="text-xs text-muted">{{ __('Enable to define color/size combinations with individual prices, costs, and stock levels.') }}</p>
        </div>
    </div>

    {{-- Simple quantity (shown when handle_variants = off) --}}
    <div id="simple_quantity_wrap" class="{{ $hasVariants ? 'hidden' : '' }}">
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Quantity') }}</label>
        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
            <i class="mdi mdi-package-variant text-primary"></i>
            <input type="number" name="quantity" value="{{ old('quantity', $inventory?->stock_count) }}"
                   class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                   placeholder="{{ __('Enter Quantity') }}">
        </div>
        <span class="product-error-msg quantity-error"></span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Unit --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Unit') }} <x-fields.mandatory-indicator/></label>
            <select name="unit_id" class="lnd-input">
                <option value="">{{ __('Select Unit') }}</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id', $uom?->unit_id) == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
            <span class="product-error-msg unit_id-error"></span>
        </div>

        {{-- UOM --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Unit Of Measurement') }} <x-fields.mandatory-indicator/></label>
            <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                <i class="mdi mdi-ruler text-primary"></i>
                <input type="number" name="uom" value="{{ old('uom', $uom?->quantity) }}"
                       class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                       placeholder="{{ __('Enter Unit Of Measurement') }}">
            </div>
            <span class="product-error-msg uom-error"></span>
        </div>
    </div>
</div>
