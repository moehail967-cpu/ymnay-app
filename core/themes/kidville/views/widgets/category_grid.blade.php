<section class="kv-category-grid-widget py-5">
    <div class="container">
        <div class="text-center mb-5">
            @if(!empty($section_tag))
                <span class="kv-section-tag">{{ strtoupper($section_tag) }}</span>
            @endif
            <h2 class="kv-section-title mt-3">{!! $title !!}</h2>
            @if(!empty($subtitle))
                <p class="kv-section-sub">{{ $subtitle }}</p>
            @endif
        </div>

        @if($categories->isNotEmpty())
            <div class="row g-3">
                @foreach($categories as $cat)
                    @php
                        $cat_img = theme_category_image($cat);
                        $cat_url = theme_category_url($cat->slug);
                    @endphp
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ $cat_url }}" class="kv-cat-card">
                            <div class="kv-cat-icon">
                                @if($cat_img)
                                    <img src="{{ $cat_img }}" alt="{{ $cat->name }}">
                                @else
                                    <i class="las la-tag"></i>
                                @endif
                            </div>
                            <div class="kv-cat-name">{{ $cat->name }}</div>
                            <div class="kv-cat-count">{{ $cat->product_count ?? 0 }} {{ __('items') }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5 kv-muted">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
