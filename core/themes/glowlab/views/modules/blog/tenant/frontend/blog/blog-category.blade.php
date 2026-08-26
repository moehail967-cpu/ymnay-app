@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name ?? __('Blog Category') }} @endsection
@section('page-title') {{ $category_name ?? __('Blog Category') }} @endsection

@section('content')
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">{{ $category_name ?? __('Blog Category') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <a href="{{ theme_blog_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Blog') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
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
                        <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);transition:box-shadow .2s,transform .2s;"
                             onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(184,150,90,.2)';this.style.transform='translateY(-3px)'"
                             onmouseout="this.style.boxShadow='var(--gl-shadow)';this.style.transform='translateY(0)'">
                            <div style="aspect-ratio:16/9;overflow:hidden;background:var(--gl-gold-pale);position:relative;">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}">
                                        <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" loading="lazy"
                                             style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
                                             onmouseover="this.style.transform='scale(1.04)'" onmouseout="this.style.transform='scale(1)'">
                                    </a>
                                @else
                                    <div style="display:flex;align-items:center;justify-content:center;height:100%;font-size:48px;">✨</div>
                                @endif
                                @if($post->category())
                                    <span style="position:absolute;top:12px;left:12px;background:var(--gl-gold);color:#fff;font-size:10px;font-weight:700;letter-spacing:.5px;padding:4px 12px;border-radius:50px;text-transform:uppercase;">
                                        {{ $post->category()->name() }}
                                    </span>
                                @endif
                            </div>
                            <div style="padding:20px;">
                                <div style="display:flex;align-items:center;gap:12px;font-size:11px;color:var(--gl-muted);margin-bottom:10px;">
                                    <span><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                    @if($post->author())
                                        <span><i class="mdi mdi-account-outline"></i> {{ $post->author() }}</span>
                                    @endif
                                </div>
                                <h3 style="font-size:16px;font-weight:600;color:var(--gl-dark);margin-bottom:8px;line-height:1.4;">
                                    <a href="{{ $post->url() }}" style="color:inherit;text-decoration:none;">{!! $post->title() !!}</a>
                                </h3>
                                <p style="font-size:13px;color:var(--gl-muted);margin-bottom:16px;line-height:1.6;">{{ $post->excerpt(18) }}</p>
                                <a href="{{ $post->url() }}"
                                   style="display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gl-dark);text-decoration:none;border-bottom:2px solid var(--gl-gold);padding-bottom:2px;transition:color .2s;"
                                   onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='var(--gl-dark)'">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:60px 20px;background:#fff;border-radius:var(--gl-radius);border:1px dashed var(--gl-border);">
                            <span style="font-size:48px;display:block;margin-bottom:16px;">✨</span>
                            <p style="color:var(--gl-muted);">{{ __('No posts in this category yet.') }}</p>
                            <a href="{{ theme_blog_url() }}"
                               style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;margin-top:16px;"
                               onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
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
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;">
                        <input type="text" name="search" placeholder="{{ __('Search articles…') }}"
                               style="flex:1;padding:9px 14px;border:0;font-size:13px;font-family:inherit;outline:none;">
                        <button type="submit" style="background:var(--gl-dark);border:0;color:#fff;padding:0 16px;cursor:pointer;font-size:16px;transition:background .2s;"
                                onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                            <i class="mdi mdi-magnify"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach(theme_blog_categories() as $cat)
                    @php $is_active = ($category_name ?? '') === $cat->name(); @endphp
                    <li style="border-bottom:1px dashed var(--gl-border);padding:8px 0;">
                        <a href="{{ $cat->url() }}"
                           style="display:flex;align-items:center;justify-content:space-between;font-size:13px;color:{{ $is_active ? 'var(--gl-gold)' : 'var(--gl-dark)' }};text-decoration:none;font-weight:{{ $is_active ? '700' : '400' }};"
                           onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='{{ $is_active ? 'var(--gl-gold)' : 'var(--gl-dark)' }}'">
                            <span>{{ $cat->name() }}</span>
                            <span style="background:{{ $is_active ? 'var(--gl-gold)' : 'var(--gl-gold-pale)' }};color:{{ $is_active ? '#fff' : 'var(--gl-gold)' }};font-size:10px;font-weight:700;padding:2px 10px;border-radius:50px;">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Popular Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                    <a href="{{ $tag->url() }}"
                       style="display:inline-block;padding:5px 14px;background:var(--gl-gold-pale);border:1.5px solid var(--gl-border);border-radius:50px;font-size:12px;font-weight:600;color:var(--gl-muted);text-decoration:none;transition:all .2s;"
                       onmouseover="this.style.background='var(--gl-gold)';this.style.color='#fff';this.style.borderColor='var(--gl-gold)'"
                       onmouseout="this.style.background='var(--gl-gold-pale)';this.style.color='var(--gl-muted)';this.style.borderColor='var(--gl-border)'">
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
