@extends('tenant.frontend.frontend-page-master')

@php
    $blog_page_title = __('Blog');
    $blog_page_id = get_static_option('blog_page');
    if ($blog_page_id) {
        $blog_page_obj = \App\Models\Page::find($blog_page_id);
        if ($blog_page_obj) { $blog_page_title = $blog_page_obj->title; }
    }
@endphp

@section('title') {{ $blog_page_title }} @endsection
@section('page-title') {{ $blog_page_title }} @endsection

@section('content')
<div class="ch-page-banner">
    <div class="container ch-page-banner-content">
        <h1>{{ $blog_page_title }}</h1>
        <div class="ch-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ $blog_page_title }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div class="ch-blog-card">
                            <div class="ch-blog-thumb">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;">
                                    </a>
                                @else
                                    <span style="font-size:48px;">🍽️</span>
                                @endif
                                @if($post->category())
                                    <span class="ch-blog-cat-pill">{{ $post->category()->name() }}</span>
                                @endif
                            </div>
                            <div class="ch-blog-body">
                                <div class="ch-blog-meta">
                                    <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="las la-user"></i> {{ $post->author() }}</span>
                                    @endif
                                    @if($post->has_comments())
                                        <span>
                                            <a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;">
                                                <i class="las la-comment"></i> {{ $post->comment_count() }}
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                <div class="ch-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="ch-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="ch-blog-read-more">
                                    {{ __('Read More') }} <i class="las la-arrow-right"></i>
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

            {{-- Pagination --}}
            @if(theme_blogs_paginator()?->hasPages())
                <div class="ch-pagination mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="ch-price-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="ch-btn ch-btn-red ch-btn-sm" style="white-space:nowrap;">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @php $recent_posts = theme_blogs()->take(5); @endphp
            @if($recent_posts->isNotEmpty())
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Recent Posts') }}</div>
                @foreach($recent_posts as $rp)
                <div class="ch-recent-post">
                    <div class="ch-recent-thumb">
                        @if($rp->has_image())
                            <img src="{{ $rp->image_url('grid') }}" alt="{{ $rp->title() }}" style="width:100%;height:100%;object-fit:cover;border-radius:var(--ch-radius-sm);">
                        @else
                            <span>🍽️</span>
                        @endif
                    </div>
                    <div>
                        <div class="ch-recent-name"><a href="{{ $rp->url() }}">{{ \Illuminate\Support\Str::words($rp->title(), 7) }}</a></div>
                        <div class="ch-recent-date">{{ $rp->date() }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_categories()->isNotEmpty())
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <label class="ch-filter-item" style="cursor:pointer;">
                        <a href="{{ $cat->url() }}" style="color:inherit;text-decoration:none;">{{ $cat->name() }}</a>
                        <span class="ch-filter-count">{{ $cat->count() }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="ch-sidebar-card">
                <div class="ch-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="ch-tag">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
