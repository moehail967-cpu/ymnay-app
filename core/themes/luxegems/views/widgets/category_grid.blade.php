<section class="py-5" style="background:var(--lg-surface);border-top:1px solid var(--lg-border);border-bottom:1px solid var(--lg-border);">
    <div class="container">
        <div class="text-center mb-5">
            <div class="lg-line mx-auto"></div>
            <h2 class="lg-section-title">{{ $title }}</h2>
            @if(!empty($subtitle))
                <p class="lg-section-sub">{{ $subtitle }}</p>
            @endif
        </div>

        @if($categories->isNotEmpty())
            <div class="row g-3">
                @foreach($categories as $cat)
                    @php
                        $cat_url  = theme_category_url($cat->slug);
                        $count    = $cat->products_count ?? $cat->product_count ?? 0;
                        $icons    = ['💎','💍','⌚','📿','🔵','🟢','✨','🏆','🪙','💫'];
                        $icon     = $icons[$loop->index % count($icons)];
                        $cat_img  = null;
                        if (!empty($cat->image_id)) {
                            $imgData = get_attachment_image_by_id($cat->image_id);
                            $cat_img = $imgData['img_url'] ?? null;
                        }
                    @endphp
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ $cat_url }}" class="lg-cat-card">
                            <div class="lg-cat-icon">
                                @if($cat_img)
                                    <img src="{{ $cat_img }}" alt="{{ $cat->name }}" class="lg-cat-img">
                                @else
                                    {{ $icon }}
                                @endif
                            </div>
                            <div class="lg-cat-name">{{ $cat->name }}</div>
                            <div class="lg-cat-count">{{ $count }} {{ __('pieces') }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--lg-muted);">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
