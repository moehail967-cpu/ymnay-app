@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Blog') }} @endsection
@section('page-title') {{ __('Blog') }} @endsection

@section('content')
<div class="fm-page-banner">
    <div class="container">
        <h1 style="font-size:28px;font-weight:800;color:var(--fm-dark);margin-bottom:8px;">{{ __('Fresh Stories & Tips') }}</h1>
        <div class="fm-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="las la-angle-right" style="font-size:11px;"></i></span>
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
                        <div class="fm-blog-card">
                            <div class="fm-blog-thumb">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <span style="font-size:64px;">🥗</span>
                                @endif
                                @if($post->category())
                                    <span class="fm-blog-cat-badge" style="position:absolute;top:12px;left:12px;">{{ $post->category()->name() }}</span>
                                @endif
                            </div>
                            <div class="fm-blog-body">
                                <div class="fm-blog-meta">
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
                                <div class="fm-blog-title mt-2">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="fm-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="fm-read-more">
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
                <div class="fm-pagination mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="fm-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="fm-btn fm-btn-green fm-btn-sm" style="white-space:nowrap;">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                    <a href="{{ $cat->url() }}" class="fm-cat-link">
                        {{ $cat->name() }}
                        <span class="fm-filter-count">{{ $cat->count() }}</span>
                    </a>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="fm-sidebar-card">
                <div class="fm-sidebar-title">{{ __('Tags') }}</div>
                <div class="fm-tag-cloud">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="fm-tag">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
