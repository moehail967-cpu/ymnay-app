@php
    $fieldName = $name ?? 'gallery_image';
    $sectionId = $fieldName . '_gallery_section';
@endphp

<div class="tw-media-upload-wrapper" id="{{ $sectionId }}">
    <div class="tw-img-wrap flex flex-wrap gap-2 mb-3"></div>
    <input type="hidden" name="{{ $fieldName }}" class="tw-media-id-input">
    <button type="button"
            class="tw-media-open-btn inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:opacity-90 transition"
            data-target="{{ $sectionId }}"
            data-multiple="true">
        <i class="mdi mdi-image-multiple-outline text-base"></i>
        {{ __('Upload Gallery Image') }}
    </button>
</div>
