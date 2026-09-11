@php
    $colors        = $colors ?? collect();
    $sizes         = $sizes ?? collect();
    $allAttributes = $allAttributes ?? collect();
    $product       = $product ?? null;

    // Existing variant rows (for edit page)
    // Mirror the frontend's auto-detection: treat as variant product if handle_variants=1
    // OR if any detail row has a non-null hash (saved via the new combination grid).
    $existingVariants = [];
    if ($product && $product->inventoryDetail) {
        $hasNewStyleRows = $product->handle_variants
            || $product->inventoryDetail->contains(fn($d) => !is_null($d->hash));

        if ($hasNewStyleRows) {
            foreach ($product->inventoryDetail as $v) {
                $existingVariants[] = [
                    'id'         => $v->id,
                    'color'      => $v->color,
                    'size'       => $v->size,
                    'sku'        => $v->sku ?? '',
                    'price'      => $v->additional_price ?? 0,
                    'cost'       => $v->add_cost ?? 0,
                    'stock'      => $v->stock_count ?? 0,
                    'image'      => $v->image ?? '',
                    'attributes' => is_array($v->attributes) ? $v->attributes : (is_string($v->attributes) ? json_decode($v->attributes, true) ?? [] : []),
                ];
            }
        }
    }
@endphp

{{-- Pass existing variants to JS --}}
<script>window.existingVariantsData = @json($existingVariants);</script>

{{-- Variant combination section — visible when handle_variants is on OR when the
     product already has variant rows (mirrors the inventory blade auto-detection) --}}
@php
    $variantsSectionVisible = !empty(old('handle_variants', $product
        ? ($product->handle_variants || ($product->inventoryDetail?->count() > 0) ? 1 : 0)
        : 0));
