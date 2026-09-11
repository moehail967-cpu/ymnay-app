{{-- VelvetLux: Category Grid --}}
<section class="py-5" style="background: var(--vl-white);">
    <div class="container">
        @if($section_title)
        <div class="text-center mb-5">
            <span class="vl-section-tag">{!! $section_tag !!}</span>
            <h2 class="vl-section-title">{!! $section_title !!}</h2>
            <div class="vl-divider mx-auto"></div>
        </div>
        @endif
        <div class="row g-3">
            @forelse($categories as $cat)
            @php $catImg = theme_category_image($cat); @endphp
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ theme_category_url($cat->slug) }}" class="vl-cat-card d-block text-decoration-none">
                    <div class="vl-cat-icon">
                        @if($catImg)
                            <img src="{{ $catImg }}" alt="{{ $cat->name }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                            <i class="mdi mdi-hanger" style="font-size:28px;"></i>
                        @endif
                    </div>
                    <div class="vl-cat-name">{{ $cat->name }}</div>
                    @if(!empty($cat->product_count))
                        <div class="vl-cat-count">{{ $cat->product_count }} {{ __('pieces') }}</div>
                    @endif
                </a>
            </div>
            @empty
            <div class="col-12 text-center py-4" style="color:var(--vl-muted);">{{ __('No categories found.') }}</div>
            @endforelse
        </div>
    </div>
</section>
