@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name }} @endsection
@section('page-title') {{ $category_name }} @endsection

@section('content')
<div class="bp-page-banner">
    <div class="container">
        <h1>{{ $category_name }}</h1>
        <div class="bp-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span><i class="las la-angle-right"></i></span>
            <span class="current">{{ $category_name }}</span>
        </div>
    </div>
</div>

<div class="container bp-blog-section">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse($blogs as $post)
                    <div class="col-md-6">
                        <div class="bp-blog-card">
                            <div class="bp-blog-img">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <span class="bp-blog-placeholder"><i class="las la-book-open"></i></span>
                                @endif
                                @if($post->category())
                                    <a href="{{ $post->category()->url() }}" class="bp-blog-cat">{{ $post->category()->name() }}</a>
                                @endif
                            </div>
                            <div class="bp-blog-body">
                                <div class="bp-blog-meta">
                                    <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="las la-user"></i> {{ $post->author() }}</span>
                                    @endif
                                </div>
                                <div class="bp-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </div>
                                <p class="bp-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="bp-btn bp-btn-outline" style="font-size:13px;padding:8px 18px;">
                                    {{ __('Read More') }} <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="bp-blog-empty">
                            <i class="las la-book-open"></i>
                            <p>{{ __('No posts in this category.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($blogs->hasPages())
                <div class="bp-pagination mt-4">
                    {{ $blogs->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="bp-sb-card">
                <div class="bp-sb-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" class="d-flex gap-2">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" class="bp-input flex-grow-1" placeholder="{{ __('Search blogs…') }}">
                    <button type="submit" class="bp-btn bp-btn-green bp-sb-search-btn">
                        <i class="las la-search"></i>
                    </button>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="bp-sb-card">
                <div class="bp-sb-title">{{ __('Categories') }}</div>
                @foreach(theme_blog_categories() as $cat)
                <div class="bp-sb-cat-row">
                    <a href="{{ $cat->url() }}" class="bp-sb-cat-link">{{ $cat->name() }}</a>
                    <span class="bp-filter-count">{{ $cat->count() }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="bp-sb-card">
                <div class="bp-sb-title">{{ __('Tags') }}</div>
                <div class="d-flex flex-wrap gap-2">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="bp-tag-pill">{{ $tag->name() }}</a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
