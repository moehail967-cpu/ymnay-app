@php
    $pt          = !empty($data['padding_top'])    ? (int)$data['padding_top']    : 80;
    $pb          = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $placeholder = global_asset('assets/common/img/placeholder.jpg');
@endphp

<style>
    .pf-blog-card {
        background: #fff;
        border: 1px solid var(--pf-border, #DDE6EA);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: box-shadow .2s, transform .2s;
    }
    .pf-blog-card:hover {
        box-shadow: 0 8px 32px rgba(0,137,123,.1);
        transform: translateY(-3px);
    }
    .pf-blog-card-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }
    .pf-blog-card-img-placeholder {
        width: 100%;
        height: 200px;
        background: var(--pf-teal-light, #E0F2F1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
    }
    .pf-blog-card-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .pf-blog-tag {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--pf-teal, #00897B);
        background: var(--pf-teal-light, #E0F2F1);
        padding: 3px 10px;
        margin-bottom: 12px;
    }
    .pf-blog-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--pf-dark, #1A2332);
        text-decoration: none;
        line-height: 1.4;
        margin-bottom: 10px;
        display: block;
        flex: 1;
    }
    .pf-blog-title:hover { color: var(--pf-teal, #00897B); }
    .pf-blog-excerpt {
        font-size: 13px;
        color: var(--pf-muted, #607080);
        line-height: 1.6;
        margin-bottom: 16px;
    }
    .pf-blog-read-more {
        font-size: 13px;
        font-weight: 600;
        color: var(--pf-teal, #00897B);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: auto;
    }
    .pf-blog-read-more:hover { color: var(--pf-teal-deep, #006358); }
    .pf-blog-meta {
        font-size: 11px;
        color: var(--pf-muted, #607080);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--pf-bg, #F7FAFB);">
    <div class="container">

        @if(!empty($data['title']))
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:32px;">
                <div>
                    <h2 style="font-size:clamp(20px,2.5vw,28px); font-weight:800; color:var(--pf-dark,#1A2332); margin:0 0 6px;">
                        {{ $data['title'] }}
                    </h2>
                    @if(!empty($data['subtitle']))
                        <p style="font-size:14px; color:var(--pf-muted,#607080); margin:0;">{{ $data['subtitle'] }}</p>
                    @endif
                </div>
                @if(!empty($data['view_all_text']))
                    <a href="{{ $data['view_all_url'] }}"
                       style="font-size:13px; font-weight:600; color:var(--pf-teal,#00897B); text-decoration:none; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;">
                        {{ $data['view_all_text'] }} <i class="mdi mdi-arrow-right"></i>
                    </a>
                @endif
            </div>
        @endif

        @if($data['posts']->isNotEmpty())
            <div class="row g-4">
                @foreach($data['posts'] as $post)
                    @php
                        $img     = get_attachment_image_by_id($post->image ?? null);
                        $img_url = !empty($img) ? $img['img_url'] : '';
                        $post_url = route('tenant.frontend.blog.single', $post->slug);
                        $cat_name = $post->category?->name ?? '';
                    @endphp
                    <div class="col-md-4">
                        <div class="pf-blog-card">
                            @if(!empty($img_url))
                                <a href="{{ $post_url }}">
                                    <img src="{{ $img_url }}" alt="{{ $post->title }}" class="pf-blog-card-img" loading="lazy">
                                </a>
                            @else
                                <a href="{{ $post_url }}" class="pf-blog-card-img-placeholder">
                                    <span>🩺</span>
                                </a>
                            @endif

                            <div class="pf-blog-card-body">
                                @if(!empty($cat_name))
                                    <span class="pf-blog-tag">{{ $cat_name }}</span>
                                @endif

                                <div class="pf-blog-meta">
                                    <span><i class="mdi mdi-calendar-outline me-1"></i>{{ $post->created_at->format('d M Y') }}</span>
                                    @if(!empty($post->author))
                                        <span><i class="mdi mdi-account-outline me-1"></i>{{ $post->author }}</span>
                                    @endif
                                </div>

                                <a href="{{ $post_url }}" class="pf-blog-title">
                                    {{ \Illuminate\Support\Str::limit($post->title, 65) }}
                                </a>

                                @if(!empty($post->excerpt))
                                    <p class="pf-blog-excerpt">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
                                @endif

                                <a href="{{ $post_url }}" class="pf-blog-read-more">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--pf-muted,#607080);">{{ __('No articles found.') }}</p>
        @endif

    </div>
</section>
