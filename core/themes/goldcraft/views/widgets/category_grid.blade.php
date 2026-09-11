<section class="gc-widget-cats">
<div class="container">
    <div class="gc-section-center">
        @if(!empty($section_tag))
            <div class="gc-section-tag">{{ $section_tag }}</div>
        @endif
        @if(!empty($title))
            <h2 class="gc-section-head">{{ $title }}</h2>
        @endif
        @if(!empty($subtitle))
            <p class="gc-section-sub">{{ $subtitle }}</p>
        @endif
        <div class="gc-section-divider"></div>
    </div>
    @if($categories->isNotEmpty())
    <div class="row g-3">
        @foreach($categories as $cat)
        @php $img = theme_category_image($cat); @endphp
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ theme_category_url($cat->slug) }}" class="gc-cat-card">
                <div class="gc-cat-icon-wrap">
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $cat->name }}">
                    @else
                        <i class="las la-gem"></i>
                    @endif
                </div>
                <div class="gc-cat-name">{{ $cat->name }}</div>
                <div class="gc-cat-count">{{ $cat->product_categories_count ?? 0 }} {{ __('pieces') }}</div>
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
</section>
