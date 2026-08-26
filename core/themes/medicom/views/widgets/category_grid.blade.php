{{-- Medicom: Category Grid (Bakerco Style) --}}
<section class="mc-category-section" style="padding-top:{{ $padding_top }}px; padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="text-center mc-cat-header">
            @if(!empty($section_tag))
                <div class="mc-section-tag">{{ $section_tag }}</div>
            @endif
            @if(!empty($title))
                <h2 class="mc-section-title">{{ $title }}</h2>
            @endif
            @if(!empty($subtitle))
                <p class="mc-section-sub">{{ $subtitle }}</p>
            @endif
        </div>

        @if($categories->isNotEmpty())
            <div class="row g-4 justify-content-center">
                @foreach($categories as $cat)
                    @php $img = theme_category_image($cat); @endphp
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                        <a href="{{ theme_category_url($cat->slug) }}" class="mc-cat-card">
                            <div class="mc-cat-icon">
                                @if($img)
                                    <img src="{{ $img }}" alt="{{ $cat->name }}">
                                @else
                                    <i class="las la-heartbeat placeholder-icon"></i>
                                @endif
                            </div>
                            <div class="mc-cat-name">{{ $cat->name }}</div>
                            <div class="mc-cat-count">{{ $cat->product_count ?? 0 }} {{ __('items') }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>


