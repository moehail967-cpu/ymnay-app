<section class="dk-widget-cats">
    <div class="container">
        <div class="text-center mb-4">
            @if($tag)
            <span class="dk-section-tag">{{ $tag }}</span>
            @endif
            @if($title)
            <h2 class="dk-section-title">{!! $title !!}</h2>
            @endif
            @if($subtitle)
            <p class="dk-section-sub">{{ $subtitle }}</p>
            @endif
        </div>

        @php
        $icons = ['las la-cogs','las la-bolt','las la-circle','las la-tools','las la-car','las la-tachometer-alt','las la-oil-can','las la-wrench'];
        @endphp

        <div class="row g-3">
            @forelse($categories as $i => $cat)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ theme_category_url($cat->slug) }}" class="dk-cat-card">
                    <div class="dk-cat-icon">
                        @php $cat_img = theme_category_image($cat); @endphp
                        @if($cat_img && !str_contains($cat_img, 'no-image'))
                            <img src="{{ $cat_img }}" alt="{{ $cat->name }}">
                        @else
                            <i class="{{ $icons[$i % count($icons)] }}"></i>
                        @endif
                    </div>
                    <div class="dk-cat-name">{{ $cat->name }}</div>
                    <div class="dk-cat-count">{{ $cat->products_count ?? 0 }} {{ __('items') }}</div>
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-5" style="color:var(--dk-silver);">{{ __('No categories found.') }}</div>
            @endforelse
        </div>
    </div>
</section>
