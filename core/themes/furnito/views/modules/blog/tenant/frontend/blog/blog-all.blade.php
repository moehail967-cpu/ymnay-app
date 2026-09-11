@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Blog') }} @endsection
@section('page-title') {{ __('Blog') }} @endsection

@section('content')
<div class="fn-page-banner">
    <div class="container">
        <h1>{{ __('Blog') }}</h1>
        <div class="fn-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ __('Blog') }}</span>
        </div>
    </div>
</div>

<div class="container fn-blog-section">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div class="fn-blog-card">
                            <div class="fn-blog-img">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <span class="fn-blog-placeholder"><i class="las la-pen-nib"></i></span>
                                @endif
                                @if($post->category())
                                    <a href="{{ $post->category()->url() }}" class="fn-blog-cat">{{ $post->category()->name() }}</a>
                                @endif
                            </div>
                            <div class="fn-blog-body">
                                <div class="fn-blog-meta">
                                    <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="las la-user"></i> {{ $post->author() }}</span>
                                    @endif
                                    @if($post->has_comments())
                                        <span>
                                            <a href="{{ $post->comment_url() }}">
                                                <i class="las la-comment"></i> {{ $post->comment_count() }}
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                <div class="fn-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="fn-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="fn-btn fn-btn-outline fn-blog-read-more">
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
                <div class="fn-pagination mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="fn-sb-card">
                <div class="fn-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="fn-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="fn-btn fn-btn-gold fn-sb-search-btn">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="fn-sb-card">
                <div class="fn-sb-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <div class="fn-sb-cat-row">
                        <a href="{{ $cat->url() }}" class="fn-sb-cat-link">{{ $cat->name() }}</a>
                        <span class="fn-filter-count">{{ $cat->count() }}</span>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="fn-sb-card">
                <div class="fn-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="fn-tag-pill">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

            @php $recent_posts = theme_blogs()->take(4); @endphp
            @if($recent_posts->isNotEmpty())
            <div class="fn-sb-card">
                <div class="fn-sb-title">{{ __('Recent Posts') }}</div>
                @foreach($recent_posts as $rpost)
                <div class="fn-recent-post">
                    @if($rpost->has_image())
                    <div class="fn-recent-thumb">
                        <a href="{{ $rpost->url() }}">
                            <img src="{{ $rpost->image_url() }}" alt="{{ $rpost->title() }}">
                        </a>
                    </div>
                    @endif
                    <div class="fn-recent-info">
                        <a href="{{ $rpost->url() }}" class="fn-recent-title">{{ \Illuminate\Support\Str::words($rpost->title(), 6) }}</a>
                        <span class="fn-recent-date"><i class="las la-calendar"></i> {{ $rpost->date() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
