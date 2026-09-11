@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_title ?? __('Blog Category') }} @endsection
@section('page-title') {{ $category_title ?? __('Blog Category') }} @endsection

@section('content')
<div class="tn-page-banner">
    <div class="container tn-page-banner-content">
        <h1>{{ $category_title ?? __('Blog Category') }}</h1>
        <div class="tn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ $category_title ?? __('Category') }}</span>
        </div>
    </div>
</div>

<div class="container tn-page-wrap">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div class="tn-blog-card">
                            <div class="tn-blog-thumb">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <span class="tn-blog-no-img"><i class="las la-baby"></i></span>
                                @endif
                                @if($post->category())
                                    <span class="tn-blog-cat-pill">{{ $post->category()->name() }}</span>
                                @endif
                            </div>
                            <div class="tn-blog-body">
                                <div class="tn-blog-meta">
                                    <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="las la-user"></i> {{ $post->author() }}</span>
                                    @endif
                                    @if($post->has_comments())
                                        <span>
                                            <a href="{{ $post->comment_url() }}" class="tn-blog-meta-link">
                                                <i class="las la-comment"></i> {{ $post->comment_count() }}
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                <div class="tn-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="tn-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="tn-blog-read-more">
                                    {{ __('Read More') }} <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center">{{ __('No posts in this category') }}</div>
                    </div>
                @endforelse
            </div>

            @if(theme_blogs_paginator()?->hasPages())
                <div class="tn-pagination mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="tn-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="tn-btn tn-btn-primary tn-btn-sm">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <label class="tn-filter-item">
                        <a href="{{ $cat->url() }}"
                           class="tn-cat-link {{ isset($category_slug) && $category_slug === $cat->slug() ? 'tn-cat-link-active' : '' }}">
                            {{ $cat->name() }}
                        </a>
                        <span class="tn-filter-count">{{ $cat->count() }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="tn-sidebar-card">
                <div class="tn-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="tn-tag">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
