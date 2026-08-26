@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name }} @endsection
@section('page-title') {{ $category_name }} @endsection

@section('content')
<div class="ar-page-banner">
    <div class="container">
        <h1>{{ $category_name }}</h1>
        <div class="ar-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span>/</span>
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
                        <div style="text-align:center;padding:40px;color:var(--ar-muted);">
                            <i class="las la-flask" style="font-size:48px;display:block;margin-bottom:12px;opacity:.3;"></i>
                            {{ __('No posts in this category.') }}
                        </div>
                    </div>
                @endforelse
            </div>

            @if($blogs->hasPages())
                <div class="mt-4">
                    {{ $blogs->links() }}
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
                @foreach(theme_blog_categories() as $cat)
                <div class="ar-sb-cat-row">
                    <a href="{{ $cat->url() }}" class="ar-sb-cat-link">{{ $cat->name() }}</a>
                    <span class="ar-filter-count">{{ $cat->count() }}</span>
                </div>
                @endforeach
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

        </div>
    </div>
</div>
@endsection
