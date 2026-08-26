{{-- Electro: Updated News --}}
<section class="el-news-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <h2 class="el-center-title">{{ $title }}</h2>

        @if($posts->isNotEmpty())
        <div class="el-news-grid">
            @foreach($posts as $post)
            @php
                $postUrl  = route('tenant.frontend.blog.single', $post->slug ?? $post->id);
                $catName  = $post->category?->name ?? '';
                $imgData  = !empty($post->image) ? get_attachment_image_by_id($post->image) : null;
                $imgSrc   = $imgData['img_url'] ?? null;
            @endphp
            <article class="el-news-card">
                <a href="{{ $postUrl }}" class="el-news-img-wrap">
                    @if($imgSrc)
                    <img src="{{ $imgSrc }}" alt="{{ $post->title }}" class="el-news-img" loading="lazy">
                    @else
                    <div class="el-news-img el-news-img-ph"><i class="las la-newspaper"></i></div>
                    @endif
                </a>
                <div class="el-news-body">
                    @if($catName)
                    <span class="el-news-cat">{{ $catName }}</span>
                    @endif
                    <a href="{{ $postUrl }}" class="el-news-title">{{ $post->title }}</a>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <p class="el-no-data">{{ __('No news found.') }}</p>
        @endif
    </div>
</section>
