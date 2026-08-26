{{-- Electro: Updated News --}}
<section class="hf-news-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <h2 class="hf-center-title">{{ $title }}</h2>

        @if($posts->isNotEmpty())
        <div class="hf-news-grid">
            @foreach($posts as $post)
            @php
                $postUrl  = route('tenant.frontend.blog.single', $post->slug ?? $post->id);
                $catName  = $post->category?->name ?? '';
                $imgData  = !empty($post->image) ? get_attachment_image_by_id($post->image) : null;
                $imgSrc   = $imgData['img_url'] ?? null;
            @endphp
            <article class="hf-news-card">
                <a href="{{ $postUrl }}" class="hf-news-img-wrap">
                    @if($imgSrc)
                    <img src="{{ $imgSrc }}" alt="{{ $post->title }}" class="hf-news-img" loading="lazy">
                    @else
                    <div class="hf-news-img hf-news-img-ph"><i class="las la-newspaper"></i></div>
                    @endif
                </a>
                <div class="hf-news-body">
                    @if($catName)
                    <span class="hf-news-cat">{{ $catName }}</span>
                    @endif
                    <a href="{{ $postUrl }}" class="hf-news-title">{{ $post->title }}</a>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <p class="hf-no-data">{{ __('No news found.') }}</p>
        @endif
    </div>
</section>
