@php $uid = 'bpnews' . substr(md5(uniqid()), 0, 8); @endphp
<section class="bp-news-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="bp-slider-head">
            <h2 class="bp-section-title">{{ $title }}</h2>
            <div class="bp-slider-arrows">
                <button class="bp-arrow" id="{{ $uid }}-prev" aria-label="{{ __('Previous') }}">
                    <i class="las la-angle-left"></i>
                </button>
                <button class="bp-arrow" id="{{ $uid }}-next" aria-label="{{ __('Next') }}">
                    <i class="las la-angle-right"></i>
                </button>
            </div>
        </div>
        <div class="bp-slider-wrap" id="{{ $uid }}">
            <div class="bp-slider-track">
                @forelse($posts as $post)
                @php
                    $post_url = theme_blog_url() . '/' . $post->slug;
                    $img      = $post->image ?? null;
                @endphp
                <div class="bp-news-card">
                    <a href="{{ $post_url }}" class="bp-news-img-wrap">
                        @if($img)
                            <img src="{{ tenant_asset($img) }}" alt="{{ $post->title }}" loading="lazy">
                        @else
                            <div class="bp-news-img-placeholder"><i class="las la-newspaper"></i></div>
                        @endif
                    </a>
                    <div class="bp-news-body">
                        <h4 class="bp-news-title">
                            <a href="{{ $post_url }}">{{ \Illuminate\Support\Str::words($post->title, 10) }}</a>
                        </h4>
                        @if(!empty($post->excerpt))
                            <p class="bp-news-excerpt">{{ \Illuminate\Support\Str::words(strip_tags($post->excerpt), 18) }}</p>
                        @endif
                        <a href="{{ $post_url }}" class="bp-btn-primary bp-btn-sm">{{ __('Read Article') }}</a>
                    </div>
                </div>
                @empty
                <p class="text-muted">{{ __('No posts yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
<script>
(function(){
    var wrap = document.getElementById('{{ $uid }}');
    var prev = document.getElementById('{{ $uid }}-prev');
    var next = document.getElementById('{{ $uid }}-next');
    if (!wrap) return;
    var scrollAmt = 340;
    if (prev) prev.addEventListener('click', function(){ wrap.scrollBy({ left: -scrollAmt, behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function(){ wrap.scrollBy({ left:  scrollAmt, behavior: 'smooth' }); });
})();
</script>
