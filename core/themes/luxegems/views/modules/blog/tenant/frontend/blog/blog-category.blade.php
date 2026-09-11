@extends('tenant.frontend.frontend-page-master')
@section('title') {{ $category_name ?? __('Blog') }} @endsection

@section('content')
<div class="lg-breadcrumb-bar">
    <div class="container">
        <div class="lg-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span class="sep">/</span>
            <span class="active">{{ $category_name ?? '' }}</span>
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
                    <div class="lg-blog-card">
                        <div class="lg-blog-thumb">
                            @if($post->has_image())
                                <a href="{{ $post->url() }}">
                                    <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                         style="width:100%;height:100%;object-fit:cover;">
                                </a>
                            @else
                                <span style="font-size:48px;color:var(--lx-gold);">◈</span>
                            @endif
                        </div>
                        <div class="lg-blog-body">
                            <div class="lg-blog-meta">
                                <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                @if($post->author())
                                    <span><i class="las la-user"></i> {{ $post->author() }}</span>
                                @endif
                            </div>
                            <div class="lg-blog-title"><a href="{{ $post->url() }}">{!! $post->title() !!}</a></div>
                            <p class="lg-blog-excerpt">{{ $post->excerpt(20) }}</p>
                            <a href="{{ $post->url() }}" class="lg-blog-read-more">
                                {{ __('Read More') }} <i class="las la-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div style="text-align:center;padding:48px;color:var(--lx-muted);">
                        <i class="las la-file-alt" style="font-size:40px;display:block;margin-bottom:12px;color:var(--lx-gold);"></i>
                        {{ __('No posts found in this category.') }}
                    </div>
                </div>
                @endforelse
            </div>

            @if(theme_blogs_paginator()?->hasPages())
            <div class="lg-pagination mt-4">{{ theme_blogs_paginator()->links() }}</div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="lg-sidebar-card">
                <div class="lg-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="lg-form-control flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="lx-btn lx-btn-primary" style="white-space:nowrap;padding:10px 14px;">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="lg-sidebar-card">
                <div class="lg-sidebar-title">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div class="lg-blog-filter-item {{ ($cat->name() === ($category_name ?? '')) ? 'active' : '' }}">
                    <a href="{{ $cat->url() }}" style="color:inherit;text-decoration:none;">{{ $cat->name() }}</a>
                    <span class="lg-filter-count">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="lg-sidebar-card">
                <div class="lg-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="lg-tag">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
