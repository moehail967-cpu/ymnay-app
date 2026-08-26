<section class="pf-cat-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="pf-section-row-head">
            <div>
                <h2 class="pf-section-h2 pf-display-font">{{ $title }}</h2>
                <p class="pf-section-hint">{{ __('Find what you need quickly') }}</p>
            </div>
            <a href="{{ theme_shop_url() }}" class="pf-link-teal">
                {{ __('All Categories') }} <i class="las la-arrow-right"></i>
            </a>
        </div>
        <div class="row g-3 row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6">
            @foreach($categories as $cat)
            @php
                $cat_img = get_attachment_image_by_id($cat->image_id ?? null);
                $cat_img_url = $cat_img['img_url'] ?? null;
                $cat_url = theme_category_url($cat->slug ?? '');
            @endphp
            <div class="col">
                <a href="{{ $cat_url }}" class="pf-cat-card-v2">
                    <div class="pf-cat-icon-v2">
                        @if($cat_img_url)
                            <img src="{{ $cat_img_url }}" alt="{{ $cat->name }}">
                        @else
                            <i class="las la-pills"></i>
                        @endif
                    </div>
                    <div class="pf-cat-name-v2">{{ $cat->name }}</div>
                    @if(isset($cat->products_count) || isset($cat->product_count))
                        <div class="pf-cat-count-v2">{{ $cat->products_count ?? $cat->product_count ?? 0 }}+ {{ __('items') }}</div>
                    @endif
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
