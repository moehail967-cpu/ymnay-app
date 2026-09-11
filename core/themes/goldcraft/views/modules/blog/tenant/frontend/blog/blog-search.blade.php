@extends('tenant.frontend.frontend-page-master')

@php
    $blog_list  = $blogs ?? $all_blogs ?? collect();
    $query_term = $tag_name ?? $search_term ?? '';
@endphp

@section('title') {{ __('Search') }}: {{ $query_term }} @endsection

@section('content')

<div style="background:var(--gc-warm);border-bottom:1px solid var(--gc-border);padding:36px 0 28px;">
    <div class="container">
        <h2 style="font-size:13px;font-weight:400;letter-spacing:4px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:8px;">
            {{ __('Search') }}: <em style="font-style:italic;color:var(--gc-dark);">{{ $query_term }}</em>
        </h2>
        <div style="display:flex;align-items:center;gap:10px;font-size:12px;color:var(--gc-muted);font-style:italic;">
            <a href="{{ theme_home_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Home') }}</a>
            <span>—</span>
            <a href="{{ theme_blog_url() }}" style="color:var(--gc-rose);text-decoration:none;">{{ __('Blog') }}</a>
            <span>—</span>
            <span>{{ __('Search') }}</span>
        </div>
    </div>
</div>

<div class="container" style="padding-top:40px;padding-bottom:80px;">
    <div class="row g-4">

        {{-- Results --}}
        <div class="col-lg-8">

            <div style="font-size:13px;color:var(--gc-muted);margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--gc-border);font-style:italic;">
                {{ $blog_list->total() }} {{ __('results found') }}
            </div>

            @forelse($blog_list as $blog)
            @php $img = get_attachment_image_by_id($blog->image, 'grid'); @endphp
            <div style="display:flex;gap:16px;background:#fff;border:1px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;box-shadow:var(--gc-shadow);margin-bottom:16px;transition:all .25s;"
                 onmouseover="this.style.boxShadow='0 12px 40px -8px rgba(200,169,110,.3)';this.style.transform='translateY(-2px)'"
                 onmouseout="this.style.boxShadow='var(--gc-shadow)';this.style.transform='translateY(0)'">

                @if(!empty($img['img_url']))
                <a href="{{ tenant_blog_single_route($blog->slug) }}"
                   style="flex-shrink:0;width:140px;overflow:hidden;background:var(--gc-warm);">
                    <img src="{{ $img['img_url'] }}" alt="{{ $blog->title }}"
                         style="width:100%;height:100%;object-fit:cover;transition:transform .4s;"
                         onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                </a>
                @endif

                <div style="padding:18px;flex:1;min-width:0;font-family:Georgia,serif;">
                    <div style="font-size:11px;color:var(--gc-muted);margin-bottom:6px;font-style:italic;">
                        <i class="las la-calendar-alt"></i> {{ $blog->created_at?->format('d M Y') }}
                    </div>
                    <h3 style="font-size:16px;font-weight:400;color:var(--gc-dark);margin-bottom:8px;line-height:1.4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-style:italic;">
                        <a href="{{ tenant_blog_single_route($blog->slug) }}"
                           style="color:inherit;text-decoration:none;"
                           onmouseover="this.style.color='var(--gc-rose)'" onmouseout="this.style.color='var(--gc-dark)'">
                            {!! \Illuminate\Support\Str::words($blog->title, 10) !!}
                        </a>
                    </h3>
                    <p style="font-size:13px;color:var(--gc-muted);line-height:1.6;margin-bottom:12px;font-style:italic;
                        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                        {!! \Illuminate\Support\Str::words(strip_tags($blog->blog_content), 20) !!}
                    </p>
                    <a href="{{ tenant_blog_single_route($blog->slug) }}"
                       style="display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--gc-rose);text-decoration:none;border-bottom:1px solid var(--gc-rose);padding-bottom:2px;transition:opacity .2s;"
                       onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'">
                        {{ __('Read More') }} <i class="las la-arrow-right"></i>
                    </a>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:60px 20px;background:var(--gc-warm);border-radius:var(--gc-radius);border:1px dashed var(--gc-border);">
                <div style="font-size:44px;margin-bottom:12px;"><i class="las la-search"></i></div>
                <p style="color:var(--gc-muted);margin-bottom:20px;font-style:italic;">{{ __('No blog posts found for') }} "<strong>{{ $query_term }}</strong>"</p>
                <a href="{{ theme_blog_url() }}" class="gc-btn gc-btn-primary">
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

            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;">{{ __('Search Again') }}</div>
                <form action="{{ theme_blog_search_url() }}" method="GET">
                    <div style="display:flex;border:1.5px solid var(--gc-border);border-radius:var(--gc-radius);overflow:hidden;">
                        <input type="text" name="search" value="{{ $query_term }}" placeholder="{{ __('Search articles…') }}"
                               style="flex:1;padding:9px 14px;border:0;font-size:13px;font-family:Georgia,serif;outline:none;font-style:italic;color:var(--gc-dark);">
                        <button type="submit" style="background:var(--gc-rose);border:0;color:#fff;padding:0 16px;cursor:pointer;font-size:16px;transition:background .2s;"
                                onmouseover="this.style.background='var(--gc-dark)'" onmouseout="this.style.background='var(--gc-rose)'">
                            <i class="las la-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            @if(theme_blog_categories()->isNotEmpty())
            <div style="background:var(--gc-ivory);border:1px solid var(--gc-border);border-radius:var(--gc-radius);padding:24px;margin-bottom:24px;box-shadow:var(--gc-shadow);">
                <div style="font-size:10px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--gc-rose);margin-bottom:16px;">{{ __('Categories') }}</div>
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach(theme_blog_categories() as $cat)
                    <li style="border-bottom:1px dashed var(--gc-border);padding:8px 0;">
                        <a href="{{ $cat->url() }}"
                           style="display:flex;align-items:center;justify-content:space-between;font-size:13px;color:var(--gc-dark);text-decoration:none;font-style:italic;"
                           onmouseover="this.style.color='var(--gc-rose)'" onmouseout="this.style.color='var(--gc-dark)'">
                            <span>{{ $cat->name() }}</span>
                            <span style="background:var(--gc-warm);color:var(--gc-rose);font-size:10px;padding:2px 10px;border-radius:50px;">{{ $cat->count() }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

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
