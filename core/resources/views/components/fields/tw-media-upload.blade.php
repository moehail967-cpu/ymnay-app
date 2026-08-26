@php
    $mediaId = isset($id) ? $id : null;
    $imgData  = $mediaId ? get_attachment_image_by_id($mediaId) : null;
    $imgUrl   = (!empty($imgData) && !empty($imgData['img_url'])) ? $imgData['img_url'] : null;
@endphp

<div class="tw-media-upload-wrapper" id="{{ $name }}_section">
    {{-- Preview --}}
    <div class="tw-img-wrap mb-3">
        @if($imgUrl)
            <div class="tw-attachment-preview relative inline-block">
                <img src="{{ $imgUrl }}" alt=""
                     class="tw-preview-img w-24 h-24 rounded-lg object-cover border border-gray-200" />
                <button type="button"
                        class="tw-rmv-btn absolute -top-2 -right-2 w-5 h-5 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs leading-none transition">
                    &times;
                </button>
            </div>
        @else
            <div class="tw-attachment-preview"></div>
        @endif
    </div>

    <input type="hidden" name="{{ $name }}" class="tw-media-id-input" value="{{ $mediaId }}">

    <button type="button"
            class="tw-media-open-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:opacity-90 transition"
            data-target="{{ $name }}_section"
            data-imgid="{{ $mediaId ?? '' }}">
        <i class="ti tabler-photo text-base"></i>
        {{ $imgUrl ? __('Change Image') : __('Upload Image') }}
    </button>

    @if(isset($dimentions))
        <p class="mt-1 text-xs text-gray-400">{{ __('Recommended') }}: {{ $dimentions }}</p>
    @endif
</div>
