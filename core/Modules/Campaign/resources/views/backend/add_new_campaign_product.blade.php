<div class="cp-card bg-surface rounded-xl border border-main mb-4">
    {{-- Header --}}
    <div class="flex items-center justify-between px-4 py-2.5 bg-secondary border-b border-main rounded-t-xl">
        <span class="text-xs font-bold text-dark flex items-center gap-1.5">
            <i class="mdi mdi-package-variant-closed text-primary text-sm"></i>
            {{ __('Product') }}
        </span>
        <div class="flex items-center gap-2">
            <span class="campaign-discount-badge product_percentage" style="display:none"></span>
            @if(isset($remove_btn))
                <button type="button" class="cross-btn w-6 h-6 rounded-md flex items-center justify-center text-muted hover:bg-danger-soft hover:text-danger transition">
                    <i class="mdi mdi-close text-sm"></i>
                </button>
            @endif
        </div>
    </div>
    {{-- Body --}}
    <div class="p-4 space-y-3">
        {{-- Product Select --}}
        <div class="select_product">
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Select Product') }}</label>
            <select name="product_id[]" class="lnd-input repeater_product_id">
                <option value="">{{ __("-- Choose a product --") }}</option>
                @foreach ($all_products as $product)
                    <option value="{{ $product->id }}"
                            data-price="{{ $product->price }}"
                            data-sale_price="{{ $product->sale_price }}"
                            data-stock="{{ optional($product->inventory)->stock_count ?? 0 }}">
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        {{-- Price & Units Grid --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Original Price') }}</label>
                <input type="number" class="lnd-input original_price product_original_price" disabled placeholder="—">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Campaign Price') }} <span class="text-danger">*</span></label>
                <input type="number" name="campaign_price[]" class="lnd-input campaign_price" step="0.01" placeholder="0.00">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Stock') }}</label>
                <input type="number" class="lnd-input available_num_of_units" disabled placeholder="—">
            </div>
            <div>
                <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1">{{ __('Units for Sale') }} <span class="text-danger">*</span></label>
                <input type="number" name="units_for_sale[]" class="lnd-input units_for_sale" placeholder="0">
            </div>
        </div>
    </div>
</div>
