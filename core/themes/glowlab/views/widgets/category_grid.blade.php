<section class="gl-widget-cats">
<div class="container">
    <div class="gl-section-center">
        @if(!empty($section_tag))
            <div class="gl-section-tag">{{ $section_tag }}</div>
        @endif
        @if(!empty($title))
            <h2 class="gl-section-head">{{ $title }}</h2>
        @endif
        @if(!empty($subtitle))
            <p class="gl-section-sub">{{ $subtitle }}</p>
        @endif
    </div>
    @if($categories->isNotEmpty())
    <div class="row g-3 justify-content-center">
        @foreach($categories as $cat)
        @php $img = theme_category_image($cat); @endphp
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ theme_category_url($cat->slug) }}" class="gl-cat-card">
                <div class="gl-cat-icon-wrap">
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $cat->name }}">
                    @else
                        <i class="las la-leaf"></i>
                    @endif
                </div>
                <div class="gl-cat-name">{{ $cat->name }}</div>
                <div class="gl-cat-count">{{ $cat->product_categories_count ?? 0 }} {{ __('items') }}</div>
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
</section>
