<section class="fp-cat-section">
    <div class="container">

        {{-- Header --}}
        <div class="text-center mb-5">
            @if(!empty($tag))
                <div class="fp-section-badge mb-3">{{ $tag }}</div>
            @endif
            @if(!empty($title))
                <h2 class="fp-cat-heading">{!! $title !!}</h2>
            @endif
            @if(!empty($subtitle))
                <p class="fp-cat-sub">{{ $subtitle }}</p>
            @endif
        </div>

        @if($categories->isNotEmpty())
            <div class="row g-3 justify-content-center">
                @foreach($categories as $cat)
                    @php
                        $cat_img = theme_category_image($cat);
                        $cat_url = theme_category_url($cat->slug ?? $cat->id);
                        $count   = $cat->product_categories_count ?? 0;
                    @endphp
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ $cat_url }}" class="fp-cat-card-v2">
                            <div class="fp-cat-icon-box">
                                @if($cat_img)
                                    <img src="{{ $cat_img }}" alt="{{ $cat->name }}" class="fp-cat-icon-img">
                                @else
                                    <i class="mdi mdi-tag-outline"></i>
                                @endif
                            </div>
                            <div class="fp-cat-name">{{ $cat->name }}</div>
                            <div class="fp-cat-count">{{ $count }} {{ __('products') }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="fp-empty-state">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
