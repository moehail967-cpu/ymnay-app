@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name ?? __('Blog Category') }} @endsection
@section('page-title') {{ $category_name ?? __('Blog Category') }} @endsection

@section('content')
<div class="el-blog-hero">
    <div class="container">
        <h2>{{ $category_name ?? __('Blog Category') }}</h2>
        <div class="el-dash-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="las la-angle-right" style="font-size:11px;"></i>
            <a href="{{ theme_blog_url() }}" style="color:#E8603C;font-weight:600;text-decoration:none;">{{ __('Blog') }}</a>
            <i class="las la-angle-right" style="font-size:11px;"></i>
            <span>{{ $category_name ?? __('Category') }}</span>
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
                    <div class="el-blog-card">
                        <div class="el-blog-thumb">
                            @if($post->has_image())
                                <a href="{{ $post->url() }}"><img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"></a>
                            @else
                                <div class="el-blog-thumb-ph"><i class="las la-newspaper"></i></div>
                            @endif
                            @if($post->category())
                                <a href="{{ $post->category()->url() }}" class="el-blog-cat-badge">{{ $post->category()->name() }}</a>
                            @endif
                        </div>
                        <div class="el-blog-body">
                            <div class="el-blog-meta">
                                <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                @if($post->author())<span><i class="las la-user"></i> {{ $post->author() }}</span>@endif
                            </div>
                            <div class="el-blog-title"><a href="{{ $post->url() }}">{!! $post->title() !!}</a></div>
                            <p class="el-blog-excerpt">{{ $post->excerpt(18) }}</p>
                            <a href="{{ $post->url() }}" class="el-blog-read-more">{{ __('Read More') }} <i class="las la-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="el-blog-empty">
                        <i class="las la-newspaper"></i>
                        <p>{{ __('No posts found in this category.') }}</p>
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
            <div class="el-blog-sidebar-card">
                <div class="el-blog-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div class="el-blog-search-row">
                        <input type="text" name="search" placeholder="{{ __('Search articles…') }}">
                        <button type="submit" class="el-blog-search-btn"><i class="las la-search"></i></button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div class="el-blog-sidebar-card">
                <div class="el-blog-sidebar-title">{{ __('Categories') }}</div>
                <ul class="el-blog-cat-list">
                    @foreach(theme_blog_categories() as $cat)
                    <li class="el-blog-cat-item">
                        <a href="{{ $cat->url() }}" class="el-blog-cat-link {{ $cat->name() === ($category_name ?? '') ? 'active' : '' }}">
                            <span><i class="las la-tag" style="color:#E8603C;margin-right:6px;"></i>{{ $cat->name() }}</span>
                            <span class="el-blog-cat-count">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div class="el-blog-sidebar-card">
                <div class="el-blog-sidebar-title">{{ __('Popular Tags') }}</div>
                <div class="el-blog-tag-cloud">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}" class="el-blog-tag">#{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
