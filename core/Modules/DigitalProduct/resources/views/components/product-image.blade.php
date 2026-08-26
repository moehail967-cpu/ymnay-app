@php
    if (!isset($product)) {
        $product = null;
    }
    $gallery = '';
    if ($product && !is_null($product->gallery_images)) {
        $image_arr = optional($product->gallery_images)->toArray();
        foreach ($image_arr as $key => $arr) {
            $gallery .= $arr['id'];
            if ($key != count($image_arr) - 1) $gallery .= '|';
        }
    }
@endphp

<div class="space-y-5">
    <h4 class="product-section-title">{{ __('File & Images') }}</h4>

    {{-- Product Main File --}}
    <x-digitalproduct::product-file-uploader.markup :file="$product"/>

    {{-- Feature Image --}}
    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
            {{ __('Feature Image') }} <x-fields.mandatory-indicator/>
        </label>
        <x-fields.tw-media-upload
            :id="$product?->image_id"
            :name="'image_id'"
            :dimentions="'200x200'"
        />
    </div>

    {{-- Image Gallery --}}
    <div>
        <label class="block text-[10px] font-bold tracking-widest text-muted uppercase mb-1.5">
            {{ __('Image Gallery') }}
        </label>
        <x-landlord-others.tw-edit-media-upload-gallery
            :label="'Image Gallery'"
            :name="'product_gallery'"
            :value="$gallery"
        />
    </div>
</div>
