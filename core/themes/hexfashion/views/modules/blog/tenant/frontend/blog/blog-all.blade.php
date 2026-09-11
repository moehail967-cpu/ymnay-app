@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Blog') }} @endsection
@section('page-title') {{ __('Blog') }} @endsection

@section('content')
<div class="hf-blog-hero">
    <div class="container">
        <h1>{{ __('Latest News & Articles') }}</h1>
        <div class="hf-dash-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="las la-angle-right" style="font-size:11px;"></i>
            <span>{{ __('Blog') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:48px;padding-bottom:80px;">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                <div class="col-md-6">
                    <div class="hf-blog-card">
                        <div class="hf-blog-thumb">
                            @if($post->has_image())
                                <a href="{{ $post->url() }}"><img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"></a>
                            @else
                                <div class="hf-blog-thumb-ph"><i class="las la-newspaper"></i></div>
                            @endif
                            @if($post->category())
                                <a href="{{ $post->category()->url() }}" class="hf-blog-cat-badge">{{ $post->category()->name() }}</a>
                            @endif
                        </div>
                        <div class="hf-blog-body">
                            <div class="hf-blog-meta">
                                <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                @if($post->author())<span><i class="las la-user"></i> {{ $post->author() }}</span>@endif
                                @if($post->has_comments())
                                <span><a href="{{ $post->comment_url() }}"><i class="las la-comment"></i> {{ $post->comment_count() }}</a></span>
                                @endif
                            </div>
                            <div class="hf-blog-title"><a href="{{ $post->url() }}">{!! $post->title() !!}</a></div>
                            <p class="hf-blog-excerpt">{{ $post->excerpt(18) }}</p>
                            <a href="{{ $post->url() }}" class="hf-blog-read-more">{{ __('Read More') }} <i class="las la-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="hf-blog-empty">
                        <i class="las la-newspaper"></i>
                        <p>{{ __('No blog posts available.') }}</p>
                    </div>
                </div>
                @endforelse
            </div>

            @if(theme_blogs_paginator()?->hasPages())
            <div style="margin-top:32px;">
                {{ theme_blogs_paginator()->links() }}
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Search --}}
            <div class="hf-blog-sidebar-card">
                <div class="hf-blog-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div class="hf-blog-search-row">
                        <input type="text" name="search" placeholder="{{ __('Search articles…') }}">
                        <button type="submit" class="hf-blog-search-btn"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div class="hf-blog-sidebar-card">
                <div class="hf-blog-sidebar-title">{{ __('Categories') }}</div>
                <ul class="hf-blog-cat-list">
                    @foreach(theme_blog_categories() as $cat)
                    <li class="hf-blog-cat-item">
                        <a href="{{ $cat->url() }}" class="hf-blog-cat-link">
                            <span><i class="las la-tag" style="color:#E8603C;margin-right:6px;"></i>{{ $cat->name() }}</span>
                            <span class="hf-blog-cat-count">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div class="hf-blog-sidebar-card">
                <div class="hf-blog-sidebar-title">{{ __('Popular Tags') }}</div>
                <div class="hf-blog-tag-cloud">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}" class="hf-blog-tag">#{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
