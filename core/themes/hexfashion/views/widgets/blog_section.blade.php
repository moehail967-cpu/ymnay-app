<style>
/* ── Blog Updates ── */
.hf-news-section { background:#fff; }
.hf-news-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:28px; }
.hf-news-card { display:flex; flex-direction:column; gap:12px; }
.hf-news-img-wrap { display:block; border-radius:10px; overflow:hidden; aspect-ratio:16/10; }
.hf-news-img { width:100%; height:100%; object-fit:cover; transition:transform .3s; display:block; }
.hf-news-card:hover .hf-news-img { transform:scale(1.03); }
.hf-news-img-ph { width:100%; height:100%; background:#f0ebe5; display:flex; align-items:center; justify-content:center; color:#ccc; font-size:40px; }
.hf-news-body { display:flex; flex-direction:column; gap:8px; }
.hf-news-cat { font-size:13px; font-weight:600; color:#ff7857; }
.hf-news-title { font-size:16px; font-weight:700; color:#1a1a1a; text-decoration:none; line-height:1.5; display:block; }
.hf-news-title:hover { color:#ff7857; }
@media(max-width:768px) { .hf-news-grid { grid-template-columns:1fr; } }
</style>
{{-- HexFashion: Blog Section --}}
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
