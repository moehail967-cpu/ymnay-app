@php $categories = theme_categories(); @endphp
<section class="ms-widget-cats">
    <div class="container">
        <div class="ms-widget-cats-head">
            @if(!empty($section_tag))
                <div class="ms-section-tag">{{ $section_tag }}</div>
            @endif
            @if(!empty($title))
                <h2 class="ms-section-title">{{ $title }}</h2>
            @endif
            @if(!empty($subtitle))
                <p class="ms-section-sub">{{ $subtitle }}</p>
            @endif
        </div>
        @if($categories->isNotEmpty())
            <div class="row g-3">
                @foreach($categories as $cat)
                    @php $img = theme_category_image($cat); @endphp
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ theme_category_url($cat->slug) }}" class="ms-cat-card">
                            <div class="ms-cat-thumb">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $cat->name }}">
                                @else
                                    <div class="ms-cat-thumb-placeholder"><i class="las la-couch"></i></div>
                                @endif
                            </div>
                            <div class="ms-cat-body">
                                <div class="ms-cat-name">{{ $cat->name }}</div>
                                <div class="ms-cat-count">{{ $cat->product_count ?? 0 }} {{ __('items') }}</div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5 ms-muted-text">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
