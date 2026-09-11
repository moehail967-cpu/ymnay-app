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
                        <input type="text" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" id="product-name" value="{!! $product?->name ?? "" !!}" name="name" placeholder="{{ __("Write product Name...") }}">
                    </div>
                    <span class="product-error-msg name-error"></span>
                </div>

                {{-- Slug --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
                        {{ __("Slug") }}
                        <i class="mdi mdi-information-outline text-muted text-xs ml-1" data-bs-toggle="tooltip" title="{{ __('Only selected language text will convert into slug') }}"></i>
                    </label>
                    <div class="flex items-center gap-2.5 bg-surface border border-main rounded-xl px-3.5 py-2 focus-within:border-primary transition">
                        <i class="mdi mdi-link-variant text-primary"></i>
                        <input type="text" class="flex-1 bg-transparent text-sm text-dark placeholder-subtle outline-none border-none focus:ring-0 p-0" id="product-slug" value="{{ $product?->slug ?? "" }}" name="slug" placeholder="{{ __("Write product slug...") }}">
                    </div>
                    <span class="product-error-msg slug-error"></span>
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
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Description") }}</label>
                    <textarea class="summernote" name="description">{!! $product?->description ?? "" !!}</textarea>
                    <span class="product-error-msg description-error"></span>
                </div>

                {{-- Brand --}}
                <div>
                    <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">{{ __("Brand") }}</label>
                    <select name="brand" class="lnd-input" id="brand_id">
                        <option value="">{{ __("Select brand") }}</option>
                        @foreach($brands as $item)
                            <option {{ $item->id == $product?->brand_id ? "selected" : "" }} value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>
