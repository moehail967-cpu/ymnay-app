@php
    if(!isset($product)){
        $product = null;
    }
@endphp

<div>
    <h4 class="product-section-title">{{ __("General Information") }}</h4>
    <div class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Name") }} <x-fields.mandatory-indicator/></label>
            <input type="text" class="lnd-input" id="product-name" value="{{ $product?->name ?? "" }}" name="name" placeholder="{{ __("Write product Name...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                {{ __("Slug") }} <x-fields.mandatory-indicator/>
                <i class="mdi mdi-information-outline text-muted text-xs ml-1" data-bs-toggle="tooltip" title="{{ __('URL-friendly version of the name') }}"></i>
            </label>
            <div class="slug-field-wrapper">
                <div class="slug-display" id="slug-display">
                    {{ $product?->slug ?? '' }}
                </div>
                <input type="text"
                       class="slug-input"
                       id="product-slug"
                       name="slug"
                       value="{{ $product?->slug ?? '' }}"
                       style="display: none;"
                       placeholder="{{ __('Enter product slug...') }}">
                <button type="button" class="slug-edit-icon" id="slug-edit-btn" title="{{ __('Edit slug') }}">
                    <i class="mdi mdi-pencil"></i>
                </button>
                <div class="slug-actions" style="display:none;">
                    <button type="button" class="slug-action-btn save" id="slug-save-btn" title="{{ __('Save') }}">
                        <i class="mdi mdi-check"></i>
                    </button>
                    <button type="button" class="slug-action-btn cancel" id="slug-cancel-btn" title="{{ __('Cancel') }}">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
            </div>
            <div class="slug-preview">
                {{ __('URL will be: ') }}<strong id="slug-url-preview">{{ request()->getSchemeAndHttpHost() }}/product/{{ $product?->slug ?? 'your-product-slug' }}</strong>
            </div>
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Summary") }} <x-fields.mandatory-indicator/></label>
            <textarea class="lnd-input" style="height: 120px" name="summary" placeholder="{{ __("Write product Summary...") }}">{{ $product?->summary ?? "" }}</textarea>
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Description") }} <x-fields.mandatory-indicator/></label>
            <textarea class="lnd-input summernote" name="description" placeholder="{{ __("Type Description") }}">{!! purify_html($product?->description ?? "") !!}</textarea>
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Included files") }} <span class="text-primary text-[9px] normal-case">{{__('Optional')}}</span></label>
            <input type="text" class="lnd-input" id="included_file" value="{{ $product?->included_files ?? "" }}" name="included_files" placeholder="{{ __("Write included file names...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Version") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" id="version" value="{{ $product?->version ?? "" }}" name="version" placeholder="{{ __("Write version number...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Release Date") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="date" class="lnd-input flatpickr" id="release_date" value="{{ $product?->release_date ?? "" }}" name="release_date" placeholder="{{ __("Write release date...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Latest Update") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="date" class="lnd-input flatpickr" id="latest_date" value="{{ $product?->update_date ?? "" }}" name="update_date" placeholder="{{ __("Write latest update...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Preview Link") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" id="preview_link" value="{{ $product?->preview_link ?? "" }}" name="preview_link" placeholder="{{ __("Write preview link...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Quantity") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional - If applicable)')}}</span></label>
            <input type="text" class="lnd-input" id="quantity" value="{{ $product?->quantity ?? "" }}" name="quantity" placeholder="{{ __("Write quantity...") }}">
        </div>
    </div>
</div>
