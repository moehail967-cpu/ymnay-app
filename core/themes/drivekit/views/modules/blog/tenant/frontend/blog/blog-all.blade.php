@extends('tenant.frontend.frontend-page-master')

@php
    $_blog_page_id = get_static_option('blog_page');
    $_blog_page    = $_blog_page_id ? \App\Models\Page::select('id','title')->find($_blog_page_id) : null;
    $_blog_title   = $_blog_page?->title ?? __('Blog');
@endphp

@section('title') {{ $_blog_title }} @endsection
@section('page-title') {{ $_blog_title }} @endsection

@section('content')

{{-- Page Banner --}}
<div class="dk-blog-page-banner">
    <div class="container">
        <h1><i class="mdi mdi-car-wrench" style="color:var(--dk-red);"></i> {{ $_blog_title }}</h1>
        <div class="dk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>›</span>
            {{ $_blog_title }}
        </div>
    </div>
</div>

{{-- Blog Layout --}}
<section class="py-5" style="background:var(--dk-carbon);">
    <div class="container">
        <div class="row g-4">

            {{-- Main Content --}}
            <div class="col-lg-8">

                @php
                    $_all_posts  = theme_blogs();
                    $_featured   = $_all_posts->first();
                    $_grid_posts = $_all_posts->slice(1);
                @endphp

                {{-- First post — full-width featured card --}}
                @if($_featured)
                <a href="{{ $_featured->url() }}" class="dk-blog-card-link">
                    <div class="dk-blog-card">
                        <div class="dk-blog-thumb large">
                            @if($_featured->has_image())
                                <img src="{{ $_featured->image_url() }}" alt="{{ $_featured->title() }}" loading="lazy">
                            @else
                                <div class="dk-blog-no-img"><i class="mdi mdi-car-wrench"></i></div>
                            @endif
                        </div>
                        <div class="dk-blog-body">
                            @if($_featured->category())
                                <span class="dk-blog-cat">{{ $_featured->category()->name() }}</span>
                            @endif
                            <div class="dk-blog-title">{!! $_featured->title() !!}</div>
                            <p class="dk-blog-excerpt">{{ $_featured->excerpt(24) }}</p>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="dk-blog-meta">
                                    @if($_featured->author())
                                        <span><i class="mdi mdi-account-outline"></i> {{ $_featured->author() }}</span>
                                    @endif
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $_featured->date() }}</span>
                                    @if($_featured->has_comments())
                                        <span><i class="mdi mdi-comment-outline"></i> {{ $_featured->comment_count() }}</span>
                                    @endif
                                </div>
                                <span class="dk-read-more">{{ __('Read Article') }} <i class="mdi mdi-arrow-right"></i></span>
                            </div>
                        </div>
                    </div>
                </a>
                @endif

                {{-- Remaining posts — 3-column grid --}}
                @if($_grid_posts->isNotEmpty())
                <div class="row g-3 mt-1">
                    @foreach($_grid_posts as $post)
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
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($_all_posts->isEmpty())
                <div class="dk-blog-empty">
                    <i class="mdi mdi-car-wrench dk-blog-empty-icon"></i>
                    <p style="color:var(--dk-silver);">{{ __('No Blog Posts Available') }}</p>
                </div>
                @endif

                {{-- Pagination --}}
                @if(theme_blogs_paginator()?->hasPages())
                <div class="dk-pagination">
                    {{ theme_blogs_paginator()->links() }}
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
                        <a href="{{ $cat->url() }}" class="dk-cat-link">
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
