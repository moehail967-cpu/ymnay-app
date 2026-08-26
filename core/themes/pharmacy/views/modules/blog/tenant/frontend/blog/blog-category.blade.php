@extends('tenant.frontend.frontend-page-master')

@section('title') {{ $category_name ?? __('Blog Category') }} @endsection
@section('page-title') {{ $category_name ?? __('Blog Category') }} @endsection

@section('content')
<div style="background:var(--pf-teal-light);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;">{{ $category_name ?? __('Blog Category') }}</h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--pf-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <a href="{{ theme_blog_url() }}" style="color:var(--pf-teal);font-weight:600;">{{ __('Blog') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ $category_name ?? __('Category') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:36px;padding-bottom:72px;">
    <div class="row g-4">

        {{-- Blog Grid --}}
        <div class="col-lg-8">
            <div class="row g-4">
                @forelse(theme_blogs() as $post)
                    <div class="col-md-6">
                        <div style="background:var(--pf-white);border:1.5px solid var(--pf-border);border-radius:var(--pf-radius-xl);overflow:hidden;box-shadow:var(--pf-shadow);transition:box-shadow .2s;">
                            <div style="height:200px;overflow:hidden;background:var(--pf-teal-light);display:flex;align-items:center;justify-content:center;position:relative;">
                                @if($post->has_image())
                                    <a href="{{ $post->url() }}"><img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;" loading="lazy"></a>
                                @else
                                    <i class="las la-newspaper" style="font-size:48px;color:var(--pf-teal);"></i>
                                @endif
                                @if($post->category())
                                    <a href="{{ $post->category()->url() }}" style="position:absolute;top:12px;left:12px;background:var(--pf-teal);color:#fff;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;text-decoration:none;">
                                        {{ $post->category()->name() }}
                                    </a>
                                @endif
                            </div>
                            <div style="padding:20px;">
                                <div style="display:flex;gap:16px;font-size:12px;color:var(--pf-muted);margin-bottom:10px;">
                                    <span><i class="las la-calendar"></i> {{ $post->date() }}</span>
                                    @if($post->author())<span><i class="las la-user"></i> {{ $post->author() }}</span>@endif
                                </div>
                                <div style="font-size:15px;font-weight:700;color:var(--pf-dark);margin-bottom:8px;line-height:1.4;">
                                    <a href="{{ $post->url() }}" style="color:inherit;text-decoration:none;">{!! $post->title() !!}</a>
                                </div>
                                <p style="font-size:13px;color:var(--pf-muted);line-height:1.7;margin-bottom:14px;">{{ $post->excerpt(20) }}</p>
                                <a href="{{ $post->url() }}" style="font-size:13px;font-weight:700;color:var(--pf-teal);text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                    {{ __('Read More') }} <i class="las la-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div style="text-align:center;padding:60px;border:2px dashed var(--pf-border);border-radius:var(--pf-radius);">
                            <i class="las la-newspaper" style="font-size:48px;color:var(--pf-border);display:block;margin-bottom:12px;"></i>
                            <p style="color:var(--pf-muted);font-size:14px;">{{ __('No posts found in this category.') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
            @if(theme_blogs_paginator()?->hasPages())
                <div style="margin-top:24px;display:flex;gap:6px;flex-wrap:wrap;">
                    {{ theme_blogs_paginator()->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Search --}}
            <div style="background:var(--pf-white);border:1.5px solid var(--pf-border);border-radius:var(--pf-radius-xl);padding:20px;margin-bottom:20px;box-shadow:var(--pf-shadow);">
                <div style="font-size:14px;font-weight:700;color:var(--pf-dark);margin-bottom:12px;padding-bottom:10px;border-bottom:1.5px solid var(--pf-border);">{{ __('Search') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="POST" style="display:flex;gap:8px;">
                    {!! theme_csrf_field() !!}
                    <input type="text" name="search" placeholder="{{ __('Search blogs…') }}"
                           style="flex:1;padding:9px 12px;border:1.5px solid var(--pf-border);border-radius:var(--pf-radius);font-size:13px;outline:none;font-family:var(--pf-font);"
                           onfocus="this.style.borderColor='var(--pf-teal)'" onblur="this.style.borderColor='var(--pf-border)'">
                    <button type="submit" class="pf-btn pf-btn-teal pf-btn-sm"><i class="las la-search"></i></button>
                </form>
            </div>

            {{-- Categories --}}
            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:var(--pf-white);border:1.5px solid var(--pf-border);border-radius:var(--pf-radius-xl);padding:20px;margin-bottom:20px;box-shadow:var(--pf-shadow);">
                <div style="font-size:14px;font-weight:700;color:var(--pf-dark);margin-bottom:12px;padding-bottom:10px;border-bottom:1.5px solid var(--pf-border);">{{ __('Categories') }}</div>
                <ul class="list-unstyled mb-0">
                    @foreach(theme_blog_categories() as $cat)
                    <li style="margin-bottom:6px;">
                        <a href="{{ $cat->url() }}"
                           style="display:flex;justify-content:space-between;align-items:center;font-size:13px;text-decoration:none;padding:6px 8px;border-radius:var(--pf-radius);transition:background .2s;color:{{ $cat->name() === ($category_name ?? '') ? 'var(--pf-teal)' : 'var(--pf-dark)' }};background:{{ $cat->name() === ($category_name ?? '') ? 'var(--pf-teal-light)' : 'transparent' }};font-weight:{{ $cat->name() === ($category_name ?? '') ? '700' : '500' }};">
                            {{ $cat->name() }}
                            <span style="font-size:11px;background:var(--pf-bg);color:var(--pf-muted);padding:1px 7px;border-radius:20px;">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @if(theme_blog_tags()->isNotEmpty())
            <div style="background:var(--pf-white);border:1.5px solid var(--pf-border);border-radius:var(--pf-radius-xl);padding:20px;box-shadow:var(--pf-shadow);">
                <div style="font-size:14px;font-weight:700;color:var(--pf-dark);margin-bottom:12px;padding-bottom:10px;border-bottom:1.5px solid var(--pf-border);">{{ __('Tags') }}</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach(theme_blog_tags() as $tag)
                        <a href="{{ $tag->url() }}"
                           style="font-size:12px;padding:4px 12px;border-radius:20px;border:1.5px solid var(--pf-border);color:var(--pf-dark);text-decoration:none;transition:all .2s;"
                           onmouseover="this.style.background='var(--pf-teal)';this.style.color='#fff';this.style.borderColor='var(--pf-teal)'"
                           onmouseout="this.style.background='';this.style.color='var(--pf-dark)';this.style.borderColor='var(--pf-border)'">
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
