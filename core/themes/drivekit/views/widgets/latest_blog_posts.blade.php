<section class="dk-widget-blogs">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <span class="dk-section-tag">{{ $tag }}</span>
                <h2 class="dk-section-title mb-0">{{ $title }}</h2>
            </div>
            @if(!empty($view_all_url) && $view_all_url !== '#')
            <a href="{{ $view_all_url }}" class="dk-btn dk-btn-outline dk-btn-sm">
                {{ __('All Articles') }} <i class="las la-arrow-right"></i>
            </a>
            @endif
        </div>

        @if($posts->isNotEmpty())
        <div class="row g-3">
            @foreach($posts as $post)
            @php
                $b_img = $post->image ? get_attachment_image_by_id($post->image, 'full') : null;
                $b_url = dynamicRoute($post->slug ?? '');
            @endphp
            <div class="col-md-4">
                <a href="{{ $b_url }}" class="dk-blog-card">
                    <div class="dk-blog-card-img">
                        @if(!empty($b_img['img_url']))
                            <img src="{{ $b_img['img_url'] }}" alt="{{ $post->title }}" loading="lazy">
                        @else
                            <div class="dk-blog-card-placeholder"><i class="las la-newspaper"></i></div>
                        @endif
                    </div>
                    <div class="dk-blog-card-body">
                        <div class="dk-blog-card-meta">
                            <i class="las la-calendar"></i>
                            {{ optional($post->created_at)->format('M d, Y') }}
                        </div>
                        <h3 class="dk-blog-card-title">{{ \Illuminate\Support\Str::words($post->title, 10) }}</h3>
                        <p class="dk-blog-card-excerpt">{{ \Illuminate\Support\Str::words(strip_tags($post->description ?? ''), 18) }}</p>
                        <span class="dk-blog-card-link">{{ __('Read More') }} <i class="las la-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        @else
        <p class="dk-muted-text text-center py-5">{{ __('No posts found.') }}</p>
        @endif
    </div>
</section>
