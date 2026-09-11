@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name ?? __('Blog Category') }} @endsection
@section('page-title') {{ $category_name ?? __('Blog Category') }} @endsection

@section('content')
<div class="tz-page-banner">
    <div class="container tz-page-banner-content">
        <h1>{{ $category_name ?? __('Blog') }}</h1>
        <div class="tz-breadcrumb">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <i class="mdi mdi-chevron-right sep"></i>
            <span class="current">{{ $category_name ?? '' }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div style="font-size:18px;font-weight:700;color:#fff;margin-bottom:20px;display:flex;align-items:center;gap:8px;">
                <i class="mdi mdi-chip" style="color:var(--tz-blue);"></i>
                {{ $category_name ?? __('Posts') }}
            </div>
            <div class="row g-3">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);overflow:hidden;transition:border-color .2s;"
                             onmouseover="this.style.borderColor='var(--tz-blue)'" onmouseout="this.style.borderColor='var(--tz-border)'">
                            <div style="aspect-ratio:16/9;overflow:hidden;background:var(--tz-mid);position:relative;">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;transition:transform .3s;"
                                             onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:40px;color:var(--tz-muted);">
                                        <i class="mdi mdi-newspaper-variant-outline"></i>
                                    </div>
                                @endif
                            </div>
                            <div style="padding:16px;">
                                <div style="display:flex;align-items:center;gap:12px;font-size:11px;color:var(--tz-muted);margin-bottom:8px;flex-wrap:wrap;">
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span>
                                    @endif
                                </div>
                                <h3 style="font-size:14px;font-weight:700;margin-bottom:8px;line-height:1.4;">
                                    <a href="{{ $post->url() }}" style="color:#fff;text-decoration:none;transition:color .2s;"
                                       onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='#fff'">
                                        {{ $post->title() }}
                                    </a>
                                </h3>
                                <a href="{{ $post->url() }}" style="font-size:12px;font-weight:600;color:var(--tz-blue);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12" style="text-align:center;padding:60px 0;color:var(--tz-muted);">
                        <i class="mdi mdi-newspaper-variant-outline" style="font-size:48px;display:block;margin-bottom:12px;"></i>
                        {{ __('No posts in this category yet.') }}
                    </div>
                @endforelse
            </div>

            @if(theme_blogs_paginator()?->hasPages())
                <div style="margin-top:28px;">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:20px;">
                <div style="font-size:13px;font-weight:700;color:var(--tz-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);overflow:hidden;background:var(--tz-mid);">
                        <input type="text" name="search" placeholder="{{ __('Search posts…') }}"
                               style="flex:1;padding:9px 12px;border:0;background:transparent;color:var(--tz-text);font-size:13px;font-family:var(--tz-font);outline:none;">
                        <button type="submit" style="background:var(--tz-blue);border:0;color:#fff;padding:0 14px;cursor:pointer;font-size:16px;">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;margin-bottom:20px;">
                <div style="font-size:13px;font-weight:700;color:var(--tz-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach(theme_blog_categories() as $cat)
                    <li style="border-bottom:1px solid var(--tz-border);padding:8px 0;">
                        <a href="{{ $cat->url() }}" style="display:flex;align-items:center;justify-content:space-between;font-size:13px;color:var(--tz-muted);text-decoration:none;"
                           onmouseover="this.style.color='var(--tz-blue)'" onmouseout="this.style.color='var(--tz-muted)'">
                            <span><i class="mdi mdi-chip" style="color:var(--tz-blue);margin-right:6px;"></i>{{ $cat->name() }}</span>
                            <span style="background:var(--tz-blue-glow);color:var(--tz-blue);font-size:10px;font-weight:700;padding:2px 8px;border-radius:var(--tz-radius-sm);">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius);padding:20px;">
                <div style="font-size:13px;font-weight:700;color:var(--tz-text);text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}"
                       style="display:inline-block;padding:4px 12px;background:var(--tz-mid);border:1px solid var(--tz-border);border-radius:var(--tz-radius-sm);font-size:11px;font-weight:600;color:var(--tz-muted);text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.background='var(--tz-blue)';this.style.color='#fff';this.style.borderColor='var(--tz-blue)'"
                       onmouseout="this.style.background='var(--tz-mid)';this.style.color='var(--tz-muted)';this.style.borderColor='var(--tz-border)'">
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