@endphp
<div id="variants_section" class="{{ $variantsSectionVisible ? '' : 'hidden' }} space-y-6 mt-6">

    <h4 class="product-section-title">{{ __('Product Variants') }}</h4>

    {{-- Axis selectors: Color + Size --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Colors --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                {{ __('Colors') }}
            </label>
            <div class="flex flex-wrap gap-2" id="color_picker_wrap">
                @foreach($colors as $color)
                    <label class="inline-flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-main text-xs font-medium select-none transition-all hover:border-primary color-option"
                           data-id="{{ $color->id }}" data-name="{{ $color->name }}">
                        <input type="checkbox" class="color-axis-check sr-only" value="{{ $color->id }}"
                               data-name="{{ $color->name }}">
                        @if($color->color_code)
                            <span class="w-3.5 h-3.5 rounded-full border border-gray-300 inline-block"
                                  style="background:{{ $color->color_code }}"></span>
                        @endif
                        {{ $color->name }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Sizes --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                {{ __('Sizes') }}
            </label>
            <div class="flex flex-wrap gap-2" id="size_picker_wrap">
                @foreach($sizes as $size)
                    <label class="inline-flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-main text-xs font-medium select-none transition-all hover:border-primary size-option"
                           data-id="{{ $size->id }}" data-name="{{ $size->name }}">
                        <input type="checkbox" class="size-axis-check sr-only" value="{{ $size->id }}"
                               data-name="{{ $size->name }}">
                        {{ $size->name }}
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Custom Attribute Axes (RAM, Storage, Material, etc.) --}}
    <div class="p-4 rounded-xl border border-main bg-surface space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-dark">{{ __('Custom Attribute Axes') }}</span>
                <span class="text-[10px] text-muted ml-1.5">{{ __('Add dimensions like RAM, Storage, Material, etc.') }}</span>
            </div>
        </div>

        {{-- Inline axis builder — always visible --}}
        <div class="flex flex-wrap items-center gap-2">
            <input type="text" id="inline_axis_name" placeholder="{{ __('Name  e.g. RAM') }}"
                   class="border border-main rounded-lg px-3 py-1.5 text-xs text-dark bg-white outline-none focus:border-primary w-36">
            <input type="text" id="inline_axis_terms" placeholder="{{ __('Values: 8GB, 16GB, 32GB') }}"
                   class="border border-main rounded-lg px-3 py-1.5 text-xs text-dark bg-white outline-none focus:border-primary w-56">
            <button type="button" onclick="addInlineAxis()"
                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary text-white text-xs font-semibold hover:opacity-90">
                <i class="mdi mdi-plus"></i> {{ __('Add Axis') }}
            </button>
        </div>

        {{-- Pre-built attributes (from Attributes module) — always shown if any exist --}}
        @if($allAttributes->count())
        <div>
            <p class="text-[10px] text-muted uppercase tracking-widest font-semibold mb-1.5">{{ __('Or pick from saved attributes') }}</p>
            <div class="flex flex-wrap gap-2" id="custom_axes_picker">
                @foreach($allAttributes as $attr)
                    @php
                        $decoded = json_decode($attr->terms ?? '[]', true);
                        $terms = is_array($decoded)
                            ? array_values(array_filter(array_map('trim', $decoded)))
                            : array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $attr->terms ?? ''))));
                    @endphp
                    <label class="custom-axis-pill inline-flex items-center gap-1.5 cursor-pointer px-3 py-1.5 rounded-lg border border-main text-xs font-medium select-none transition-all hover:border-primary bg-white"
                           data-attr-name="{{ $attr->title }}"
                           data-terms="{{ json_encode($terms) }}">
                        <input type="checkbox" class="custom-axis-toggle sr-only"
                               value="{{ $attr->id }}"
                               data-name="{{ $attr->title }}"
                               data-terms="{{ json_encode($terms) }}">
                        <i class="mdi mdi-plus text-muted text-sm custom-axis-icon"></i>
                        {{ $attr->title }}
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Active axis value chips render here --}}
        <div id="custom_axis_values_wrap" class="space-y-2"></div>
    </div>

    {{-- Apply-to-all row --}}
    <div class="flex flex-wrap items-center gap-3 p-3 bg-surface border border-main rounded-xl text-xs">
        <span class="font-semibold text-dark">{{ __('Apply to all rows:') }}</span>
        <div class="flex items-center gap-2">
            <input type="number" id="bulk_price" placeholder="{{ __('Price') }}" step="0.01" min="0"
                   class="w-24 bg-white border border-main rounded-lg px-2 py-1.5 text-xs outline-none focus:border-primary">
            <button type="button" onclick="applyToAll('v_price', 'bulk_price')"
                    class="px-2 py-1.5 bg-primary text-white rounded-lg text-xs hover:opacity-90">{{ __('Price') }}</button>
        </div>
        <div class="flex items-center gap-2">
            <input type="number" id="bulk_cost" placeholder="{{ __('Cost') }}" step="0.01" min="0"
                   class="w-24 bg-white border border-main rounded-lg px-2 py-1.5 text-xs outline-none focus:border-primary">
            <button type="button" onclick="applyToAll('v_cost', 'bulk_cost')"
                    class="px-2 py-1.5 bg-primary text-white rounded-lg text-xs hover:opacity-90">{{ __('Cost') }}</button>
        </div>
        <div class="flex items-center gap-2">
            <input type="number" id="bulk_stock" placeholder="{{ __('Stock') }}" min="0"
                   class="w-24 bg-white border border-main rounded-lg px-2 py-1.5 text-xs outline-none focus:border-primary">
            <button type="button" onclick="applyToAll('v_stock', 'bulk_stock')"
                    class="px-2 py-1.5 bg-primary text-white rounded-lg text-xs hover:opacity-90">{{ __('Stock') }}</button>
        </div>
    </div>

    {{-- Combination grid — thead rebuilt by JS --}}
    <div class="overflow-x-auto">
        <table class="w-full text-xs border-collapse" id="variant_combo_table">
            <thead id="variant_combo_head">
                {{-- Rebuilt dynamically by JS based on active axes --}}
            </thead>
            <tbody id="variant_combo_body">
                {{-- Rows generated by JS from existingVariantsData --}}
            </tbody>
        </table>
        <p id="variant_empty_msg" class="text-center text-muted text-xs py-6">
            {{ __('Select colors, sizes, or custom attribute axes above to generate variant combinations.') }}
        </p>
    </div>
