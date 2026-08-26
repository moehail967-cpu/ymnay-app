@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name ?? __('Blog Category') }} @endsection
@section('page-title') {{ $category_name ?? __('Blog Category') }} @endsection

@section('content')
<div class="ph-page-banner">
    <div class="container">
        <h1>{{ $category_name ?? __('Blog Category') }}</h1>
        <div class="ph-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_blog_url() }}" style="color:var(--ph-terra);">{{ __('Blog') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ $category_name ?? __('Category') }}</span>
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
                        <div class="ph-blog-card">
                            <div class="ph-blog-thumb">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}"><img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"></a>
                                @else
                                    <i class="las la-paw" style="font-size:48px;color:var(--ph-terra);"></i>
                                @endif
                                @if($post->category())
                                    <span class="ph-blog-cat-pill">{{ $post->category()->name() }}</span>
                                @endif
                            </div>
                            <div class="ph-blog-body">
                                <div class="ph-blog-meta">
                                    <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                    @if($post->author())<span><i class="las la-user"></i> {{ $post->author() }}</span>@endif
                                </div>
                                <div class="ph-blog-title"><a href="{{ $post->url() }}">{!! $post->title() !!}</a></div>
                                <p class="ph-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="ph-blog-read-more">
                                    {{ __('Read More') }} <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:60px;border:2px dashed var(--ph-border);border-radius:var(--ph-radius);">
                            <i class="las la-paw" style="font-size:48px;color:var(--ph-border);display:block;margin-bottom:12px;"></i>
                            <p style="color:var(--ph-muted);font-size:14px;">{{ __('No posts found in this category.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
            @if(theme_blogs_paginator()?->hasPages())
                <div class="ph-pagination mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="ph-price-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="ph-btn ph-btn-terra ph-btn-sm"><i class="las la-search"></i></button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <label class="ph-filter-item" style="cursor:pointer;">
                        <a href="{{ $cat->url() }}"
                           style="color:{{ $cat->name() === ($category_name ?? '') ? 'var(--ph-terra)' : 'inherit' }};font-weight:{{ $cat->name() === ($category_name ?? '') ? '800' : '600' }};text-decoration:none;">
                            {{ $cat->name() }}
                        </a>
                        <span class="ph-filter-count">{{ $cat->count() }}</span>
                    </label>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="ph-sidebar-card">
                <div class="ph-sidebar-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="ph-tag">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
