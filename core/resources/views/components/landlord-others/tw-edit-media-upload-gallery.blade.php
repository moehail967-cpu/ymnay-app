@php
    $size = $size ?? '1920x1280';
    $fieldName = $name ?? 'gallery_image';
    $sectionId = $fieldName . '_gallery_section';
    $galleryImages = [];
    if (!empty($value)) {
        $ids = explode('|', $value);
        foreach ($ids as $imgId) {
            if (empty($imgId)) continue;
            $imgData = get_attachment_image_by_id($imgId, null, true);
            if (!empty($imgData) && !empty($imgData['img_url'])) {
                $galleryImages[] = $imgData['img_url'];
            }
        }
    }
@endphp

<div class="tw-media-upload-wrapper" id="{{ $sectionId }}">
    <div class="tw-img-wrap flex flex-wrap gap-2 mb-3">
        @foreach($galleryImages as $imgUrl)
            <div class="w-16 h-16 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                <img src="{{ $imgUrl }}" alt="" class="w-full h-full object-cover">
            </div>
        @endforeach
    </div>
    <input type="hidden" name="{{ $fieldName }}" class="tw-media-id-input" value="{{ $value ?? '' }}">
    <button type="button"
            class="tw-media-open-btn inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium text-white bg-primary hover:opacity-90 transition"
            data-target="{{ $sectionId }}"
            data-multiple="true">
        <i class="mdi mdi-image-multiple-outline text-base"></i>
        {{ __('Change Gallery') }}
    </button>
    <p class="text-[11px] text-muted mt-1.5">{{ __('Allowed: jpg, jpeg, png') }} &middot; {{ __('Recommended size:') }} {{ $size }}</p>
</div>
