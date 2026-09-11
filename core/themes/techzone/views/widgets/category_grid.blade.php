{{-- TechZone: Category Grid Widget --}}
<section class="tz-cat-section">
    <div class="container">

        {{-- Heading --}}
        <div class="text-center mb-5">
            <span class="tz-section-tag">&#9632; {{ __('Browse') }}</span>
            @php
                $titleWords = explode(' ', trim($title));
                $lastWord   = array_pop($titleWords);
                $titleStart = implode(' ', $titleWords);
            @endphp
            <h2 class="tz-section-title">{{ $titleStart }} <span class="tz-title-accent">{{ $lastWord }}</span></h2>
            @if(!empty($subtitle))
                <p class="tz-section-sub">{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Category cards --}}
        <div class="row g-3 justify-content-center">
            @forelse($categories as $idx => $cat)
                @php
                    $catUrl = theme_category_url($cat->slug ?? $cat->id);
                    $catImg = theme_category_image($cat);
                    $count  = $cat->product_count ?? 0;
                    $hasImg = !empty($cat->image_id) && !empty($catImg);

                    $fallbackIcons = [
                        'las la-laptop','las la-mobile-alt','las la-headphones',
                        'las la-gamepad','las la-home','las la-plug',
                        'las la-camera','las la-tv','las la-microchip','las la-keyboard',
                    ];
                    $icon = $fallbackIcons[$idx % count($fallbackIcons)];

                    $unitLabel = in_array($cat->slug ?? '', ['laptops','phones','cameras','tablets'])
                        ? __('models') : __('items');
                @endphp
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <a href="{{ $catUrl }}" class="tz-cat-card">
                        <div class="tz-cat-thumb {{ $hasImg ? 'tz-cat-thumb-img' : '' }}">
                            @if($hasImg)
                                <img src="{{ $catImg }}" alt="{{ $cat->name }}" loading="lazy">
                            @else
                                <i class="{{ $icon }}"></i>
                            @endif
                        </div>
                        <div class="tz-cat-name">{{ $cat->name }}</div>
                        @if($count > 0)
                            <div class="tz-cat-count">{{ $count }} {{ $unitLabel }}</div>
                        @endif
                    </a>
                </div>
            @empty
                <div class="col-12 text-center py-5" style="color:var(--tz-muted);opacity:.5;">
                    <i class="las la-box-open" style="font-size:40px;"></i>
                    <p class="mt-2">{{ __('No categories yet.') }}</p>
                </div>
            @endforelse
        </div>

    </div>
</section>
