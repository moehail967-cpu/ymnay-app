@extends('tenant.frontend.frontend-page-master')

@section('title') {{ __('Blog') }} @endsection
@section('page-title') {{ __('Blog') }} @endsection

@section('content')
<div style="background:var(--tr-bark);padding:24px 0;border-bottom:2px solid var(--tr-olive);">
    <div class="container">
        <h1 style="font-size:26px;font-weight:900;color:#fff;margin-bottom:6px;">{{ __('Trail Journal') }}</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:rgba(255,255,255,.6);">
            <a href="{{ theme_home_url() }}" style="color:var(--tr-sand);font-weight:600;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right"></i>
            <span>{{ __('Blog') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding:60px 0 80px;">
    <div class="row g-5">

        {{-- Main Content --}}
        <div class="col-lg-8">
            @php $posts = theme_blogs(); @endphp
            @if($posts && $posts->count())
                <div class="row g-4">
                    @foreach($posts as $post)
                    <div class="col-md-6">
                        <article style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;box-shadow:var(--tr-shadow);height:100%;display:flex;flex-direction:column;transition:box-shadow .2s;">
                            @if($post->has_image())
                            <a href="{{ $post->url() }}" style="display:block;overflow:hidden;aspect-ratio:16/9;">
                                <img src="{{ $post->image_url() }}" alt="{{ $post->title() }}" style="width:100%;height:100%;object-fit:cover;transition:transform .4s;">
                            </a>
                            @endif
                            <div style="padding:24px;flex:1;display:flex;flex-direction:column;">
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:12px;">
                                    @if($post->category())
                                    <span style="background:var(--tr-olive);color:#fff;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;padding:3px 10px;border-radius:4px;">{{ $post->category()->name() }}</span>
                                    @endif
                                    <span style="font-size:12px;color:var(--tr-stone);"><i class="mdi mdi-calendar-outline"></i> {{ $post->date() }}</span>
                                </div>
                                <h2 style="font-size:17px;font-weight:800;color:var(--tr-bark);margin-bottom:10px;line-height:1.35;">
                                    <a href="{{ $post->url() }}" style="color:inherit;text-decoration:none;">{{ $post->title() }}</a>
                                </h2>
                                <p style="font-size:13px;color:var(--tr-stone);line-height:1.65;flex:1;margin-bottom:16px;">{{ $post->excerpt(18) }}</p>
                                <a href="{{ $post->url() }}" class="tr-btn tr-btn-outline" style="width:fit-content;font-size:12px;padding:8px 18px;">
                                    {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @php $paginator = theme_blogs_paginator(); @endphp
                @if($paginator && $paginator->hasPages())
                <div style="display:flex;align-items:center;justify-content:center;gap:6px;margin-top:48px;flex-wrap:wrap;">
                    @if($paginator->onFirstPage())
                        <span style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:var(--tr-radius);border:1.5px solid var(--tr-border);color:var(--tr-stone);font-size:16px;opacity:.4;"><i class="mdi mdi-chevron-left"></i></span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:var(--tr-radius);border:1.5px solid var(--tr-border);color:var(--tr-bark);font-size:16px;text-decoration:none;transition:border-color .2s;" onmouseover="this.style.borderColor='var(--tr-olive)'" onmouseout="this.style.borderColor='var(--tr-border)'"><i class="mdi mdi-chevron-left"></i></a>
                    @endif
                    @foreach($paginator->getUrlRange(1,$paginator->lastPage()) as $page => $url)
                        @if($page == $paginator->currentPage())
                            <span style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:var(--tr-radius);background:var(--tr-olive);color:#fff;font-size:13px;font-weight:700;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:var(--tr-radius);border:1.5px solid var(--tr-border);color:var(--tr-bark);font-size:13px;font-weight:600;text-decoration:none;transition:border-color .2s;" onmouseover="this.style.borderColor='var(--tr-olive)'" onmouseout="this.style.borderColor='var(--tr-border)'">{{ $page }}</a>
                        @endif
                    @endforeach
                    @if($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:var(--tr-radius);border:1.5px solid var(--tr-border);color:var(--tr-bark);font-size:16px;text-decoration:none;transition:border-color .2s;" onmouseover="this.style.borderColor='var(--tr-olive)'" onmouseout="this.style.borderColor='var(--tr-border)'"><i class="mdi mdi-chevron-right"></i></a>
                    @else
                        <span style="width:36px;height:36px;display:flex;align-items:center;justify-content:center;border-radius:var(--tr-radius);border:1.5px solid var(--tr-border);color:var(--tr-stone);font-size:16px;opacity:.4;"><i class="mdi mdi-chevron-right"></i></span>
                    @endif
                </div>
                @endif
            @else
                <div style="text-align:center;padding:80px 0;color:var(--tr-stone);">
                    <i class="mdi mdi-post-outline" style="font-size:64px;display:block;margin-bottom:16px;opacity:.3;"></i>
                    <p style="font-size:16px;">{{ __('No posts found.') }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Search --}}
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:24px;margin-bottom:28px;">
                <h3 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--tr-bark);margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--tr-olive);">{{ __('Search') }}</h3>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1.5px solid var(--tr-border);border-radius:var(--tr-radius);overflow:hidden;">
                        <input type="text" name="s" placeholder="{{ __('Search posts…') }}" value="{{ request('s') }}"
                               style="flex:1;padding:9px 14px;border:0;outline:none;font-size:13px;background:#fff;">
                        <button type="submit" style="background:var(--tr-olive);color:#fff;border:0;padding:0 16px;font-size:16px;cursor:pointer;"><i class="mdi mdi-magnify"></i></button>
                    </div>
                </form>
            </div>

            {{-- Categories --}}
            @php $cats = theme_blog_categories(); @endphp
            @if($cats && $cats->count())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:24px;margin-bottom:28px;">
                <h3 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--tr-bark);margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--tr-olive);">{{ __('Categories') }}</h3>
                <ul style="list-style:none;padding:0;margin:0;">
                    @foreach($cats as $cat)
                    <li style="border-bottom:1px solid var(--tr-border);">
                        <a href="{{ $cat->url() }}" style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;font-size:13px;color:var(--tr-bark);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--tr-olive)'" onmouseout="this.style.color='var(--tr-bark)'">
                            <span><i class="mdi mdi-tag-outline" style="color:var(--tr-olive);margin-right:6px;"></i>{{ $cat->name() }}</span>
                            <span style="background:var(--tr-cream);color:var(--tr-stone);font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;">{{ $cat->count() ?: '' }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Tags --}}
            @php $tags = theme_blog_tags(); @endphp
            @if($tags && $tags->count())
            <div style="background:#fff;border:1px solid var(--tr-border);border-radius:var(--tr-radius);padding:24px;">
                <h3 style="font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:1px;color:var(--tr-bark);margin-bottom:16px;padding-bottom:10px;border-bottom:2px solid var(--tr-olive);">{{ __('Tags') }}</h3>
                <div style="display:flex;flex-wrap:wrap;gap:8px;">
                    @foreach($tags as $tag)
                    <a href="{{ $tag->url() }}" style="background:var(--tr-cream);color:var(--tr-stone);font-size:12px;font-weight:600;padding:5px 12px;border-radius:4px;text-decoration:none;border:1px solid var(--tr-border);transition:all .2s;" onmouseover="this.style.background='var(--tr-olive)';this.style.color='#fff';this.style.borderColor='var(--tr-olive)'" onmouseout="this.style.background='var(--tr-cream)';this.style.color='var(--tr-stone)';this.style.borderColor='var(--tr-border)'">
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
