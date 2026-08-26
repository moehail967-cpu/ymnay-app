@php $categories = theme_categories()->take($limit); @endphp
<section style="padding-top:{{ $padding_top }}px; padding-bottom:{{ $padding_bottom }}px; background:#fff;">
<div class="container">
    <div class="text-center mb-4">
        @if(!empty($section_tag))<div class="bk-section-tag">{{ $section_tag }}</div>@endif
        @if(!empty($title))<h2 class="bk-section-title">{{ $title }}</h2>@endif
        @if(!empty($subtitle))<p class="bk-section-sub">{{ $subtitle }}</p>@endif
    </div>
    @if($categories->isNotEmpty())
    <div class="row g-3">
        @foreach($categories as $cat)
        @php $img = theme_category_image($cat); @endphp
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ theme_category_url($cat->slug) }}" class="bk-cat-card">
                <div class="bk-cat-icon">
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $cat->name }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        <i class="las la-tag" style="font-size:2rem;color:var(--bk-rose);"></i>
                    @endif
                </div>
                <div class="bk-cat-name">{{ $cat->name }}</div>
                <div class="bk-cat-count">{{ $cat->product_count ?? 0 }} {{ __('items') }}</div>
            </a>
        </div>
        @endforeach
    </div>
    @endif
</div>
</section>
