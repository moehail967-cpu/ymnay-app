@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('The Maison Journal') }} @endsection
@section('page-title') {{ __('The Maison Journal') }} @endsection

@section('style')
<style>
.ms-blog-card {
    background: #fff;
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    overflow: hidden;
    box-shadow: var(--ms-shadow);
    transition: box-shadow .25s, transform .25s;
    height: 100%;
}
.ms-blog-card:hover {
    box-shadow: 0 18px 50px -10px rgba(200,184,154,.4);
    transform: translateY(-3px);
}
.ms-blog-card-img {
    aspect-ratio: 16/10;
    overflow: hidden;
    background: var(--ms-warm);
    position: relative;
}
.ms-blog-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s;
}
.ms-blog-card:hover .ms-blog-card-img img { transform: scale(1.05); }
.ms-cat-pill {
    display: inline-block;
    background: var(--ms-linen);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 2px;
}
.ms-cat-pill a { color: inherit; text-decoration: none; }
.ms-sidebar-widget {
    background: #fff;
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--ms-shadow);
}
.ms-widget-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .14em;
    text-transform: uppercase;
    color: var(--ms-muted);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--ms-border);
}
.ms-search-wrap {
    display: flex;
    border: 1px solid var(--ms-border);
    border-radius: var(--ms-radius);
    overflow: hidden;
}
.ms-search-wrap input {
    flex: 1;
    padding: 9px 14px;
    border: 0;
    font-size: 13px;
    font-family: inherit;
    outline: none;
    color: var(--ms-dark);
}
.ms-search-wrap button {
    background: var(--ms-dark);
    border: 0;
    color: #fff;
    padding: 0 16px;
    cursor: pointer;
    font-size: 16px;
    transition: background .2s;
}
.ms-search-wrap button:hover { background: var(--ms-linen-d); }
.ms-tag-pill {
    display: inline-flex;
    align-items: center;
    padding: 5px 13px;
    background: var(--ms-warm);
    border: 1px solid var(--ms-border);
    border-radius: 2px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: var(--ms-muted);
    text-decoration: none;
    transition: all .2s;
}
.ms-tag-pill:hover {
    background: var(--ms-linen);
    color: #fff;
    border-color: var(--ms-linen);
}
</style>
@endsection

@section('content')
{{-- Hero Banner --}}
<div style="background:var(--ms-warm);border-bottom:1px solid var(--ms-border);padding:56px 0 40px;text-align:center;">
    <div class="container">
        <div style="font-size:11px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:var(--ms-muted);margin-bottom:10px;">{{ __('Inspiration & Ideas') }}</div>
        <h1 style="font-size:clamp(28px,5vw,48px);font-weight:300;color:var(--ms-dark);margin:0 auto 14px;max-width:520px;line-height:1.2;">{{ __('The Maison Journal') }}</h1>
        <p style="font-size:14px;color:var(--ms-muted);max-width:400px;margin:0 auto;line-height:1.7;">{{ __('Stories on living beautifully — interiors, materials, craftsmanship and the art of the home.') }}</p>
        <div style="display:flex;align-items:center;justify-content:center;gap:8px;font-size:12px;color:var(--ms-muted);margin-top:16px;">
            <a href="{{ theme_home_url() }}" style="color:var(--ms-linen-d);text-decoration:none;font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;color:var(--ms-border);"></i>
            <span>{{ __('Journal') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:56px 0 80px;">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div class="ms-blog-card">
                            <div class="ms-blog-card-img">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy">
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;">
                                        <i class="mdi mdi-image-outline" style="font-size:52px;color:var(--ms-border);"></i>
                                    </div>
                                @endif
                                @if($post->category())
                                    <div style="position:absolute;top:14px;left:14px;">
                                        <span class="ms-cat-pill">
                                            <a href="{{ $post->category()->url() }}">{{ $post->category()->name() }}</a>
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div style="padding:22px;">
                                <div style="display:flex;align-items:center;gap:12px;font-size:11px;color:var(--ms-muted);margin-bottom:10px;letter-spacing:.04em;">
                                    <span><i class="mdi mdi-calendar-outline" style="margin-right:4px;"></i>{{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="mdi mdi-account-outline" style="margin-right:4px;"></i>{{ $post->author() }}</span>
                                    @endif
                                    @if($post->has_comments())
                                        <span>
                                            <a href="{{ $post->comment_url() }}" style="color:inherit;text-decoration:none;">
                                                <i class="mdi mdi-comment-outline" style="margin-right:4px;"></i>{{ $post->comment_count() }}
                                            </a>
                                        </span>
                                    @endif
                                </div>
                                <h3 style="font-size:16px;font-weight:500;color:var(--ms-dark);margin-bottom:10px;line-height:1.4;">
                                    <a href="{{ $post->url() }}" style="color:inherit;text-decoration:none;"
                                       onmouseover="this.style.color='var(--ms-linen-d)'"
                                       onmouseout="this.style.color='var(--ms-dark)'">
                                        {!! $post->title() !!}
                                    </a>
                                </h3>
                                <p style="font-size:13px;color:var(--ms-muted);margin-bottom:16px;line-height:1.7;">{{ $post->excerpt(18) }}</p>
                                <a href="{{ $post->url() }}"
                                   style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--ms-charcoal);text-decoration:none;transition:color .2s;"
                                   onmouseover="this.style.color='var(--ms-linen-d)'"
                                   onmouseout="this.style.color='var(--ms-charcoal)'">
                                    {{ __('Read Article') }}
                                    <i class="mdi mdi-arrow-right" style="font-size:15px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:72px 20px;background:#fff;border-radius:var(--ms-radius);border:1px dashed var(--ms-border);">
                            <i class="mdi mdi-newspaper-variant-outline" style="font-size:48px;color:var(--ms-border);display:block;margin-bottom:14px;"></i>
                            <p style="color:var(--ms-muted);font-size:14px;">{{ __('No articles published yet.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(theme_blogs_paginator()?->hasPages())
                <div style="margin-top:36px;">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            {{-- Search --}}
            <div class="ms-sidebar-widget">
                <div class="ms-widget-title">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div class="ms-search-wrap">
                        <input type="text" name="search" placeholder="{{ __('Search articles…') }}">
                        <button type="submit"><i class="mdi mdi-magnify"></i></button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div class="ms-sidebar-widget">
                <div class="ms-widget-title">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach(theme_blog_categories() as $cat)
                    <li style="border-bottom:1px dashed var(--ms-border);padding:9px 0;">
                        <a href="{{ $cat->url() }}"
                           style="display:flex;align-items:center;justify-content:space-between;font-size:13px;color:var(--ms-dark);text-decoration:none;font-weight:500;transition:color .2s;"
                           onmouseover="this.style.color='var(--ms-linen-d)'"
                           onmouseout="this.style.color='var(--ms-dark)'">
                            <span style="display:flex;align-items:center;gap:8px;">
                                <span style="width:4px;height:4px;background:var(--ms-linen);border-radius:50%;flex-shrink:0;"></span>
                                {{ $cat->name() }}
                            </span>
                            <span style="font-size:11px;color:var(--ms-muted);background:var(--ms-warm);padding:2px 9px;border-radius:2px;">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div class="ms-sidebar-widget">
                <div class="ms-widget-title">{{ __('Topics') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}" class="ms-tag-pill">
                            {{ $tag->name() }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
