{{-- TrailCo: Category Grid Widget --}}
<section class="tc-cat-section py-5">
    <div class="container">
        @if($title)
        <div class="text-center mb-4">
            <span class="tc-section-tag">{{ __('Categories') }}</span>
            <h2 class="tc-section-title">{!! $title !!}</h2>
            @if(!empty($subtitle))
                <p class="tc-section-sub">{{ $subtitle }}</p>
            @endif
        </div>
        @endif

        <div class="row g-3">
            @forelse($categories as $cat)
                @php
                    $catUrl = theme_category_url($cat->slug ?? $cat->id);
                    $catImg = $cat->image_id ? theme_category_image($cat) : null;
                    $count  = $cat->products_count ?? 0;
                @endphp
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <a href="{{ $catUrl }}" class="tc-cat-card">
                        <div class="tc-cat-icon">
                            @if($catImg)
                                <img src="{{ $catImg }}" alt="{{ $cat->name }}">
                            @else
                                <i class="{{ $cat->icon ?? 'mdi mdi-hiking' }}"></i>
                            @endif
                        </div>
                        <div class="tc-cat-name">{{ $cat->name }}</div>
                        @if($count > 0)
                            <div class="tc-cat-count">{{ $count }} {{ __('items') }}</div>
                        @endif
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-4">
                    <p style="color:var(--tc-muted);">{{ __('No categories available.') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
