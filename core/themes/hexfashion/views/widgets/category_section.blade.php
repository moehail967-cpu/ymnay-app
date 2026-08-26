{{-- Electro: Category Section --}}
<section class="hf-cat-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <h2 class="hf-center-title">{{ $title }}</h2>

        @if($categories->isNotEmpty())
        <div class="hf-cat-grid">
            @foreach($categories as $category)
            @php
                $img = theme_category_image($category);
                $url = theme_category_url($category->slug);
                $count = $category->product_count ?? 0;
            @endphp
            <a href="{{ $url }}" class="hf-cat-card">
                <div class="hf-cat-img-wrap">
                    <img src="{{ $img }}" alt="{{ $category->name }}" class="hf-cat-img" loading="lazy">
                </div>
                <div class="hf-cat-info">
                    <span class="hf-cat-name">{{ $category->name }}</span>
                    <span class="hf-cat-count">{{ $count }} {{ __('Products') }}</span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="hf-no-data">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
