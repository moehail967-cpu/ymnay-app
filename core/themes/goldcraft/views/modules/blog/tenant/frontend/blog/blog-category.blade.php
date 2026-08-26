@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name ?? __('Blog Category') }} @endsection

@section('content')
<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">{{ $category_name ?? __('Blog Category') }}</h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <a href="{{ theme_blog_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Blog') }}</a>
            <span>—</span>
            <span>{{ $category_name ?? __('Category') }}</span>
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
                        <div style="background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);transition:all .25s;"
                             onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(200,169,110,.3)';this.style.transform='translateY(-3px)'"
                             onmouseout="this.style.boxShadow='var(--gc-shadow)';this.style.transform='translateY(0)'">
                            <div style="aspect-ratio:16/9;overflow:hidden;background:var(--gc-warm);position:relative;">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;transition:transform .5s;"
                                             onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:48px;"><i class="las la-gem"></i></div>
                                @endif
                                @if($post->category())
                                    <span style="position:absolute;top:12px;left:12px;background:var(--gc-rose);color:#fff;font-size:9px;font-weight:700;letter-spacing:1px;padding:4px 12px;border-radius:3px;text-transform:uppercase;">
                                        {{ $post->category()->name() }}
                                    </span>
                                @endif
                            </div>
                            <div style="padding:20px;">
                                <div style="display:flex;align-items:center;gap:12px;font-size:11px;color:var(--gc-muted);margin-bottom:10px;font-style:italic;">
                                    <span><i class="las la-calendar-alt"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="las la-user"></i> {{ $post->author() }}</span>
                                    @endif
                                </div>
                                <h3 style="font-size:17px;font-weight:400;color:var(--gc-dark);margin-bottom:8px;line-height:1.4;font-family:Georgia,serif;font-style:italic;">
                                    <a href="{{ $post->url() }}" style="color:inherit;text-decoration:none;">{!! $post->title() !!}</a>
                                </h3>
                                <p style="font-size:13px;color:var(--gc-muted);margin-bottom:16px;line-height:1.6;font-style:italic;">{{ $post->excerpt(18) }}</p>
                                <a href="{{ $post->url() }}"
                                   style="display:inline-flex;align-items:center;gap:6px;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-rose);text-decoration:none;border-bottom:1px solid var(--gc-rose);padding-bottom:2px;transition:opacity .2s;"
                                   onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                                    {{ __('Read More') }} <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:60px 20px;background:var(--gc-warm);border-radius:var(--gc-radius);border:1px dashed var(--gc-border);">
                            <div style="font-size:48px;margin-bottom:16px;"><i class="las la-book-open"></i></div>
                            <p style="color:var(--gc-muted);font-style:italic;">{{ __('No posts in this category yet.') }}</p>
                            <a href="{{ theme_blog_url() }}" class="gc-btn gc-btn-primary" style="margin-top:16px;">
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
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;">
                        <input type="text" name="search" placeholder="{{ __('Search articles…') }}"
                               style="flex:1;padding:9px 14px;border:0;font-size:13px;font-family:Georgia,serif;outline:none;font-style:italic;color:var(--gc-dark);">
                        <button type="submit" style="background:var(--gc-rose);border:0;color:#fff;padding:0 16px;cursor:pointer;font-size:16px;transition:background .2s;"
                                onmouseover="this.style.background='var(--gc-dark)'" onmouseout="this.style.background='var(--gc-rose)'">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach(theme_blog_categories() as $cat)
                    @php $is_active = ($category_name ?? '') === $cat->name(); @endphp
                    <li style="border-bottom:1px dashed var(--gc-border);padding:8px 0;">
                        <a href="{{ $cat->url() }}"
                           style="display:flex;align-items:center;justify-content:space-between;font-size:13px;color:{{ $is_active ? 'var(--gc-rose)' : 'var(--gc-dark)' }};text-decoration:none;font-style:italic;"
                           onmouseover="this.style.color='var(--gc-rose)'" onmouseout="this.style.color='{{ $is_active ? 'var(--gc-rose)' : 'var(--gc-dark)' }}'">
                            <span>{{ $cat->name() }}</span>
                            <span style="background:{{ $is_active ? 'var(--gc-rose)' : 'var(--gc-warm)' }};color:{{ $is_active ? '#fff' : 'var(--gc-rose)' }};font-size:10px;padding:2px 10px;border-radius:50px;">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;">{{ __('Popular Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}"
                       style="display:inline-block;padding:5px 14px;background:var(--gc-warm);border:1.5px solid var(--gc-border);border-radius:50px;font-size:12px;color:var(--gc-muted);text-decoration:none;transition:all .2s;font-style:italic;"
                       onmouseover="this.style.background='var(--gc-rose)';this.style.color='#fff';this.style.borderColor='var(--gc-rose)'"
                       onmouseout="this.style.background='var(--gc-warm)';this.style.color='var(--gc-muted)';this.style.borderColor='var(--gc-border)'">
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
