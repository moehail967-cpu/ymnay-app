{{-- Casual: Product image gallery (no Slick — simple thumbnail click) --}}
<div class="cs-pd-gallery" id="shop_details_gallery_slider">
    <div class="cs-pd-main-img">
        <div class="shop-details-thums long-img" data-src="{{ $main_img_url }}">
            <img id="cs-main-product-img" src="{{ $main_img_url }}" alt="{{ $product->name }}" loading="lazy">
        </div>
    </div>
    @if(count($image_array) > 1)
    <div class="cs-pd-thumb-list">
        @foreach($image_array as $imgId)
            @php
                $tdata = get_attachment_image_by_id($imgId, 'grid');
                $turl  = $tdata['img_url'] ?? null;
            @endphp
            @if($turl)
            <div class="cs-pd-thumb {{ $loop->first ? 'active' : '' }} small-img"
                 data-image-path="{{ $turl }}"
                 onclick="csSwapImg(this, '{{ $turl }}')">
                <img src="{{ $turl }}" alt="{{ $product->name }}" loading="lazy">
            </div>
            @endif
        @endforeach
    </div>
    @endif
</div>
