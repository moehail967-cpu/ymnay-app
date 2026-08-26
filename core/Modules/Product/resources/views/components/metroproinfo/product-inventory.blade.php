@php
    if(!isset($inventory)){
        $inventory = null;
    }

    if(!isset($uom)){
        $uom = null;
    }
@endphp

<div class="space-y-4">
    <h4 class="product-section-title">{{ __('Product Inventory') }}</h4>

    {{-- Barcode Display --}}
    <div>
        @if(!$inventory)
            <p class="text-xs text-muted">{{ __('A barcode will be generated after creating a SKU') }}</p>
        @endif
        @if($inventory)
            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($inventory?->sku, 'C128') }}" alt="">
        @endif
    </div>

    {{-- SKU --}}
    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('SKU') }}</label>
        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
            <i class="mdi mdi-barcode text-primary"></i>
            <input type="text" name="sku" value="{{ $inventory?->sku }}" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" placeholder="{{ __('Enter SKU') }}">
        </div>
        <p class="text-xs text-muted mt-1">{{ __('Custom Unique Code for this product.') }}</p>
    </div>

    {{-- Quantity --}}
    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Quantity') }}</label>
        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
            <i class="mdi mdi-package-variant text-primary"></i>
            <input type="tel" name="quantity" value="{{ $inventory?->stock_count }}" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" placeholder="{{ __('Enter Quantity') }}">
        </div>
        <p class="text-xs text-muted mt-1">{{ __('This will be replaced with the sum of inventory items. if any inventory item is registered.') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {{-- Unit --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Unit') }}</label>
            <select name="unit_id" class="lnd-input">
                <option value="">{{ __('Select Unit') }}</option>
                @foreach($units as $unit)
                    <option
                        value="{{ $unit->id }}"
                        {{ $unit->id === $uom?->unit_id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
            <p class="text-xs text-muted mt-1">{{ __('Select Unit') }}</p>
        </div>

        {{-- Unit of Measurement --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Unit Of Measurement') }}</label>
            <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                <i class="mdi mdi-ruler text-primary"></i>
                <input type="number" name="uom" value="{{ $uom?->quantity }}" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" placeholder="{{ __('Enter Unit Of Measurement') }}">
            </div>
            <p class="text-xs text-muted mt-1">{{ __('Enter the number here') }}</p>
        </div>
    </div>
</div>
