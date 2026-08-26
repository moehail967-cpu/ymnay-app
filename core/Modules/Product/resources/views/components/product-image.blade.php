<div class="space-y-4">
    <h4 class="product-section-title">{{ __('Product Images') }}</h4>

    @if(isset($product))
        <x-fields.tw-media-upload
            :id="$product->image_id"
            :title="__('Feature Image')"
            :name="'image_id'"
            :dimentions="'200x200'"
        />
        <span class="product-error-msg image_id-error"></span>

        @php
            if (!is_null($product->gallery_images)) {
                $image_arr = optional($product->gallery_images)->toArray();
                $gallery = '';
                foreach ($image_arr as $key => $arr) {
                    $gallery .= $arr['id'];
                    if ($key != count($image_arr)-1) {
                        $gallery .= '|';
                    }
                }
            }
        @endphp
        <x-landlord-others.tw-edit-media-upload-gallery
            :label="'Image Gallery'"
            :name="'product_gallery'"
            :value="$gallery ?? ''"
        />
    @else
        <x-fields.tw-media-upload
            :title="__('Feature Image')"
            :name="'image_id'"
            :dimentions="'200x200'"
        />
        <span class="product-error-msg image_id-error"></span>

        <x-landlord-others.tw-edit-media-upload-gallery
            :label="'Image Gallery'"
            :name="'product_gallery'"
            :value="''"
        />
    @endif
</div>

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
