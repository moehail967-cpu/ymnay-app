@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name }} @endsection
@section('page-title') {{ $category_name }} @endsection

@section('content')

{{-- Page Banner --}}
<div class="dk-blog-page-banner">
    <div class="container">
        <h1><i class="mdi mdi-tag-outline" style="color:var(--dk-red);"></i> {{ $category_name }}</h1>
        <div class="dk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>›</span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span>›</span>
            {{ $category_name }}
        </div>
    </div>
</div>

{{-- Blog Layout --}}
<section class="py-5" style="background:var(--dk-carbon);">
    <div class="container">
        <div class="row g-4">

            {{-- Main Content --}}
            <div class="col-lg-8">

                {{-- Sort Bar --}}
                <div class="dk-sort-bar mb-4" style="background:var(--dk-surface);padding:10px 16px;border-radius:var(--dk-radius);border:1px solid var(--dk-border);">
                    <span class="dk-sort-count" style="color:var(--dk-silver);font-size:13px;">
                        @php $pd = $blogs->withQueryString()->toArray(); @endphp
                        {{ __('Showing') }} <strong style="color:var(--dk-text);">{{ $pd['from'] ?? 0 }}–{{ $pd['to'] ?? 0 }}</strong>
                        {{ __('of') }} <strong style="color:var(--dk-text);">{{ $pd['total'] ?? 0 }}</strong> {{ __('posts') }}
                    </span>
                    <form action="{{ url()->current() }}" method="GET" id="dk-sort-form">
                        <input type="hidden" name="sort" id="dk-sort-input">
                        <select class="dk-sort-select" id="dk-sort-sel">
                            <option value="1" {{ ($order_type ?? 0) == 1 ? 'selected' : '' }}>{{ __('Name') }}</option>
                            <option value="2" {{ ($order_type ?? 0) == 2 ? 'selected' : '' }}>{{ __('Ascending') }}</option>
                            <option value="3" {{ ($order_type ?? 0) == 3 ? 'selected' : '' }}>{{ __('Descending') }}</option>
                        </select>
                    </form>
                </div>

                {{-- Post Grid --}}
                <div class="row g-3">
                    @forelse($theme_blogs ?? collect() as $post)
                    <div class="col-md-4">
                        <a href="{{ $post->url() }}" class="dk-blog-card-link">
                            <div class="dk-blog-card dk-blog-card-sm">
                                <div class="dk-blog-thumb small">
                                    @if($post->has_image())
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    @else
                                        <div class="dk-blog-no-img"><i class="mdi mdi-car-wrench"></i></div>
                                    @endif
                                </div>
                                <div class="dk-blog-body">
                                    @if($post->category())
                                        <span class="dk-blog-cat">{{ $post->category()->name() }}</span>
                                    @endif
                                    <div class="dk-blog-title-sm">{!! $post->title() !!}</div>
                                    <div class="dk-blog-meta">
                                        <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                        @if($post->author())
                                            <span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span>
                                        @endif
                                        @if($post->has_comments())
                                            <span><i class="mdi mdi-comment-outline"></i> {{ $post->comment_count() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="dk-blog-empty">
                            <i class="mdi mdi-car-wrench dk-blog-empty-icon"></i>
                            <p style="color:var(--dk-silver);">{{ __('No posts found in this category.') }}</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($blogs->hasPages())
                <div class="dk-pagination mt-4">
                    {{ $blogs->withQueryString()->links() }}
                </div>
                @endif

            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">

                {{-- Search --}}
                <div class="dk-sidebar-panel">
                    <div class="dk-sidebar-title"><i class="mdi mdi-magnify"></i> {{ __('Search') }}</div>
                    <form action="{{ theme_blog_search_url() }}" method="GET">
                        <div class="dk-search-bar">
                            <input type="text" name="search" placeholder="{{ __('Search articles...') }}">
                            <button type="submit"><i class="mdi mdi-magnify"></i></button>
                        </div>
                    </form>
                </div>

                {{-- Recent Posts --}}
                @if(theme_blogs()->isNotEmpty())
                <div class="dk-sidebar-panel">
                    <div class="dk-sidebar-title"><i class="mdi mdi-clock-outline"></i> {{ __('Recent Posts') }}</div>
                    @foreach(theme_blogs()->take(5) as $recent)
                    <div class="dk-recent-post">
                        <div class="dk-recent-thumb">
                            @if($recent->has_image())
                                <img src="{{ $recent->image_url() }}" alt="{{ $recent->title() }}">
                            @else
                                <i class="mdi mdi-car-wrench" style="color:var(--dk-red);opacity:.3;"></i>
                            @endif
                        </div>
                        <div>
                            <a href="{{ $recent->url() }}" class="dk-recent-title">{!! $recent->title() !!}</a>
                            <div class="dk-recent-date"><i class="mdi mdi-calendar" style="color:var(--dk-red);"></i> {{ $recent->date() }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Categories --}}
                @if(theme_blog_categories()->isNotEmpty())
                <div class="dk-sidebar-panel">
                    <div class="dk-sidebar-title"><i class="mdi mdi-format-list-bulleted"></i> {{ __('Categories') }}</div>
                    @foreach(theme_blog_categories() as $cat)
                        <a href="{{ $cat->url() }}" class="dk-cat-link {{ $cat->name() === $category_name ? 'active' : '' }}">
                            <span><i class="mdi mdi-chevron-right" style="color:var(--dk-red);"></i> {{ $cat->name() }}</span>
                            <span class="dk-cat-count">{{ $cat->count() }}</span>
                        </a>
                    @endforeach
                </div>
                @endif

                {{-- Tags --}}
                @if(theme_blog_tags()->isNotEmpty())
                <div class="dk-sidebar-panel">
                    <div class="dk-sidebar-title"><i class="mdi mdi-tag-outline"></i> {{ __('Tags') }}</div>
                    <div>
                        @foreach(theme_blog_tags() as $tag)
                            <a href="{{ $tag->url() }}" class="dk-tag">#{{ $tag->name() }}</a>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).on('change', '#dk-sort-sel', function () {
    $('#dk-sort-input').val($(this).val());
    $('#dk-sort-form').submit();
});
</script>
@endsection
