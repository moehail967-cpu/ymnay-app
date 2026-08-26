{{-- PawHaus: Category Grid --}}
@php $categories = theme_categories()->take(12); @endphp
<section class="ph-category-grid-widget">
    <div class="container">
        <div class="ph-cats-head">
            @if(!empty($section_tag))
                <div class="ph-section-pill">{{ $section_tag }}</div>
            @endif
            @if(!empty($title))
                <h2 class="ph-cats-title">{!! $title !!}</h2>
            @endif
            @if(!empty($subtitle))
                <p class="ph-section-sub" style="margin:0;">{{ $subtitle }}</p>
            @endif
        </div>

        @if($categories->isNotEmpty())
            <div class="ph-cats-grid">
                @foreach($categories as $cat)
                    @php
                        $cat_img = theme_category_image($cat);
                        $cat_url = theme_category_url($cat->slug ?? $cat->id);
                    @endphp
                    <a href="{{ $cat_url }}" class="ph-cat-card">
                        <div class="ph-cat-icon">
                            @if($cat_img)
                                <img src="{{ $cat_img }}" alt="{{ $cat->name }}"
                                     style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                            @else
                                <i class="las la-paw" style="font-size:30px;color:var(--ph-terra);"></i>
                            @endif
                        </div>
                        <div class="ph-cat-name">{{ $cat->name }}</div>
                        @if(isset($cat->product_count) && $cat->product_count)
                            <div class="ph-cat-count">{{ $cat->product_count }} {{ __('items') }}</div>
                        @endif
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="las la-paw" style="font-size:48px;color:var(--ph-border);"></i>
                <p class="ph-section-sub mt-2">{{ __('No categories found.') }}</p>
            </div>
        @endif
    </div>
</section>
