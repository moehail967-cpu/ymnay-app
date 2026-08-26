@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name }} @endsection
@section('page-title') {{ $category_name }} @endsection

@section('content')
<div class="bk-page-banner">
    <div class="container">
        <h1>{{ $category_name }}</h1>
        <div class="bk-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <a href="{{ theme_blog_url() }}" style="color:inherit;text-decoration:none;">{{ __('Blog') }}</a>
            <span><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ $category_name }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:48px;padding-bottom:80px;">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse($blogs as $post)
                    <div class="col-md-6">
                        <div class="bk-blog-card">
                            <div class="bk-blog-img" style="height:200px;">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;">
                                    </a>
                                @else
                                    <span style="font-size:48px;">🥐</span>
                                @endif
                                @if($post->category())
                                    <a href="{{ $post->category()->url() }}" class="bk-blog-cat">{{ $post->category()->name() }}</a>
                                @endif
                            </div>
                            <div class="bk-blog-body">
                                <div class="bk-blog-meta">
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span>
                                    @endif
                                </div>
                                <div class="bk-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p style="font-size:14px;color:var(--bk-muted);margin-bottom:12px;">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="bk-btn bk-btn-outline" style="font-size:13px;padding:8px 18px;">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:40px;color:var(--bk-muted);">
                            <i class="mdi mdi-post-outline" style="font-size:48px;display:block;margin-bottom:12px;"></i>
                            {{ __('No posts in this category.') }}
                        </div>
                    </div>
                @endforelse
            </div>

            @if($blogs->hasPages())
                <div class="bk-pagination mt-4">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="bk-search">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit"><i class="mdi mdi-magnify"></i></button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div class="bk-sb-cat-row">
                    <a href="{{ $cat->url() }}">{{ $cat->name() }}</a>
                    <span class="bk-filter-count">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="bk-sb-card">
                <div class="bk-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="bk-tag-pill">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
