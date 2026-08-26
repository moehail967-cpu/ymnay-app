{{--
    # Params/Variables
        $key, $colors, $sizes, $selected_color, $selected_size, $isFirst,
        $allAvailableAttributes, $inventoryDetail
--}}
@php
    if(!isset($detail)){ $detail = null; }
@endphp

<div class="inventory_item mb-4 rounded-xl p-4" style="border: 1px solid var(--color-border-main); background: var(--color-bg-surface);" @if(isset($key)) data-id="{{ $key }}" @endif>
    @if(isset($inventoryDetail) && !is_null($inventoryDetail))
        <input type="hidden" name="inventory_details_id[]" value="{{ $inventoryDetail->id }}"/>
    @endif
    <div class="flex gap-3">
        <div class="flex-1">
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Item Size') }}</label>
                    <select name="item_size[]" class="lnd-input product-inventory-variant-select">
                        <option value="">{{ __('Select Size') }}</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}" @if(isset($detail) && $detail->size == $size->id) selected @endif>{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Item Color') }}</label>
                    <select name="item_color[]" class="lnd-input product-inventory-variant-select">
                        <option value="">{{ __('Select Color') }}</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}" @if(isset($detail) && $detail->color == $color->id) selected @endif>{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Additional Price') }}</label>
                    <input type="number" step="0.01" name="item_additional_price[]" class="lnd-input" min="0" placeholder="{{ __('Additional price') }}" value="{{ $detail?->additional_price ?? 0 }}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Extra cost') }}</label>
                    <input type="number" name="item_extra_cost[]" class="lnd-input" min="0" placeholder="{{ __('Extra cost') }}" value="{{ $detail?->add_cost ?? 0 }}">
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Stock Count') }} <span class="text-danger">*</span></label>
                    <input type="number" name="item_stock_count[]" class="lnd-input" min="0" placeholder="{{ __('Stock Count') }}" value="{{ $detail->stock_count ?? 0 }}">
                </div>
                <div>
                    @php
                        $image = isset($detail?->attr_image) ? $detail?->attr_image ?? '' : '';
                    @endphp
                    <x-fields.tw-media-upload :id="$image->id ?? ''" :title="__('Attribute Image')" name="item_image[]" dimentions="1280x1280"/>
                </div>
            </div>
            <div class="item_selected_attributes mt-3">
                @if(isset($detail) && !is_null($detail) && !is_null($detail->attribute))
                    @foreach($detail->attribute as $attribute)
                        <div class="grid grid-cols-[1fr_1fr_auto] gap-2 items-center mb-2">
                            <input type="text" class="lnd-input" name="item_attribute_name[{{ $key }}][]" value="{{ $attribute->attribute_name }}" readonly/>
                            <input type="text" class="lnd-input" name="item_attribute_value[{{ $key }}][]" value="{{ $attribute->attribute_value }}" readonly/>
                            <button class="variant-repeater-btn remove remove_details_attribute" data-id="{{ $attribute->id }}">x</button>
                        </div>
                    @endforeach
                @endif
            </div>
            <div class="grid grid-cols-[1fr_1fr_auto] gap-2 items-end mt-2">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Variant Name') }}</label>
                    <select name="item_attribute_name[]" class="lnd-input item_attribute_name">
                        <option value="">{{ __('Select Attribute') }}</option>
                        @foreach ($allAvailableAttributes as $name => $attribute)
                            <option value="{{ $attribute->id }}" data-terms="{{ $attribute->terms }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Variant Value') }}</label>
                    <select name="item_attribute_value[]" class="lnd-input item_attribute_value">
                        <option value="">{{ __('Select variant value') }}</option>
                    </select>
                </div>
                <button type="button" class="variant-repeater-btn add add_item_attribute mt-1"><i class="mdi mdi-arrow-up"></i></button>
            </div>
        </div>
        <div class="flex flex-col gap-1.5 pt-6">
            <button type="button" class="variant-repeater-btn add"><i class="mdi mdi-plus"></i></button>
            @if(!isset($isFirst) || !$isFirst)
                <button type="button" class="variant-repeater-btn remove"><i class="mdi mdi-delete-outline"></i></button>
            @endif
        </div>
    </div>
</div>
