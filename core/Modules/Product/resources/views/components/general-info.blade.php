@php
    if(!isset($product)){
        $product = null;
    }
@endphp

<div class="general-info-wrapper">
    <h4 class="product-section-title">{{ __("General Information") }}</h4>

    <div class="general-info-form">
        <form action="#">
            <div class="space-y-4">
                {{-- Name --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Name") }} <x-fields.mandatory-indicator/></label>
                    <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-tag-text-outline text-primary"></i>
                        <input type="text" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" id="product-name" value="{!! $product?->name ?? "" !!}" name="name" placeholder="{{ __("Write product Name...") }}" required>
                    </div>
                    <span class="product-error-msg name-error"></span>
                </div>

                {{-- Slug --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                        {{ __("Slug") }} <x-fields.mandatory-indicator/>
                        <i class="mdi mdi-information-outline text-muted text-xs ml-1" data-bs-toggle="tooltip" title="URL-friendly version of the name"></i>
                    </label>

                    <div class="slug-field-wrapper">
                        {{-- Display Mode --}}
                        <div class="slug-display" id="slug-display">
                            {{ $product->slug ?? '' }}
                        </div>

                        {{-- Edit Mode (Hidden by default) --}}
                        <input type="text"
                               class="slug-input"
                               id="product-slug"
                               name="slug"
                               value="{{ $product->slug ?? '' }}"
                               style="display: none;"
                               pattern="^[\p{L}\p{N}]+(?:-[\p{L}\p{N}]+)*$"
                               title="Slug can only contain letters (any language), numbers, and hyphens"
                               lang="{{ app()->getLocale() }}">

                        {{-- Edit Icon --}}
                        <button type="button" class="slug-edit-icon" id="slug-edit-btn" title="Edit slug">
                            <i class="mdi mdi-pencil"></i>
                        </button>

                        {{-- Save/Cancel Actions (Hidden by default) --}}
                        <div class="slug-actions" style="display:none;">
                            <button type="button" class="slug-action-btn save" id="slug-save-btn" title="Save">
                                <i class="mdi mdi-check"></i>
                            </button>
                            <button type="button" class="slug-action-btn cancel" id="slug-cancel-btn" title="Cancel">
                                <i class="mdi mdi-close"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Slug Preview --}}
                    <div class="slug-preview">
                        {{ __('URL will be: ') }}<strong id="slug-url-preview">{{ request()->getSchemeAndHttpHost() }}/product/{{ $product->slug ?? 'your-product-slug' }}</strong>
                    </div>
                </div>

                {{-- Summary --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Summary") }}</label>
                    <div class="bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <textarea class="w-full bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0 resize-none" rows="4" name="summery" placeholder="{{ __("Write product Summary...") }}">{!! $product?->summary ?? "" !!}</textarea>
                    </div>
                    <span class="product-error-msg summery-error"></span>
                </div>

                {{-- Description (Summernote) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase">{{ __("Description") }} <x-fields.mandatory-indicator/></label>
                        @if(\App\PluginSystem\PluginManager::isActive('ai-integration'))
                        <button type="button" class="ai-bar-btn" data-ai-target="description" data-ai-type="product">
                            <i class="mdi mdi-robot-outline"></i> {{__('Generate with AI')}}
                        </button>
                        @endif
                    </div>
                    <textarea class="summernote" name="description" required>{!! $product?->description ?? "" !!}</textarea>
                    <p class="text-xs text-muted mt-1">{{ __('Description must be at least 10 characters long.') }}</p>
                    <span class="product-error-msg description-error"></span>
                </div>

                {{-- Brand --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Brand") }}</label>
                    <div>
                        <select name="brand" class="lnd-input" id="brand_id">
                            <option value="">{{ __("Select brand") }}</option>
                            @foreach($brands as $item)
                                <option {{ $item->id == $product?->brand_id ? "selected" : "" }} value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
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
{{--                <button type="submit" class="submit-form inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-success hover:opacity-90 transition" style="display: none;">--}}
{{--                    <i class="mdi mdi-check"></i> {{ isset($product) ? __('Update Product') : __('Create Product') }}--}}
{{--                </button>--}}
            </div>
        </form>
    </div>
</div>
