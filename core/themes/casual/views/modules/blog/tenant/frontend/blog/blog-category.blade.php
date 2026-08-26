@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name }} @endsection
@section('page-title') {{ $category_name }} @endsection

@section('content')

<div class="cs-page-banner">
    <div class="container">
        <h1 class="cs-page-banner-title">{{ $category_name }}</h1>
        <div class="cs-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <a href="{{ theme_blog_url() }}" class="cs-breadcrumb-link">{{ __('Blog') }}</a>
            <span class="cs-breadcrumb-sep"><i class="las la-angle-right"></i></span>
            <span class="cs-breadcrumb-current">{{ $category_name }}</span>
        </div>
    </div>
</div>

<div class="cs-blog-section">
    <div class="container">
        <div class="row g-4">

            {{-- Blog Grid --}}
            <div class="col-lg-8">
                <div class="row g-4">
                    @forelse($blogs as $post)
                    <div class="col-md-6">
                        <div class="cs-blog-card">
                            <div class="cs-blog-thumb">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <div class="cs-blog-thumb-placeholder">
                                        <i class="las la-newspaper"></i>
                                    </div>
                                @endif
                                @if($post->category())
                                    <a href="{{ $post->category()->url() }}" class="cs-blog-cat">{{ $post->category()->name() }}</a>
                                @endif
                            </div>
                            <div class="cs-blog-body">
                                <div class="cs-blog-meta">
                                    <span><i class="las la-calendar-alt"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="las la-user"></i> {{ $post->author() }}</span>
                                    @endif
                                </div>
                                <h3 class="cs-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </h3>
                                <p class="cs-blog-excerpt">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" class="cs-blog-read-more">
                                    {{ __('Read More') }} <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="cs-dash-empty">
                            <i class="las la-newspaper cs-dash-empty-icon"></i>
                            <p class="cs-dash-empty-text">{{ __('No posts in this category.') }}</p>
                            <a href="{{ theme_blog_url() }}" class="cs-dash-empty-btn">{{ __('All Posts') }}</a>
                        </div>
                    </div>
                    @endforelse
                </div>

                @if($blogs->hasPages())
                <div class="cs-blog-pagination mt-4">
                    {{ $blogs->links() }}
                </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                @include('theme::modules.blog.tenant.frontend.blog.partials.sidebar')
            </div>

        </div>
    </div>
</div>

@endsection
