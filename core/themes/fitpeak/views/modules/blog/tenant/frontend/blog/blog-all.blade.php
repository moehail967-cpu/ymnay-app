@extends('tenant.frontend.frontend-page-master')

@php
    $blog_page_title = __('Blog');
    $blog_page_id = get_static_option('blog_page');
    if ($blog_page_id) {
        $obj = \App\Models\Page::find($blog_page_id);
        if ($obj) { $blog_page_title = $obj->title; }
    }
@endphp

@section('title') {{ $blog_page_title }} @endsection
@section('page-title') {{ $blog_page_title }} @endsection

@section('content')
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{{ $blog_page_title }}</h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li>{{ $blog_page_title }}</li>
        </ul>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div class="fp-blog-card">
                            <div class="fp-blog-thumb">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;">
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;">
                                        <i class="mdi mdi-dumbbell" style="font-size:48px;color:var(--fp-green);opacity:.3;"></i>
                                    </div>
                                @endif
                                @if($post->category())
                                    <span class="fp-blog-cat-pill">{{ $post->category()->name() }}</span>
                                @endif
                            </div>
                            <div class="fp-blog-body">
                                <div class="fp-blog-meta">
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
                                <div class="fp-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="fp-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="fp-blog-read-more">
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

            {{-- Pagination --}}
            @if(theme_blogs_paginator()?->hasPages())
                <div class="fp-pagination mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="fp-price-input flex-grow-1" placeholder="{{ __('Search articles…') }}">
                    <button type="submit" class="fp-btn fp-btn-primary fp-btn-sm" style="white-space:nowrap;">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <label class="fp-filter-item" style="cursor:pointer;">
                        <a href="{{ $cat->url() }}" style="color:var(--fp-muted);text-decoration:none;">{{ $cat->name() }}</a>
                        <span class="fp-filter-count">{{ $cat->count() }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="fp-tag">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
