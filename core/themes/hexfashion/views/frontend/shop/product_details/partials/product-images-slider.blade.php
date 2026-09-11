<div class="hf-pd-images">
    @php
        $image_array = [];
        array_push($image_array, (int) $product->image_id);
        if (count($product->gallery_images) > 0) {
            foreach ($product->gallery_images ?? [] as $galleryImage) {
                array_push($image_array, $galleryImage->id);
            }
        }
        $main_img_data = get_attachment_image_by_id($product->image_id);
        $main_img_url  = !empty($main_img_data) ? $main_img_data['img_url'] : '';
    @endphp

    <!-- Main Image -->
    <div class="hf-pd-main-img long-img" data-src="{{ $main_img_url }}">
        {!! render_image_markup_by_attachment_id($product->image_id, 'rounded') !!}
    </div>

    <!-- Thumbnail Strip -->
    @if(count($image_array) > 1)
        <div class="hf-pd-thumb-strip">
            @foreach($image_array as $imageId)
                @php
                    $img_info  = get_attachment_image_by_id($imageId);
                    $img_path  = !empty($img_info) ? $img_info['img_url'] : '';
                @endphp
                <div class="hf-pd-thumb small-img shop-details-thums bg-image details-small-product"
                     {!! render_background_image_markup_by_attachment_id($imageId) !!}
                     data-image-path="{{ $img_path }}">
                </div>
            @endforeach
        </div>
    @endif
</div>
