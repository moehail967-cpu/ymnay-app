<section class="ch-category-grid-widget" style="background:var(--ch-warm); padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        <div class="ch-sec-heading">
            <div class="ch-sec-title">{{ $title }}</div>
            @if(!empty($view_all_url) && $view_all_url !== '#')
                <a href="{{ $view_all_url }}" class="ch-view-all">
                    {{ __('All Cuisines') }} <i class="las la-arrow-right"></i>
                </a>
            @endif
        </div>

        @if($categories->isNotEmpty())
            <div class="row g-3">
                @foreach($categories as $cat)
                    @php
                        $img_data  = get_attachment_image_by_id($cat->image_id ?? null);
                        $cat_img   = !empty($img_data) ? $img_data['img_url'] : null;
                        $cat_url   = theme_category_url($cat->slug ?? $cat->id);
                        $prod_count = $cat->product_categories_count ?? 0;
                    @endphp
                    <div class="col-4 col-md-2">
                        <a href="{{ $cat_url }}" class="ch-cuisine-card">
                            @if($cat_img)
                                <div class="ch-cuisine-img">
                                    <img src="{{ $cat_img }}" alt="{{ $cat->name }}">
                                </div>
                            @else
                                <span class="ch-cuisine-emoji">🍽️</span>
                            @endif
                            <div class="ch-cuisine-name">{{ $cat->name }}</div>
                            <div class="ch-cuisine-count">{{ $prod_count }} {{ __('items') }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
