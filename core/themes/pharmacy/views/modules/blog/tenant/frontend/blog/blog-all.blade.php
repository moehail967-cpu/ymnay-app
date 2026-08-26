@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Health Blog') }} @endsection
@section('page-title') {{ __('Health Blog') }} @endsection

@section('content')
<div class="pf-blog-hero">
    <div class="container">
        <h1>{{ __('Health & Wellness Blog') }}</h1>
        <div class="pf-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
            <span class="current">{{ __('Blog') }}</span>
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
                        <div class="pf-blog-card-wrap">
                            <div class="pf-blog-thumb-wrap">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <div class="pf-blog-thumb-placeholder">
                                        <i class="las la-heartbeat"></i>
                                    </div>
                                @endif
                                @if($post->category())
                                    <span class="pf-blog-cat-badge">{{ $post->category()->name() }}</span>
                                @endif
                            </div>
                            <div class="pf-blog-body">
                                <div class="pf-blog-meta">
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span>
                                    @endif
                                    @if($post->has_comments())
                                        <span>
                                            <a href="{{ $post->comment_url() }}">
                                                <i class="mdi mdi-comment-outline"></i> {{ $post->comment_count() }}
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                <h3 class="pf-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </h3>
                                <p class="pf-blog-excerpt">{{ $post->excerpt(18) }}</p>
                                <a href="{{ $post->url() }}" class="pf-btn pf-btn-outline pf-btn-sm">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="pf-blog-empty">
                            <i class="mdi mdi-newspaper-variant-outline"></i>
                            <p>{{ __('No Blog Available') }}</p>
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
            <div class="pf-blog-sidebar-card">
                <div class="pf-blog-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div class="pf-blog-search-bar">
                        <input type="text" name="search" placeholder="{{ __('Search articles…') }}">
                        <button type="submit"><i class="mdi mdi-magnify"></i></button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div class="pf-blog-sidebar-card">
                <div class="pf-blog-sidebar-title">{{ __('Categories') }}</div>
                <ul class="pf-blog-cat-list">
                    @foreach(theme_blog_categories() as $cat)
                    <li class="pf-blog-cat-item">
                        <a href="{{ $cat->url() }}" class="pf-blog-cat-link">
                            <span><i class="mdi mdi-medical-bag"></i>{{ $cat->name() }}</span>
                            <span class="pf-blog-cat-count">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div class="pf-blog-sidebar-card">
                <div class="pf-blog-sidebar-title">{{ __('Popular Tags') }}</div>
                <div class="pf-blog-tag-cloud">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}" class="pf-blog-tag">#{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
