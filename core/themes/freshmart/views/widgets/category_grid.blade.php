<section class="fm-category-grid-widget">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="fm-section-title">{{ $title }}</h2>
            @if(!empty($subtitle))
                <p class="fm-section-sub mb-0">{{ $subtitle }}</p>
            @endif
        </div>

        @if($categories->isNotEmpty())
            <div class="row g-3">
                @foreach($categories as $cat)
                    @php
                        $cat_img = theme_category_image($cat);
                        $cat_url = theme_category_url($cat->slug ?? $cat->id);
                    @endphp
                    <div class="col-4 col-md-3 col-lg-2">
                        <a href="{{ $cat_url }}" class="fm-cat-card">
                            <div class="fm-cat-img">
                                @if($cat_img)
                                    <img src="{{ $cat_img }}" alt="{{ $cat->name }}">
                                @else
                                    <i class="mdi mdi-tag-outline fm-cat-icon-fallback"></i>
                                @endif
                            </div>
                            <div class="fm-cat-name">{{ $cat->name }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="fm-empty-state">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
