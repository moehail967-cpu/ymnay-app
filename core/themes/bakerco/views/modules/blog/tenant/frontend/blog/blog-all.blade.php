@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Blog') }} @endsection
@section('page-title') {{ __('Blog') }} @endsection

@section('content')
<div class="bk-page-banner">
    <div class="container">
        <h1>{{ __('Blog') }}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('Blog') }}</span>
        </div>
    </div>
</div>

<div class="container bk-blog-section">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div class="bk-blog-card">
                            <div class="bk-blog-img">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;">
                                    </a>
                                @else
                                    <span style="font-size:48px;">🥐</span>
                                @endif
                                @if($post->category())
                                    <a href="{{ $post->category()->url() }}" class="bk-blog-cat">{{ $post->category()->name() }}</a>
                                @endif
                            </div>
                            <div class="bk-blog-body">
                                <div class="bk-blog-meta">
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span>
                                    @endif
                                    @if($post->has_comments())
                                        <span>
                                            <a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;">
                                                <i class="mdi mdi-comment-outline"></i> {{ $post->comment_count() }}
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                <div class="bk-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="bk-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="bk-btn bk-btn-outline bk-blog-read-more">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center">{{ __('No Blog Available') }}</div>
                    </div>
                @endforelse
            </div>

            @if(theme_blogs_paginator()?->hasPages())
                <div class="bk-pagination mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="bk-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="bk-btn bk-btn-rose bk-sb-search-btn">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <div class="bk-sb-cat-row">
                        <a href="{{ $cat->url() }}" class="bk-sb-cat-link">{{ $cat->name() }}</a>
                        <span class="bk-filter-count">{{ $cat->count() }}</span>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="bk-tag-pill">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

            @php $recent_posts = theme_blogs()->take(4); @endphp
            @if($recent_posts->isNotEmpty())
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Recent Posts') }}</div>
                @foreach($recent_posts as $rpost)
                <div class="bk-recent-post">
                    @if($rpost->has_image())
                    <div class="bk-recent-thumb">
                        <a href="{{ $rpost->url() }}">
                            <img src="{{ $rpost->image_url() }}" alt="{{ $rpost->title() }}">
                        </a>
                    </div>
                    @endif
                    <div class="bk-recent-info">
                        <a href="{{ $rpost->url() }}" class="bk-recent-title">{{ \Illuminate\Support\Str::words($rpost->title(), 6) }}</a>
                        <span class="bk-recent-date"><i class="mdi mdi-calendar-outline"></i> {{ $rpost->date() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
