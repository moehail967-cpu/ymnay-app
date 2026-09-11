{{--
    # Params/Variables
        $key
        $colors
        $sizes
        $selected_color
        $selected_size
        $isFirst
        $allAvailableAttributes
        $inventoryDetail
--}}
@php
    if(!isset($detail)){
        $detail = null;
    }
@endphp

<div class="inventory_item product-variant-card" @if(isset($key)) data-id="{{ $key }}" @endif>
    @if(isset($inventoryDetail) && !is_null($inventoryDetail))
        <input type="hidden" name="inventory_details_id[]" value="{{ $inventoryDetail->id }}"/>
    @endif

    <div class="flex gap-4">
        <div class="flex-1">
            {{-- Main Fields Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Item Size') }}</label>
                    <select name="item_size[]" class="lnd-input product-inventory-variant-select">
                        <option value="">{{ __('Select Size') }}</option>
                        @foreach($sizes as $size)
                            <option value="{{ $size->id }}"
                                    @if(isset($detail) && $detail->size == $size->id) selected @endif>{{ $size->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Item Color') }}</label>
                    <select name="item_color[]" class="lnd-input product-inventory-variant-select">
                        <option value="">{{ __('Select Color') }}</option>
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}"
                                    @if(isset($detail) && $detail->color == $color->id) selected @endif>{{ $color->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Additional Price') }}</label>
                    <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <input type="number" step="0.01" name="item_additional_price[]"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                               min="0" placeholder="{{ __('Additional price') }}"
                               value="{{ $detail?->additional_price ?? 0 }}">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Extra cost') }}</label>
                    <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <input type="number" name="item_extra_cost[]"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                               min="0" placeholder="{{ __('Extra cost') }}"
                               value="{{ $detail?->add_cost ?? 0 }}">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Stock Count') }} <i class="mdi mdi-star text-danger text-[8px]"></i></label>
                    <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <input type="number" name="item_stock_count[]"
                               class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                               min="0" placeholder="{{ __('Stock Count') }}"
                               value="{{ $detail->stock_count ?? 1 }}">
                    </div>
                </div>
                <div>
                    @php
                        $image = $detail?->attr_image ?? null;
                        $mediaId = is_object($image) ? ($image->id ?? '') : '';
                        $imgData = $mediaId ? get_attachment_image_by_id($mediaId) : null;
                        $imgUrl = (!empty($imgData) && !empty($imgData['img_url'])) ? $imgData['img_url'] : null;
                        $uniqueMediaId = 'item_image_' . ($key ?? uniqid()) . '_section';
                    @endphp
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Attribute Image') }}</label>
                    <div class="tw-media-upload-wrapper variant-media-upload" id="{{ $uniqueMediaId }}">
                        <div class="tw-img-wrap mb-2">
                            @if($imgUrl)
                                <div class="tw-attachment-preview relative inline-block">
                                    <img src="{{ $imgUrl }}" alt="" class="tw-preview-img w-16 h-16 rounded-lg object-cover border border-gray-200" />
                                    <button type="button" class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">&times;</button>
                                </div>
                            @else
                                <div class="tw-attachment-preview"></div>
                            @endif
                        </div>
                        <input type="hidden" name="item_image[]" class="tw-media-id-input" value="{{ $mediaId }}">
                        <button type="button"
                                class="tw-media-open-btn inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-primary hover:opacity-90 transition"
                                data-target="{{ $uniqueMediaId }}"
                                data-imgid="{{ $mediaId }}">
                            <i class="mdi mdi-image-outline text-sm"></i>
                            {{ $imgUrl ? __('Change') : __('Upload') }}
                        </button>
                        <p class="mt-1 text-[10px] text-gray-400">1280x1280</p>
                    </div>
                </div>
            </div>

            {{-- Selected Attributes --}}
            <div class="item_selected_attributes mt-3 space-y-2">
                @if(isset($detail) && !is_null($detail) && !is_null($detail->attribute))
                    @foreach($detail->attribute as $attribute)
                        <div class="grid grid-cols-[1fr_1fr_auto] gap-4 items-center">
                            <div>
                                <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2">
                                    <input type="text" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0" name="item_attribute_name[{{ $key }}][]"
                                           value="{{ $attribute->attribute_name }}" readonly/>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2">
                                    <input type="text" class="flex-1 bg-transparent text-sm text-dark outline-none border-none focus:ring-0 p-0" name="item_attribute_value[{{ $key }}][]"
                                           value="{{ $attribute->attribute_value }}" readonly/>
                                </div>
                            </div>
                            <div>
                                <button type="button" class="variant-repeater-btn remove remove_details_attribute" data-id="{{ $attribute->id }}">
                                    <i class="mdi mdi-close"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Attribute Selection Row --}}
            <div class="grid grid-cols-[1fr_1fr_auto] gap-4 items-end mt-3 select-attribute-wrapper">
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Variant Name') }}</label>
                    <select name="item_attribute_name[]" class="lnd-input item_attribute_name">
                        <option value="">{{ __('Select Variant') }}</option>
                        @foreach ($allAvailableAttributes as $name => $attribute)
                            <option value="{{ $attribute->id }}"
                                    data-terms="{{ $attribute->terms }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Variant Value') }}</label>
                    <select name="item_attribute_value[]" class="lnd-input item_attribute_value">
                        <option value="">{{ __('Select Variant value') }}</option>
                    </select>
                </div>
                <div>
                    <button type="button" class="variant-repeater-btn add add_item_attribute">
                        <i class="mdi mdi-arrow-up"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Add/Remove Repeater Buttons --}}
        <div class="flex flex-col gap-2 pt-6">
            <div class="repeater_button">
                <button type="button" class="variant-repeater-btn add"><i class="mdi mdi-plus"></i></button>
            </div>
            @if(!isset($isFirst) || !$isFirst)
                <div class="repeater_button">
                    <button type="button" class="variant-repeater-btn remove"><i class="mdi mdi-trash-can-outline"></i></button>
                </div>
            @endif
        </div>
    </div>
</div>
