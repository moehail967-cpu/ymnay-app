@extends('tenant.frontend.frontend-page-master')

@php
    $blog_list  = $blogs ?? $all_blogs ?? collect();
    $query_term = $tag_name ?? $search_term ?? '';
@endphp

@section('title') {{ __('Search') }}: {{ $query_term }} @endsection

@section('content')

<div class="kv-page-banner">
    <div class="container kv-page-banner-content">
        <h1>{{ __('Search') }}: <em style="color:var(--kv-yellow);">{{ $query_term }}</em></h1>
        <div class="kv-breadcrumb" style="margin-top:8px;">
            <a href="{{ theme_home_url() }}">{{ __('Home') }}</a>
            <span class="sep">/</span>
            <a href="{{ theme_blog_url() }}">{{ __('Blog') }}</a>
            <span class="sep">/</span>
            <span class="current">{{ __('Search') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:80px;">
    <div class="row g-4">

        {{-- Results --}}
        <div class="col-lg-8">

            <div style="font-size:13px;color:var(--kv-muted);margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid var(--kv-border);font-weight:600;">
                {{ $blog_list->total() }} {{ __('results found') }}
            </div>

            @forelse($blog_list as $blog)
            @php $img = get_attachment_image_by_id($blog->image, 'grid'); @endphp
            <div style="display:flex;gap:16px;background:#fff;border:2px solid var(--kv-border);border-radius:var(--kv-radius);overflow:hidden;box-shadow:var(--kv-shadow);margin-bottom:16px;transition:all .25s;"
                 onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(244,67,54,.2)';this.style.borderColor='var(--kv-red)'"
                 onmouseout="this.style.boxShadow='var(--kv-shadow)';this.style.borderColor='var(--kv-border)'">

                @if(!empty($img['img_url']))
                <a href="{{ tenant_blog_single_route($blog->slug) }}"
                   style="flex-shrink:0;width:140px;overflow:hidden;background:var(--kv-light);">
                    <img src="{{ $img['img_url'] }}" alt="{{ $blog->title }}"
                         style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
                         onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </a>
                @endif

                <div style="padding:18px;flex:1;min-width:0;">
                    <div style="font-size:11px;color:var(--kv-muted);margin-bottom:6px;font-weight:600;">
                        <i class="las la-calendar-alt"></i> {{ $blog->created_at?->format('d M Y') }}
                    </div>
                    <h3 style="font-size:16px;font-weight:800;color:var(--kv-dark);margin-bottom:8px;line-height:1.4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <a href="{{ tenant_blog_single_route($blog->slug) }}"
                           style="color:inherit;text-decoration:none;"
                           onmouseover="this.style.color='var(--kv-red)'" onmouseout="this.style.color='var(--kv-dark)'">
                            {!! \Illuminate\Support\Str::words($blog->title, 10) !!}
                        </a>
                    </h3>
                    <p style="font-size:13px;color:var(--kv-muted);line-height:1.6;margin-bottom:12px;
                        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {!! \Illuminate\Support\Str::words(strip_tags($blog->blog_content), 20) !!}
                    </p>
                    <a href="{{ tenant_blog_single_route($blog->slug) }}" class="kv-btn kv-btn-outline kv-btn-sm">
                        {{ __('Read More') }} <i class="las la-arrow-right"></i>
                    </a>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:60px 20px;background:var(--kv-light);border-radius:var(--kv-radius);border:2px dashed var(--kv-border);">
                <i class="las la-search" style="font-size:52px;color:var(--kv-muted);display:block;margin-bottom:12px;"></i>
                <p style="color:var(--kv-muted);margin-bottom:20px;font-weight:600;">{{ __('No blog posts found for') }} "<strong>{{ $query_term }}</strong>"</p>
                <a href="{{ theme_blog_url() }}" class="kv-btn kv-btn-red">
                    <i class="las la-arrow-left"></i> {{ __('Back to Blog') }}
                </a>
            </div>
            @endforelse

            @if($blog_list->hasPages())
            <div style="margin-top:24px;">
                {{ $blog_list->links() }}
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">

            <div class="kv-sidebar-card" style="margin-bottom:24px;">
                <div class="kv-sidebar-title">{{ __('Search Again') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:2px solid var(--kv-border);border-radius:var(--kv-radius-sm);overflow:hidden;">
                        <input type="text" name="search" value="{{ $query_term }}" placeholder="{{ __('Search articles…') }}"
                               style="flex:1;padding:10px 14px;border:0;font-size:13px;outline:none;color:var(--kv-dark);background:#fff;">
                        <button type="submit" style="background:var(--kv-red);border:0;color:#fff;padding:0 16px;cursor:pointer;font-size:18px;transition:background .2s;"
                                onmouseover="this.style.background='var(--kv-dark)'" onmouseout="this.style.background='var(--kv-red)'">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div class="kv-sidebar-card" style="margin-bottom:24px;">
                <div class="kv-sidebar-title">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach(theme_blog_categories() as $cat)
                    <li style="border-bottom:1px dashed var(--kv-border);padding:8px 0;">
                        <a href="{{ $cat->url() }}"
                           style="display:flex;align-items:center;justify-content:space-between;font-size:13px;font-weight:600;color:var(--kv-dark);text-decoration:none;transition:color .2s;"
                           onmouseover="this.style.color='var(--kv-red)'" onmouseout="this.style.color='var(--kv-dark)'">
                            <span>{{ $cat->name() }}</span>
                            <span style="background:var(--kv-light);color:var(--kv-red);font-size:10px;font-weight:800;padding:2px 10px;border-radius:50px;">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

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
