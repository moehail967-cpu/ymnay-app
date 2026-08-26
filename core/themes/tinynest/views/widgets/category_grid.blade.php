{{-- TinyNest: Category Grid Widget --}}
<section class="tn-section tn-cat-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        @if(!empty($title))
        <div class="text-center mb-5">
            <span class="tn-section-tag tn-section-tag-pink">{{ __('Browse by Category') }}</span>
            <h2 class="tn-section-title mt-2">
                @php
                    $words    = explode(' ', $title);
                    $lastWord = array_pop($words);
                    echo e(implode(' ', $words)) . ' <span class="tn-title-accent">' . e($lastWord) . '?</span>';
                @endphp
            </h2>
            @if(!empty($subtitle))
                <p class="tn-section-sub mx-auto" style="max-width:460px;">{{ $subtitle }}</p>
            @endif
        </div>
        @endif

        <div class="row g-3 justify-content-center">
            @forelse($categories as $cat)
                @php
                    $catUrl = theme_category_url($cat->slug ?? $cat->id);
                    $catImg = $cat->image_id ? theme_category_image($cat) : null;
                    $count  = $cat->products_count ?? 0;
                @endphp
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <a href="{{ $catUrl }}" class="tn-cat-card-v2">
                        <div class="tn-cat-icon-v2">
                            @if($catImg)
                                <img src="{{ $catImg }}" alt="{{ $cat->name }}">
                            @else
                                <i class="{{ $cat->icon ?? 'mdi mdi-tag-outline' }}"></i>
                            @endif
                        </div>
                        <div class="tn-cat-name-v2">{{ $cat->name }}</div>
                        @if($count > 0)
                            <div class="tn-cat-count-v2">{{ $count }} {{ __('items') }}</div>
                        @endif
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-4">
                    <div class="tn-empty-state">
                        <i class="mdi mdi-tag-off-outline tn-empty-icon"></i>
                        <p class="tn-empty-title">{{ __('No categories found.') }}</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>
