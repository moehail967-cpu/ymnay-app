@php
    $col_class = match((int)$columns) {
        2 => 'col-md-6',
        4 => 'col-md-6 col-lg-3',
        default => 'col-md-6 col-lg-4',
    };
@endphp

<section class="xgpb-recent-blog" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        @if(!empty($title))
            <div class="xgpb-section-header d-flex align-items-center justify-content-between mb-5">
                <div>
                    <h2 class="xgpb-section-title">{{ $title }}</h2>
                    @if(!empty($subtitle))<p class="xgpb-section-subtitle mb-0">{{ $subtitle }}</p>@endif
                </div>
                @if(!empty($view_all_text))
                    <a href="{{ $view_all_url }}" class="btn btn-outline-primary">{{ $view_all_text }}</a>
                @endif
            </div>
        @endif

        @if($posts->isNotEmpty())
            <div class="row g-4">
                @foreach($posts as $post)
                    @php
                        $img_data = get_attachment_image_by_id($post->image ?? null);
                        $img_url  = !empty($img_data) ? $img_data['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                        $slug_route = route('tenant.frontend.blog.single', $post->slug ?? '#');
                    @endphp
                    <div class="{{ $col_class }}">
                        <article class="xgpb-blog-card card h-100 border-0">
                            <a href="{{ $slug_route }}" class="xgpb-blog-img-wrap d-block overflow-hidden">
                                <img src="{{ $img_url }}" alt="{{ $post->title }}"
                                     class="card-img-top xgpb-blog-img" loading="lazy">
                            </a>
                            <div class="card-body p-4">
                                @if($show_date && !empty($post->created_at))
                                    <span class="xgpb-blog-date">{{ $post->created_at->format('M d, Y') }}</span>
                                @endif
                                @if($show_author && !empty($post->author))
                                    <span class="xgpb-blog-author ms-2">by {{ $post->author }}</span>
                                @endif
                                <h5 class="xgpb-blog-title mt-2">
                                    <a href="{{ $slug_route }}" class="text-decoration-none">{{ $post->title }}</a>
                                </h5>
                                @if(!empty($post->excerpt))
                                    <p class="xgpb-blog-excerpt">{{ Str::limit($post->excerpt, 100) }}</p>
                                @endif
                                <a href="{{ $slug_route }}" class="xgpb-blog-read-more">{{ __('Read More') }} &rarr;</a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No blog posts found.') }}</p>
        @endif
    </div>
</section>

<style>
.xgpb-blog-card { border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.07); transition: box-shadow .3s, transform .3s; }
.xgpb-blog-card:hover { box-shadow: 0 8px 32px rgba(0,0,0,.14); transform: translateY(-4px); }
.xgpb-blog-img-wrap { height: 220px; overflow: hidden; }
.xgpb-blog-img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
.xgpb-blog-card:hover .xgpb-blog-img { transform: scale(1.05); }
.xgpb-blog-date, .xgpb-blog-author { font-size: .8rem; color: #6c757d; }
.xgpb-blog-title { font-size: 1rem; font-weight: 600; line-height: 1.4; margin-bottom: .5rem; }
.xgpb-blog-title a { color: #1a1a2e; }
.xgpb-blog-title a:hover { color: var(--main-color-one, #0d6efd); }
.xgpb-blog-excerpt { font-size: .875rem; color: #6c757d; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: .75rem; }
.xgpb-blog-read-more { font-size: .875rem; font-weight: 600; color: var(--main-color-one, #0d6efd); text-decoration: none; }
</style>
