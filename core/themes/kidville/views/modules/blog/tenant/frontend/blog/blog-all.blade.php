@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Blog') }} @endsection

@section('content')
<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('Blog & News') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Blog') }}</span>
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
                        <div class="kv-blog-card">
                            <div class="kv-blog-thumb">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <i class="las la-newspaper" style="font-size:48px;color:var(--kv-muted);"></i>
                                @endif
                                @if($post->category())
                                    <span class="kv-blog-cat-pill">{{ $post->category()->name() }}</span>
                                @endif
                            </div>
                            <div class="kv-blog-body">
                                <div class="kv-blog-meta">
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
                                <div class="kv-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="kv-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="kv-blog-read-more">
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

            @if(theme_blogs_paginator()?->hasPages())
                <div class="kv-pagination mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="kv-price-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="kv-btn kv-btn-red kv-btn-sm" style="white-space:nowrap;">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <label class="kv-filter-item" style="cursor:pointer;">
                        <a href="{{ $cat->url() }}" style="color:inherit;text-decoration:none;font-weight:700;">{{ $cat->name() }}</a>
                        <span class="kv-filter-count">{{ $cat->count() }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="kv-tag">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
