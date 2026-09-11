{{-- Search --}}
<div class="cs-sb-card">
    <div class="cs-sb-title">{{ __('Search') }}</div>
    <form action="{{ theme_blog_search_url() }}" method="POST" class="cs-sb-search">
        {!! theme_csrf_field() !!}
        <input type="text" name="search" placeholder="{{ __('Search posts…') }}">
        <button type="submit"><i class="las la-search"></i></button>
    </form>
</div>

{{-- Recent Posts --}}
@php $recent_posts = theme_blogs()->take(4); @endphp
@if($recent_posts->isNotEmpty())
<div class="cs-sb-card">
    <div class="cs-sb-title">{{ __('Recent Posts') }}</div>
    @foreach($recent_posts as $rpost)
    <div class="cs-sb-recent">
        @if($rpost->has_image())
        <div class="cs-sb-recent-thumb">
            <a href="{{ $rpost->url() }}">
                <img src="{{ $rpost->image_url() }}" alt="{{ $rpost->title() }}" loading="lazy">
            </a>
        </div>
        @endif
        <div class="cs-sb-recent-info">
            <a href="{{ $rpost->url() }}" class="cs-sb-recent-title">{{ \Illuminate\Support\Str::words($rpost->title(), 6) }}</a>
            <span class="cs-sb-recent-date"><i class="las la-calendar-alt"></i> {{ $rpost->date() }}</span>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Categories --}}
@if(theme_blog_categories()->isNotEmpty())
<div class="cs-sb-card">
    <div class="cs-sb-title">{{ __('Categories') }}</div>
    @foreach(theme_blog_categories() as $cat)
    <div class="cs-sb-cat-row">
        <a href="{{ $cat->url() }}" class="cs-sb-cat-link">{{ $cat->name() }}</a>
        <span class="cs-sb-cat-count">{{ $cat->count() }}</span>
    </div>
    @endforeach
</div>
@endif

{{-- Tags --}}
@if(theme_blog_tags()->isNotEmpty())
<div class="cs-sb-card">
    <div class="cs-sb-title">{{ __('Tags') }}</div>
    <div class="cs-sb-tags">
        @foreach(theme_blog_tags() as $tag)
            <a href="{{ $tag->url() }}" class="cs-sb-tag"><i class="las la-tag"></i> {{ $tag->name() }}</a>
        @endforeach
    </div>
</div>
@endif
