<section class="pf-blog-widget-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="pf-section-row-head">
            <div>
                <h2 class="pf-section-h2">{{ $title }}</h2>
                @if($subtitle)
                    <p class="pf-section-hint">{{ $subtitle }}</p>
                @endif
            </div>
            <a href="{{ theme_blog_url() }}" class="pf-link-teal">
                {{ __('All Articles') }} <i class="las la-arrow-right"></i>
            </a>
        </div>
        @if($posts->isNotEmpty())
        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-3">
            @foreach($posts as $post)
            @php
                $post_img = null;
                if($post->image) {
                    $img_data = get_attachment_image_by_id($post->image);
                    $post_img = $img_data['img_url'] ?? null;
                }
                $post_url = theme_blog_url() . '/' . $post->slug;
                $cat_name = $post->category?->name ?? null;
            @endphp
            <div class="col">
                <div class="pf-blog-widget-card">
                    <div class="pf-blog-widget-thumb">
                        @if($post_img)
                            <a href="{{ $post_url }}">
                                <img src="{{ $post_img }}" alt="{{ $post->title }}" loading="lazy">
                            </a>
                        @else
                            <div class="pf-blog-widget-thumb-ph">
                                <i class="las la-heartbeat"></i>
                            </div>
                        @endif
                    </div>
                    <div class="pf-blog-widget-body">
                        @if($cat_name)
                            <span class="pf-blog-widget-cat">{{ $cat_name }}</span>
                        @endif
                        <h3 class="pf-blog-widget-title">
                            <a href="{{ $post_url }}">{{ $post->title }}</a>
                        </h3>
                        @if($post->excerpt)
                            <p class="pf-blog-widget-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt, 120) }}</p>
                        @endif
                        <a href="{{ $post_url }}" class="pf-blog-widget-read">
                            {{ __('Read More') }} <i class="las la-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>
