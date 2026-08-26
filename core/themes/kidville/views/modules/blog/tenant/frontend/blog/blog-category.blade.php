@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name ?? __('Blog Category') }} @endsection

@section('content')

<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ $category_name ?? __('Blog Category') }}</h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ $category_name ?? __('Category') }}</span>
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
                        <div class="kv-blog-card">
                            <div class="kv-blog-thumb">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;background:var(--kv-light);">
                                        <i class="las la-newspaper" style="font-size:48px;color:var(--kv-muted);"></i>
                                    </div>
                                @endif
                                @if($post->category())
                                    <span style="position:absolute;top:12px;left:12px;background:var(--kv-red);color:#fff;font-size:10px;font-weight:800;letter-spacing:.5px;padding:4px 12px;border-radius:var(--kv-radius-sm);text-transform:uppercase;">
                                        {{ $post->category()->name() }}
                                    </span>
                                @endif
                            </div>
                            <div class="kv-blog-body">
                                <div class="kv-blog-meta">
                                    <span><i class="las la-calendar-alt"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="las la-user"></i> {{ $post->author() }}</span>
                                    @endif
                                </div>
                                <h3 class="kv-blog-title">
                                    <a href="{{ $post->url() }}">{!! $post->title() !!}</a>
                                </h3>
                                <p class="kv-blog-excerpt">{{ $post->excerpt(18) }}</p>
                                <a href="{{ $post->url() }}" class="kv-btn kv-btn-outline kv-btn-sm">
                                    {{ __('Read More') }} <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:60px 20px;background:var(--kv-light);border-radius:var(--kv-radius);border:2px dashed var(--kv-border);">
                            <i class="las la-book-open" style="font-size:52px;color:var(--kv-muted);display:block;margin-bottom:16px;"></i>
                            <p style="color:var(--kv-muted);font-weight:600;">{{ __('No posts in this category yet.') }}</p>
                            <a href="{{ theme_blog_url() }}" class="kv-btn kv-btn-red" style="margin-top:16px;">
                                {{ __('Browse All Posts') }}
                            </a>
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
            <div class="kv-sidebar-card" style="margin-bottom:24px;">
                <div class="kv-sidebar-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);overflow:hidden;">
                        <input type="text" name="search" placeholder="{{ __('Search articles…') }}"
                               style="flex:1;padding:10px 14px;border:0;font-size:13px;outline:none;color:var(--kv-dark);background:#fff;">
                        <button type="submit" style="background:var(--kv-red);border:0;color:#fff;padding:0 16px;cursor:pointer;font-size:18px;transition:background .2s;"
                                onmouseover="this.style.background='var(--kv-dark)'" onmouseout="this.style.background='var(--kv-red)'">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div class="kv-sidebar-card" style="margin-bottom:24px;">
                <div class="kv-sidebar-title">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach(theme_blog_categories() as $cat)
                    @php $is_active = ($category_name ?? '') === $cat->name(); @endphp
                    <li style="border-bottom:1px dashed var(--kv-border);padding:8px 0;">
                        <a href="{{ $cat->url() }}"
                           style="display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600;color:{{ $is_active ? 'var(--kv-red)' : 'var(--kv-dark)' }};text-decoration:none;transition:color .2s;"
                           onmouseover="this.style.color='var(--kv-red)'" onmouseout="this.style.color='{{ $is_active ? 'var(--kv-red)' : 'var(--kv-dark)' }}'">
                            <span>{{ $cat->name() }}</span>
                            <span style="background:{{ $is_active ? 'var(--kv-red)' : 'var(--kv-light)' }};color:{{ $is_active ? '#fff' : 'var(--kv-red)' }};font-size:10px;font-weight:800;padding:2px 10px;border-radius:50px;">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div class="kv-sidebar-card">
                <div class="kv-sidebar-title">{{ __('Popular Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}"
                       style="display:inline-block;padding:5px 14px;background:var(--kv-light);border:2px solid var(--kv-border);border-radius:50px;font-size:12px;font-weight:700;color:var(--kv-muted);text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.background='var(--kv-red)';this.style.color='#fff';this.style.borderColor='var(--kv-red)'"
                       onmouseout="this.style.background='var(--kv-light)';this.style.color='var(--kv-muted)';this.style.borderColor='var(--kv-border)'">
                        #{{ $tag->name() }}
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

@endsection
