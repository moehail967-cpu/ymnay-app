@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Blog') }} @endsection
@section('page-title') {{ __('Blog') }} @endsection

@section('content')
<div class="sz-page-banner">
    <div class="container">
        <h1>{{ __('Sports Blog') }}</h1>
        <div class="sz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep"><i class="mdi mdi-chevron-right"></i></span>
            <span class="current">{{ __('Blog') }}</span>
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
                        <div style="background:var(--sz-white);border:2px solid var(--sz-border);border-radius:var(--sz-radius-xl);overflow:hidden;transition:all .2s;"
                             onmouseover="this.style.borderColor='var(--sz-red)';this.style.boxShadow='var(--sz-shadow)';this.style.transform='translateY(-3px)'"
                             onmouseout="this.style.borderColor='var(--sz-border)';this.style.boxShadow='none';this.style.transform='translateY(0)'">
                            <div style="aspect-ratio:16/9;overflow:hidden;background:var(--sz-red-light);position:relative;">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                                             onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;"><i class="mdi mdi-dumbbell" style="font-size:48px;color:var(--sz-red);opacity:.3;"></i></div>
                                @endif
                                @if($post->category())
                                    <span style="position:absolute;top:12px;left:12px;background:var(--sz-red);color:#fff;font-family:var(--sz-font-head);font-size:10px;font-weight:400;padding:4px 12px;text-transform:uppercase;letter-spacing:1.5px;">
                                        {{ $post->category()->name() }}
                                    </span>
                                @endif
                            </div>
                            <div style="padding:20px;">
                                <div style="display:flex;align-items:center;gap:14px;font-size:12px;color:var(--sz-muted);margin-bottom:10px;font-family:var(--sz-font-head);text-transform:uppercase;letter-spacing:.5px;">
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                    @if($post->author()) <span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span> @endif
                                    @if($post->has_comments())
                                        <span><a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;"><i class="mdi mdi-comment-outline"></i> {{ $post->comment_count() }}</a></span>
                                    @endif
                                </div>
                                <h3 style="font-family:var(--sz-font-head);font-size:17px;font-weight:700;color:var(--sz-dark);margin-bottom:8px;line-height:1.3;text-transform:uppercase;letter-spacing:.5px;">
                                    <a href="{{ $post->url() }}" style="color:inherit;text-decoration:none;">{!! $post->title() !!}</a>
                                </h3>
                                <p style="font-size:13px;color:var(--sz-muted);margin-bottom:14px;line-height:1.6;">{{ $post->excerpt(18) }}</p>
                                <a href="{{ $post->url() }}" class="sz-btn sz-btn-outline sz-btn-sm">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:60px 20px;background:var(--sz-white);border:2px dashed var(--sz-border);border-radius:var(--sz-radius-xl);">
                            <i class="mdi mdi-newspaper-variant-outline" style="font-size:48px;color:var(--sz-border);display:block;margin-bottom:12px;"></i>
                            <p style="color:var(--sz-muted);">{{ __('No Blog Available') }}</p>
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

            <div class="sz-sidebar" style="margin-bottom:24px;">
                <div class="sz-sidebar-head"><i class="mdi mdi-magnify"></i> {{ __('Search') }}</div>
                <div class="sz-sidebar-block">
                    <form action="{{ theme_blog_search_url() }}" method="GET">
                        <div style="display:flex;border:2px solid var(--sz-border);border-radius:var(--sz-radius);overflow:hidden;">
                            <input type="text" name="search" placeholder="{{ __('Search articles…') }}"
                                   style="flex:1;padding:9px 14px;border:0;font-size:13px;font-family:var(--sz-font-body);outline:none;">
                            <button type="submit" style="background:var(--sz-red);border:0;color:#fff;padding:0 16px;cursor:pointer;font-size:16px;">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="sz-sidebar" style="margin-bottom:24px;">
                <div class="sz-sidebar-head"><i class="mdi mdi-tag-outline"></i> {{ __('Categories') }}</div>
                <div class="sz-sidebar-block">
                    <ul style="list-style:none;margin:0;padding:0;">
                        @foreach(theme_blog_categories() as $cat)
                        <li style="border-bottom:1px solid var(--sz-border);padding:8px 0;display:flex;align-items:center;justify-content:space-between;">
                            <a href="{{ $cat->url() }}" style="font-family:var(--sz-font-head);font-size:13px;color:var(--sz-dark);text-decoration:none;text-transform:uppercase;letter-spacing:1px;display:flex;align-items:center;gap:6px;transition:color .2s;"
                               onmouseover="this.style.color='var(--sz-red)'" onmouseout="this.style.color='var(--sz-dark)'">
                                <i class="mdi mdi-chevron-right" style="color:var(--sz-red);"></i> {{ $cat->name() }}
                            </a>
                            <span style="background:var(--sz-red-light);color:var(--sz-red);font-family:var(--sz-font-head);font-size:11px;padding:2px 10px;border-radius:2px;">{{ $cat->count() }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div class="sz-sidebar">
                <div class="sz-sidebar-head"><i class="mdi mdi-pound"></i> {{ __('Popular Tags') }}</div>
                <div class="sz-sidebar-block">
                    <div style="display:flex;flex-wrap:wrap;gap:8px;">
                        @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}"
                           style="display:inline-block;padding:5px 12px;background:var(--sz-bg);border:2px solid var(--sz-border);font-family:var(--sz-font-head);font-size:11px;color:var(--sz-muted);text-decoration:none;text-transform:uppercase;letter-spacing:1px;transition:all .2s;"
                           onmouseover="this.style.background='var(--sz-red)';this.style.color='#fff';this.style.borderColor='var(--sz-red)'"
                           onmouseout="this.style.background='var(--sz-bg)';this.style.color='var(--sz-muted)';this.style.borderColor='var(--sz-border)'">
                            #{{ $tag->name() }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
