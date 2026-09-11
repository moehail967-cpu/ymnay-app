@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name ?? __('Category') }} @endsection
@section('page-title') {{ $category_name ?? __('Category') }} @endsection

@section('content')
<div class="fp-page-hero">
    <div class="container">
        <h1 class="fp-page-title">{{ $category_name }}</h1>
        <ul class="fp-breadcrumb-list">
            <li><a href="{{ theme_home_url() }}">{{ __('Home') }}</a></li>
            <li><a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a></li>
            <li>{{ $category_name }}</li>
        </ul>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:72px;">
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
                                <span class="fp-blog-cat-pill">{{ $category_name }}</span>
                            </div>
                            <div class="fp-blog-body">
                                <div class="fp-blog-meta">
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                    @if($post->author())<span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span>@endif
                                </div>
                                <div class="fp-blog-title"><a href="{{ $post->url() }}">{!! $post->title() !!}</a></div>
                                <p class="fp-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="fp-blog-read-more">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12" style="text-align:center;padding:60px;color:var(--fp-muted);">
                        <i class="mdi mdi-post-outline" style="font-size:64px;color:var(--fp-border);display:block;margin-bottom:16px;"></i>
                        <p style="font-family:var(--fp-font-head);text-transform:uppercase;letter-spacing:1px;">{{ __('No posts in this category') }}</p>
                    </div>
                @endforelse
            </div>
            @if(theme_blogs_paginator()?->hasPages())
                <div class="fp-pagination mt-4">{{ theme_blogs_paginator()->links() }}</div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="fp-sidebar-card">
                <div class="fp-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="fp-price-input flex-grow-1" placeholder="{{ __('Search…') }}">
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
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--fp-border);">
                        <a href="{{ $cat->url() }}"
                           style="font-family:var(--fp-font-head);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1px;text-decoration:none;transition:color .2s;{{ ($category_name === $cat->name()) ? 'color:var(--fp-green);' : 'color:var(--fp-text);' }}">
                            {{ $cat->name() }}
                        </a>
                        <span class="fp-filter-count">{{ $cat->count() }}</span>
                    </div>
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
