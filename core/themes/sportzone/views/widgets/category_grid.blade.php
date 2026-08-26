{{-- SportZone: Category Grid Widget --}}
<section class="sz-cat-section">
    <div class="container">

        <div class="text-center mb-4">
            <span class="sz-section-tag">{{ __('Sports Categories') }}</span>
            <h2 class="sz-section-title">{{ $title }}</h2>
            <p class="sz-section-sub">{{ __('Gear up for your game.') }}</p>
        </div>

        @php
            $fallback_icons = [
                'mdi mdi-soccer','mdi mdi-run-fast','mdi mdi-weight-lifter',
                'mdi mdi-basketball','mdi mdi-tennis','mdi mdi-swim',
                'mdi mdi-bike','mdi mdi-golf','mdi mdi-football',
                'mdi mdi-snowboard','mdi mdi-yoga','mdi mdi-hiking',
            ];
        @endphp

        <div class="row g-3">
            @forelse($categories as $idx => $cat)
                @php
                    $img    = get_attachment_image_by_id($cat->image_id ?? null, 'thumbnail');
                    $imgUrl = $img['img_url'] ?? null;
                    $count  = $cat->product_count ?? 0;
                @endphp
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ theme_shop_url() }}?category={{ $cat->slug }}" class="sz-cat-card">
                        <div class="sz-cat-icon">
                            @if($imgUrl)
                                <img src="{{ $imgUrl }}" alt="{{ $cat->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" loading="lazy">
                            @else
                                <i class="{{ $fallback_icons[$idx % count($fallback_icons)] }}"></i>
                            @endif
                        </div>
                        <div class="sz-cat-name">{{ $cat->name }}</div>
                        @if($count)
                            <div class="sz-cat-count">{{ $count }} {{ __('items') }}</div>
                        @endif
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p style="opacity:.4;">{{ __('No categories available.') }}</p>
                </div>
            @endforelse
        </div>

    </div>
</section>
