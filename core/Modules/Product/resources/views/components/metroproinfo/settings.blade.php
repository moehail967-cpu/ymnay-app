@php
    if(!isset($product)){
        $product = null;
    }
@endphp

<div class="space-y-4 px-3">
    <h4 class="product-section-title">{{ __("Product Settings") }}</h4>

    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Minimum quantity of Purchase") }}</label>
        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
            <i class="mdi mdi-cart-minus text-primary"></i>
            <input type="text" id="min_purchase" name="min_purchase" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" value="{{ $product?->min_purchase }}" placeholder="{{ __("Minimum quantity of purchase") }}">
        </div>
    </div>

    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Maximum quantity of Purchase") }}</label>
        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
            <i class="mdi mdi-cart-plus text-primary"></i>
            <input type="text" id="max_purchase" name="max_purchase" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" value="{{ $product?->max_purchase }}" placeholder="{{ __("Maximum quantity of Purchase") }}">
        </div>
    </div>

    <div class="flex items-center gap-3">
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{ __("Inventory Warning") }}</label>
        <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" name="is_inventory_warning" class="sr-only peer" value="1" {{ $product?->is_inventory_warn_able ? "checked" : "" }}>
            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-primary transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full"></div>
        </label>
    </div>
</div>
