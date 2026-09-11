{{-- Electro: Category Section --}}
<section class="el-cat-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <h2 class="el-center-title">{{ $title }}</h2>

        @if($categories->isNotEmpty())
        <div class="el-cat-grid">
            @foreach($categories as $category)
            @php
                $img = theme_category_image($category);
                $url = theme_category_url($category->slug);
                $count = $category->product_count ?? 0;
            @endphp
            <a href="{{ $url }}" class="el-cat-card">
                <div class="el-cat-img-wrap">
                    <img src="{{ $img }}" alt="{{ $category->name }}" class="el-cat-img" loading="lazy">
                </div>
                <div class="el-cat-info">
                    <span class="el-cat-name">{{ $category->name }}</span>
                    <span class="el-cat-count">{{ $count }} {{ __('Products') }}</span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <p class="el-no-data">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
