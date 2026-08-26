@php
    if (!isset($authors)) {
        $authors = [];
    }
    if (!isset($languages)) {
        $languages = [];
    }
    if (!isset($product)) {
        $product = null;
    }
@endphp

<div>
    <h4 class="product-section-title">{{__('Product Additional Field Info')}}</h4>
    <div class="space-y-4">
        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Author") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <select name="author_id" id="tax" class="lnd-input">
                <option value="">{{__('Select an author')}}</option>
                @foreach($authors as $author)
                    <option value="{{$author->id}}" {{$product?->additionalFields?->author_id == $author->id ? 'selected' : ''}}>{{$author->name}}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Pages") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" value="{{ $product?->additionalFields?->pages }}" name="page" placeholder="{{ __("Enter page number...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Language") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <select name="language" id="tax" class="lnd-input">
                <option value="">{{__('Select a language')}}</option>
                @foreach($languages as $author)
                    <option value="{{$author->id}}" {{$product?->additionalFields?->language == $author->id ? 'selected' : ''}}>{{$author->name}}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Formats") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" value="{{ $product?->additionalFields?->formats }}" name="formats" placeholder="{{ __("Enter formats...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Words") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" value="{{ $product?->additionalFields?->words }}" name="word" placeholder="{{ __("Enter words...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Tool Used") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" value="{{ $product?->additionalFields?->tool_used }}" name="tool_used" placeholder="{{ __("Enter tool names...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Database Used") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" value="{{ $product?->additionalFields?->database_used }}" name="database_used" placeholder="{{ __("Enter database names...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Compatible Browsers") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" value="{{ $product?->additionalFields?->compatible_browsers }}" name="compatible_browsers" placeholder="{{ __("Enter compatible browser names...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Compatible OS") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <input type="text" class="lnd-input" value="{{ $product?->additionalFields?->compatible_os }}" name="compatible_os" placeholder="{{ __("Enter compatible os names...") }}">
        </div>

        <div>
            <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("High Resolution") }} <span class="text-primary text-[9px] normal-case">{{__('(Optional)')}}</span></label>
            <select name="high_resolution" id="high_resolution" class="lnd-input">
                <option value="">{{__('Select an option')}}</option>
                <option value="yes" {{$product?->additionalFields?->high_resolution == 'yes' ? 'selected' : ''}}>{{__('Yes')}}</option>
                <option value="no" {{$product?->additionalFields?->high_resolution == 'no' ? 'selected' : ''}}>{{__('No')}}</option>
            </select>
        </div>
    </div>

    <h4 class="product-section-title mt-8">{{__('Product Additional Custom Field Info')}} <span class="text-primary text-[9px] normal-case font-normal">{{__('(Optional)')}}</span></h4>
    <div class="mt-4">
        @php
            $custom_fields = $product?->additionalCustomFields ?? [];
        @endphp

        @if(count($custom_fields) > 0)
            @foreach($custom_fields as $field)
                <div class="dp-custom-field-row custom-additional-field-row">
                    <input type="text" class="lnd-input" value="{{$field->option_name}}" name="option_name[]" placeholder="{{ __("Option Name") }}">
                    <input type="text" class="lnd-input" value="{{$field->option_value}}" name="option_value[]" placeholder="{{ __("Option Value") }}">
                    <div class="flex items-center gap-1.5 pt-0.5">
                        <button type="button" class="variant-repeater-btn add custom-plus"><span class="mdi mdi-plus"></span></button>
                        <button type="button" class="variant-repeater-btn remove custom-minus"><span class="mdi mdi-minus"></span></button>
                    </div>
                </div>
            @endforeach
        @else
            <div class="dp-custom-field-row custom-additional-field-row">
                <input type="text" class="lnd-input" value="" name="option_name[]" placeholder="{{ __("Option Name") }}">
                <input type="text" class="lnd-input" value="" name="option_value[]" placeholder="{{ __("Option Value") }}">
                <div class="flex items-center gap-1.5 pt-0.5">
                    <button type="button" class="variant-repeater-btn add custom-plus"><span class="mdi mdi-plus"></span></button>
                    <button type="button" class="variant-repeater-btn remove custom-minus"><span class="mdi mdi-minus"></span></button>
                </div>
            </div>
        @endif
    </div>
</div>
