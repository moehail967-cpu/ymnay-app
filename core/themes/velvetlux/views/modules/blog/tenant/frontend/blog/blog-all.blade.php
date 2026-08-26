@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Journal') }} @endsection
@section('page-title') {{ __('Journal') }} @endsection

@section('content')
<div style="background:var(--vl-surface);border-bottom:1px solid var(--vl-border);padding:40px 0 28px;">
    <div class="container">
        <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:8px;">{{ __('Editorial') }}</div>
        <h2 style="font-size:36px;font-weight:400;color:var(--vl-ivory);margin-bottom:12px;font-family:'Cormorant Garamond',serif;letter-spacing:2px;">{{ __('The VelvetLux Journal') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:11px;color:var(--vl-muted);letter-spacing:1px;text-transform:uppercase;">
            <a href="{{ theme_home_url() }}" style="color:var(--vl-champagne);">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="font-size:14px;"></i>
            <span>{{ __('Journal') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:56px;padding-bottom:80px;">
    <div class="row g-5">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div style="background:var(--vl-card);border:1px solid var(--vl-border);overflow:hidden;transition:border-color .3s;"
                             onmouseover="this.style.borderColor='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)'">
                            <div style="aspect-ratio:4/3;overflow:hidden;background:var(--vl-surface);position:relative;">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;transition:transform .5s;"
                                             onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:48px;color:var(--vl-champagne);">✦</div>
                                @endif
                                @if($post->category())
                                    <span style="position:absolute;top:14px;left:14px;background:var(--vl-plum);color:var(--vl-champagne-l);font-size:9px;font-weight:400;letter-spacing:3px;text-transform:uppercase;padding:5px 12px;">
                                        {{ $post->category()->name() }}
                                    </span>
                                @endif
                            </div>
                            <div style="padding:24px;">
                                <div style="display:flex;align-items:center;gap:16px;font-size:11px;color:var(--vl-muted);letter-spacing:.5px;margin-bottom:12px;">
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                    @if($post->author())<span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span>@endif
                                </div>
                                <h3 style="font-family:'Cormorant Garamond',serif;font-size:20px;font-weight:400;color:var(--vl-ivory);margin-bottom:10px;line-height:1.4;letter-spacing:.5px;">
                                    <a href="{{ $post->url() }}" style="color:inherit;text-decoration:none;">{!! $post->title() !!}</a>
                                </h3>
                                <p style="font-size:13px;color:var(--vl-muted);margin-bottom:16px;line-height:1.7;">{{ $post->excerpt(16) }}</p>
                                <a href="{{ $post->url() }}" style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);text-decoration:none;">
                                    {{ __('Read More') }} →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:64px;border:1px dashed var(--vl-border);">
                            <div style="font-size:10px;letter-spacing:4px;text-transform:uppercase;color:var(--vl-muted);">{{ __('No Articles Available') }}</div>
                        </div>
                    </div>
                @endforelse
            </div>

            @if(theme_blogs_paginator()?->hasPages())
                <div style="margin-top:40px;">{{ theme_blogs_paginator()->links() }}</div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;margin-bottom:28px;">
                <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1px solid var(--vl-border);">
                        <input type="text" name="search" placeholder="{{ __('Search journal…') }}"
                               style="flex:1;padding:10px 14px;border:0;background:var(--vl-surface);color:var(--vl-ivory);font-size:13px;font-family:inherit;outline:none;">
                        <button type="submit" style="background:var(--vl-champagne);border:0;color:var(--vl-dark);padding:0 16px;cursor:pointer;font-size:16px;">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;margin-bottom:28px;">
                <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach(theme_blog_categories() as $cat)
                    <li style="border-bottom:1px solid rgba(58,36,68,.5);padding:10px 0;">
                        <a href="{{ $cat->url() }}" style="display:flex;align-items:center;justify-content:space-between;font-size:13px;color:var(--vl-muted);text-decoration:none;letter-spacing:.5px;transition:color .2s;"
                           onmouseover="this.style.color='var(--vl-champagne)'" onmouseout="this.style.color='var(--vl-muted)'">
                            <span>{{ $cat->name() }}</span>
                            <span style="font-size:11px;color:var(--vl-plum);background:var(--vl-surface);padding:2px 10px;">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:var(--vl-card);border:1px solid var(--vl-border);padding:28px;">
                <div style="font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--vl-champagne);margin-bottom:16px;">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}" style="display:inline-block;padding:5px 14px;background:var(--vl-surface);border:1px solid var(--vl-border);font-size:11px;letter-spacing:2px;text-transform:uppercase;color:var(--vl-muted);text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.borderColor='var(--vl-champagne)';this.style.color='var(--vl-champagne)'" onmouseout="this.style.borderColor='var(--vl-border)';this.style.color='var(--vl-muted)'">
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
