@php
    if(!isset($product)){
        $product = null;
    }
@endphp

<div class="general-info-wrapper">
    <h4 class="product-section-title">{{ __('Price Manage') }}</h4>

    <div class="space-y-4">
        {{-- Base Cost --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Base Cost") }}</label>
            <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                <i class="mdi mdi-cash-multiple text-primary"></i>
                <input type="number" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" value="{{ $product?->cost }}" name="cost" placeholder="{{ __("Base Cost...") }}" step="0.01">
            </div>
            <p class="text-xs text-muted mt-1">{{ __("Purchase price of this product.") }}</p>
        </div>

        {{-- Regular Price --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Regular Price") }}</label>
            <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                <i class="mdi mdi-currency-usd text-primary"></i>
                <input type="number" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" value="{{ $product?->price }}" name="price" placeholder="{{ __("Enter Regular Price...") }}" step="0.01">
            </div>
            <p class="text-xs text-muted mt-1">{{ __("This price will display like this") }} <del>({{ site_currency_symbol() }} 10)</del></p>
            <span class="product-error-msg price-error"></span>
        </div>

        {{-- Sale Price --}}
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Sale Price") }} <x-fields.mandatory-indicator/></label>
            <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                <i class="mdi mdi-sale text-primary"></i>
                <input type="number" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" value="{{ $product?->sale_price }}" name="sale_price" placeholder="{{ __("Enter Sale Price...") }}" step="0.01">
            </div>
            <p class="text-xs text-muted mt-1">{{ __("This will be your product selling price") }}</p>
        </div>

        {{-- Taxable & Tax Class --}}
        @if($product)
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="is_taxable_wrapper">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Is Taxable?") }}</label>
                    <select name="is_taxable" class="lnd-input">
                        <option @selected($product->is_taxable == 0) value="no">{{ __('No') }}</option>
                        <option @selected($product->is_taxable == 1) value="yes">{{ __('Yes') }}</option>
                    </select>
                </div>

                <div class="tax_classes_wrapper" @style(['display: none' => !$product->is_taxable])>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Tax classes") }}</label>
                    <select name="tax_class" class="lnd-input">
                        <option value="">{{ __('Select an option') }}</option>
                        @foreach($taxClasses ?? [] as $class)
                            <option @selected($product->tax_class_id == $class->id) value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="is_taxable_wrapper">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Is Taxable?") }}</label>
                    <select name="is_taxable" class="lnd-input">
                        <option value="no">{{ __('No') }}</option>
                        <option value="yes">{{ __('Yes') }}</option>
                    </select>
                </div>

                <div class="tax_classes_wrapper" style="display:none">
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Tax classes") }} <x-fields.mandatory-indicator/></label>
                    <select name="tax_class" class="lnd-input">
                        <option value="">{{ __('Select an option') }}</option>
                        @foreach($taxClasses ?? [] as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    <span class="product-error-msg tax_class-error"></span>
                </div>
            </div>
        @endif
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
</div>
