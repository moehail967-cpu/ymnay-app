{{-- Casual: Blog Updates --}}
<section class="cs-blog-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="cs-section-head">
            <h2 class="cs-section-title">{{ $title }}</h2>
            <a href="{{ theme_blog_url() }}" class="cs-section-view-all">{{ __('View All') }} <i class="las la-arrow-right"></i></a>
        </div>

        @if($posts->isNotEmpty())
        <div class="cs-blog-grid">
            @foreach($posts as $post)
            @php
                $postUrl = theme_blog_details_url($post->slug ?? $post->id);
                $catName = $post->category?->name ?? '';
                $catUrl  = !empty($post->category?->slug) ? theme_blog_url() : '#';
                $imgSrc  = !empty($post->image) ? tenant_asset($post->image) : null;
                $postDate = \Carbon\Carbon::parse($post->created_at)->format('M d, Y');
            @endphp
            <article class="cs-blog-card">
                <a href="{{ $postUrl }}" class="cs-blog-img-wrap">
                    @if($imgSrc)
                    <img src="{{ $imgSrc }}" alt="{{ $post->title }}" class="cs-blog-img" loading="lazy">
                    @else
                    <div class="cs-blog-img cs-blog-img-ph"><i class="las la-newspaper"></i></div>
                    @endif
                    @if($catName)
                    <span class="cs-blog-cat-tag">{{ $catName }}</span>
                    @endif
                </a>
                <div class="cs-blog-body">
                    <span class="cs-blog-date"><i class="las la-calendar"></i> {{ $postDate }}</span>
                    <a href="{{ $postUrl }}" class="cs-blog-title">{{ $post->title }}</a>
                    @if(!empty($post->short_description))
                    <p class="cs-blog-excerpt">{{ Str::limit(strip_tags($post->short_description), 90) }}</p>
                    @endif
                    <a href="{{ $postUrl }}" class="cs-blog-read-more">{{ __('Read More') }} <i class="las la-arrow-right"></i></a>
                </div>
            </article>
            @endforeach
        </div>
        @else
        <p class="cs-no-data">{{ __('No blog posts found.') }}</p>
        @endif
    </div>
</section>