</div>

{{-- Custom Specifications --}}
<div class="mt-8">
    <h4 class="product-section-title">
        <i class="mdi mdi-tag-multiple-outline mr-2"></i>{{ __('Custom Product Specifications') }}
        <span class="text-xs text-muted font-normal ml-1">({{ __('Optional') }})</span>
    </h4>

    <div class="custom_specifications_container">
        @php
            $specifications = collect();
            if (old('custom_spec_title')) {
                $specifications = collect(old('custom_spec_title'))->map(function ($title, $i) {
                    return ['title' => $title, 'value' => old('custom_spec_value')[$i] ?? ''];
                });
            } elseif (isset($product) && $product->custom_specifications) {
                $specifications = $product->custom_specifications->map(fn($s) => ['title' => $s->title, 'value' => $s->value]);
            }
        @endphp

        @forelse($specifications as $index => $spec)
            <div class="custom_specification_item custom-spec-item" data-id="{{ $index }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_2fr_auto] gap-4 items-end">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Specification Title') }}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <input type="text" name="custom_spec_title[]" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                                   value="{{ $spec['title'] ?? '' }}" placeholder="{{ __('e.g., Fabric Type') }}" maxlength="100">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Specification Value') }}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <input type="text" name="custom_spec_value[]" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                                   placeholder="{{ __('e.g., 65% Cotton') }}" value="{{ $spec['value'] ?? '' }}" maxlength="500">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="variant-repeater-btn add add_custom_spec"><i class="mdi mdi-plus"></i></button>
                        <button type="button" class="variant-repeater-btn remove remove_custom_spec" {{ $loop->first ? 'style=display:none;' : '' }}><i class="mdi mdi-trash-can-outline"></i></button>
                    </div>
                </div>
            </div>
        @empty
            <div class="custom_specification_item custom-spec-item" data-id="0">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_2fr_auto] gap-4 items-end">
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Specification Title') }}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <input type="text" name="custom_spec_title[]" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                                   placeholder="{{ __('e.g., Fabric Type') }}" maxlength="100">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __('Specification Value') }}</label>
                        <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                            <input type="text" name="custom_spec_value[]" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0"
                                   placeholder="{{ __('e.g., 65% Cotton') }}" maxlength="500">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="variant-repeater-btn add add_custom_spec"><i class="mdi mdi-plus"></i></button>
                        <button type="button" class="variant-repeater-btn remove remove_custom_spec" style="display:none;"><i class="mdi mdi-trash-can-outline"></i></button>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- Navigation Buttons --}}
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

<style>
.variant-input {
    background: #fff;
    border: 1px solid var(--border-color, #e5e7eb);
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 12px;
    outline: none;
}
.variant-input:focus { border-color: var(--primary-color, #6366f1); }
.color-option.selected, .size-option.selected {
    border-color: var(--primary-color, #6366f1);
    background: rgba(99,102,241,.06);
    color: var(--primary-color, #6366f1);
}
.custom-axis-pill.selected {
    border-color: var(--primary-color, #6366f1);
    background: rgba(99,102,241,.06);
    color: var(--primary-color, #6366f1);
}
.custom-axis-pill.selected .custom-axis-icon { transform: rotate(45deg); }
.custom-axis-icon { transition: transform 0.15s; }
.custom-val-chip { display: inline-flex; align-items: center; gap: 4px; cursor: pointer; padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border-color,#e5e7eb); font-size: 11px; font-weight: 500; transition: all 0.15s; }
.custom-val-chip:hover { border-color: var(--primary-color,#6366f1); }
.custom-val-chip.selected { border-color: var(--primary-color,#6366f1); background: rgba(99,102,241,.06); color: var(--primary-color,#6366f1); }
</style>
