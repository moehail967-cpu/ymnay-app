@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Blog') }} @endsection
@section('page-title') {{ __('Blog') }} @endsection

@section('content')
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ __('Blog') }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <span class="current">{{ __('Blog') }}</span>
        </div>
    </div>
</div>

<div class="container ar-blog-section">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div class="ar-blog-card">
                            <div class="ar-blog-img">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <i class="las la-flask" style="font-size:48px;color:var(--ar-red);opacity:.3;"></i>
                                @endif
                                @if($post->category())
                                    <a href="{{ $post->category()->url() }}" class="ar-blog-cat">{{ $post->category()->name() }}</a>
                                @endif
                            </div>
                            <div class="ar-blog-body">
                                <div class="ar-blog-meta">
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
                                <div class="ar-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="ar-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="ar-btn ar-btn-outline ar-btn-sm">
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

            @if(theme_blogs_paginator()?->hasPages())
                <div class="mt-4">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="ar-sb-card">
                <div class="ar-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="ar-sidebar-search">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit"><i class="mdi mdi-magnify"></i></button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="ar-sb-card">
                <div class="ar-sb-title">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <div class="ar-sb-cat-row">
                        <a href="{{ $cat->url() }}" class="ar-sb-cat-link">{{ $cat->name() }}</a>
                        <span class="ar-filter-count">{{ $cat->count() }}</span>
                    </div>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="ar-sb-card">
                <div class="ar-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="ar-tag-pill">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

            @php $recent_posts = theme_blogs()->take(4); @endphp
            @if($recent_posts->isNotEmpty())
            <div class="ar-sb-card">
                <div class="ar-sb-title">{{ __('Recent Posts') }}</div>
                @foreach($recent_posts as $rpost)
                <div class="ar-recent-post">
                    @if($rpost->has_image())
                    <div class="ar-recent-thumb">
                        <a href="{{ $rpost->url() }}">
                            <img src="{{ $rpost->image_url() }}" alt="{{ $rpost->title() }}">
                        </a>
                    </div>
                    @endif
                    <div style="flex:1;min-width:0;">
                        <a href="{{ $rpost->url() }}" class="ar-recent-title">{{ \Illuminate\Support\Str::words($rpost->title(), 6) }}</a>
                        <span class="ar-recent-date"><i class="mdi mdi-calendar-outline"></i> {{ $rpost->date() }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
