{{-- Casual: Category Grid --}}
<section class="cs-catgrid-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px; background-color: #fff;" >
    <div class="container">
        <div class="cs-section-head">
            <h2 class="cs-section-title">{{ $title }}</h2>
        </div>

        @if($categories->isNotEmpty())
        <div class="cs-catgrid-row">
            @foreach($categories as $cat)
            @php
                $catImg = null;
                if (!empty($cat->image_id)) {
                    $d = get_attachment_image_by_id((int) $cat->image_id);
                    $catImg = $d['img_url'] ?? null;
                }
                $catUrl = theme_category_url($cat->slug ?? $cat->id);
            @endphp
            <a href="{{ $catUrl }}" class="cs-catgrid-card">
                <div class="cs-catgrid-img-wrap">
                    @if($catImg)
                    <img src="{{ $catImg }}" alt="{{ $cat->name }}" class="cs-catgrid-img" loading="lazy">
                    @else
                    <div class="cs-catgrid-img-placeholder"><i class="las la-tag"></i></div>
                    @endif
                </div>
                <div class="cs-catgrid-info">
                    <span class="cs-catgrid-name">{{ $cat->name }}</span>
                    @if(isset($cat->product_count))
                    <span class="cs-catgrid-count">{{ $cat->product_count }} Items</span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="cs-no-data">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
