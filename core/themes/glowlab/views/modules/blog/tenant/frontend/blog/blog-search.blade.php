@extends('tenant.frontend.frontend-page-master')

@php
    $blog_list  = $blogs ?? $all_blogs ?? collect();
    $query_term = $tag_name ?? $search_term ?? '';
@endphp

@section('title') {{ __('Search') }}: {{ $query_term }} @endsection
@section('page-title') {{ __('Search') }}: {{ $query_term }} @endsection

@section('content')

{{-- Page banner --}}
<div style="background:var(--gl-gold-pale);border-bottom:1px solid var(--gl-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:26px;font-weight:300;color:var(--gl-dark);margin-bottom:8px;letter-spacing:-.3px;">
            {{ __('Search results for') }}: <em style="font-style:italic;color:var(--gl-gold);">{{ $query_term }}</em>
        </h2>
        <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--gl-muted);">
            <a href="{{ theme_home_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Home') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <a href="{{ theme_blog_url() }}" style="color:var(--gl-gold);font-weight:600;text-decoration:none;">{{ __('Blog') }}</a>
            <i class="mdi mdi-chevron-right" style="opacity:.5;"></i>
            <span>{{ __('Search') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:80px;">
    <div class="row g-4">

        {{-- Results --}}
        <div class="col-lg-8">

            {{-- Result count --}}
            <div style="font-size:13px;color:var(--gl-muted);margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--gl-border);">
                {{ $blog_list->total() }} {{ __('results found') }}
            </div>

            @forelse($blog_list as $blog)
            @php $img = get_attachment_image_by_id($blog->image, 'grid'); @endphp
            <div style="display:flex;gap:16px;background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;box-shadow:var(--gl-shadow);margin-bottom:16px;transition:box-shadow .2s,transform .2s;"
                 onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(184,150,90,.2)';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.boxShadow='var(--gl-shadow)';this.style.transform='translateY(0)'">

                @if(!empty($img['img_url']))
                <a href="{{ tenant_blog_single_route($blog->slug) }}"
                   style="flex-shrink:0;width:140px;overflow:hidden;background:var(--gl-gold-pale);">
                    <img src="{{ $img['img_url'] }}" alt="{{ $blog->title }}"
                         style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
                         onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </a>
                @endif

                <div style="padding:18px;flex:1;min-width:0;">
                    <div style="font-size:11px;color:var(--gl-muted);margin-bottom:6px;">
                        <i class="mdi mdi-calendar-outline"></i> {{ $blog->created_at?->format('d M Y') }}
                    </div>
                    <h3 style="font-size:16px;font-weight:600;color:var(--gl-dark);margin-bottom:8px;line-height:1.4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        <a href="{{ tenant_blog_single_route($blog->slug) }}"
                           style="color:inherit;text-decoration:none;"
                           onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='var(--gl-dark)'">
                            {!! \Illuminate\Support\Str::words($blog->title, 10) !!}
                        </a>
                    </h3>
                    <p style="font-size:13px;color:var(--gl-muted);line-height:1.6;margin-bottom:12px;
                        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {!! \Illuminate\Support\Str::words(strip_tags($blog->blog_content), 20) !!}
                    </p>
                    <a href="{{ tenant_blog_single_route($blog->slug) }}"
                       style="display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:var(--gl-dark);text-decoration:none;border-bottom:2px solid var(--gl-gold);padding-bottom:2px;transition:color .2s;"
                       onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='var(--gl-dark)'">
                        {{ __('Read More') }} <i class="mdi mdi-arrow-right"></i>
                    </a>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:60px 20px;background:#fff;border-radius:var(--gl-radius);border:1px dashed var(--gl-border);">
                <i class="mdi mdi-magnify-close" style="font-size:48px;color:var(--gl-border);display:block;margin-bottom:12px;"></i>
                <p style="color:var(--gl-muted);margin-bottom:20px;">{{ __('No blog posts found for') }} "<strong>{{ $query_term }}</strong>"</p>
                <a href="{{ theme_blog_url() }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;background:var(--gl-dark);color:#fff;border-radius:var(--gl-radius);font-size:12px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;text-decoration:none;transition:background .2s;"
                   onmouseover="this.style.background='var(--gl-gold)'" onmouseout="this.style.background='var(--gl-dark)'">
                    <i class="mdi mdi-arrow-left"></i> {{ __('Back to Blog') }}
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

            {{-- Search again --}}
            <div style="background:#fff;border:1px solid var(--gl-border);border-radius:var(--gl-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gl-shadow);">
                <div style="font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gl-muted);margin-bottom:16px;">{{ __('Search Again') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1.5px solid var(--gl-border);border-radius:var(--gl-radius);overflow:hidden;">
                        <input type="text" name="search" value="{{ $query_term }}" placeholder="{{ __('Search articles…') }}"
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
                    <li style="border-bottom:1px dashed var(--gl-border);padding:8px 0;">
                        <a href="{{ $cat->url() }}"
                           style="display:flex;align-items:center;justify-content:space-between;font-size:13px;color:var(--gl-dark);text-decoration:none;"
                           onmouseover="this.style.color='var(--gl-gold)'" onmouseout="this.style.color='var(--gl-dark)'">
                            <span>{{ $cat->name() }}</span>
                            <span style="background:var(--gl-gold-pale);color:var(--gl-gold);font-size:10px;font-weight:700;padding:2px 10px;border-radius:50px;">{{ $cat->count() }}</span>
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
                       style="display:inline-block;padding:5px 14px;background:var(--gl-gold-pale);border:1.5px solid var(--gl-border);border-radius:50px;font-size:11px;font-weight:600;color:var(--gl-muted);text-decoration:none;transition:all .2s;"
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
