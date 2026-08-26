@extends('tenant.frontend.frontend-page-master')
@section('title') {{ theme_site_name() }} @endsection

@section('content')
@if(isset($page_post->rendered_content))
    @include('tenant.frontend.partials.page-builder-content', ['page_post' => $page_post])
@else

{{-- ═══════════════════════════════════════════
     HERO
═══════════════════════════════════════════ --}}
<section class="dk-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <span class="dk-section-tag">
                    <i class="mdi mdi-cog" style="color:var(--dk-red);"></i>
                    {{ get_static_option('home_hero_tag') ?: __('Trusted By Mechanics') }}
                </span>
                <h1 class="dk-hero-title">
                    {!! nl2br(e(get_static_option('home_hero_title') ?: __('Parts. Tools. Performance.'))) !!}
                    <span>{{ get_static_option('home_hero_title_highlight') ?: __('All Here.') }}</span>
                </h1>
                <p class="dk-hero-sub">
                    {{ get_static_option('home_hero_subtitle') ?: __('OEM and aftermarket parts for every make and model. From oil filters to full engine rebuilds — we stock what mechanics and enthusiasts need.') }}
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-red">
                        <i class="mdi mdi-car-wrench"></i> {{ get_static_option('home_hero_cta_primary') ?: __('Shop Now') }}
                    </a>
                    <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-ghost">
                        <i class="mdi mdi-wrench-outline"></i> {{ get_static_option('home_hero_cta_secondary') ?: __('Browse All') }}
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                @php $hero_img_id = get_static_option('home_hero_image'); @endphp
                @if($hero_img_id)
                    <div class="dk-hero-img" style="overflow:hidden;background:transparent;border:0;">
                        {!! render_image_markup_by_attachment_id($hero_img_id, 'dk-hero-main-img', 'full') !!}
                    </div>
                @else
                    <div class="dk-hero-img">
                        <i class="mdi mdi-car-wrench" style="color:var(--dk-border);font-size:130px;"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     CATEGORIES
═══════════════════════════════════════════ --}}
@php $categories = theme_categories()->take(6); @endphp
@if($categories->count())
<section class="dk-section-categories">
    <div class="container">
        <div class="text-center mb-4">
            <span class="dk-section-tag">{{ __('Categories') }}</span>
            <h2 class="dk-section-title">{{ __('Shop by') }} <span>{{ __('Department') }}</span></h2>
            <p class="dk-section-sub">{{ __('Everything under the bonnet and beyond.') }}</p>
        </div>
        <div class="row g-3">
            @foreach($categories as $cat)
            <div class="col-6 col-md-4 col-lg-2">
                <a href="{{ theme_category_url($cat->slug) }}" class="dk-cat-card">
                    @if($cat->image_id)
                        <div class="dk-cat-icon" style="padding:0;overflow:hidden;border-radius:50%;">
                            <img src="{{ theme_category_image($cat) }}" alt="{{ $cat->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:50%;">
                        </div>
                    @else
                        <div class="dk-cat-icon"><i class="mdi mdi-shape-outline"></i></div>
                    @endif
                    <div class="dk-cat-name">{{ $cat->name }}</div>
                    @if(isset($cat->product_count))
                        <div class="dk-cat-count">{{ $cat->product_count }} {{ __('items') }}</div>
                    @endif
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════
     FEATURED PRODUCTS
═══════════════════════════════════════════ --}}
@php $featured = theme_featured_products(8); @endphp
@if($featured->count())
<section class="dk-section-products" style="background:var(--dk-carbon);padding:56px 0 72px;">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <span class="dk-section-tag">{{ __('Best Sellers') }}</span>
                <h2 class="dk-section-title mb-0">{{ __('Customer') }} <span>{{ __('Favourites') }}</span></h2>
            </div>
            <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-outline dk-btn-sm">
                {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-3">
            @foreach($featured as $product)
            @php
                $pdata     = theme_product_price($product);
                $img_url   = theme_product_image($product->image_id ?? null, 'grid');
                $badge     = $product->badge?->name ?? null;
                $discount  = $pdata['discount'];
                $prod_url  = theme_product_url($product->slug);
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <div class="dk-card">
                    <div class="dk-card-img">
                        @if($img_url)
                            <a href="{{ $prod_url }}"><img src="{{ $img_url }}" alt="{{ $product->name }}" loading="lazy"></a>
                        @else
                            <a href="{{ $prod_url }}" class="dk-card-no-img"><i class="mdi mdi-car-wrench"></i></a>
                        @endif
                        @if($discount)
                            <span class="dk-card-badge dk-badge-sale">{{ $discount }}% {{ __('off') }}</span>
                        @elseif($badge)
                            <span class="dk-card-badge dk-badge-hot">{{ $badge }}</span>
                        @endif
                        <button class="dk-card-wishlist add-to-wishlist-btn" data-product_id="{{ $product->id }}" aria-label="{{ __('Wishlist') }}">
                            <i class="mdi mdi-heart-outline"></i>
                        </button>
                    </div>
                    <div class="dk-card-body">
                        @if($product->category)
                            <div class="dk-card-cat">{{ $product->category->name }}</div>
                        @endif
                        <a href="{{ $prod_url }}" class="dk-card-name">{{ \Illuminate\Support\Str::words($product->name, 7) }}</a>
                        <div class="dk-stars">{!! theme_star_rating($product) !!}</div>
                        <div class="dk-card-price">
                            <span class="dk-price-sale">{{ amount_with_currency_symbol($pdata['sale_price']) }}</span>
                            @if($pdata['regular_price'])
                                <span class="dk-price-orig">{{ amount_with_currency_symbol($pdata['regular_price']) }}</span>
                            @endif
                        </div>
                        <button class="dk-card-atc dk-atc-btn" data-id="{{ $product->id }}" data-slug="{{ $product->slug }}">
                            <i class="mdi mdi-cart-plus"></i> {{ __('Add to Cart') }}
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════
     PROMO BANNER
═══════════════════════════════════════════ --}}
<section class="dk-section-promo">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 mb-4 mb-lg-0">
                <span class="dk-section-tag">
                    <i class="mdi mdi-tag-outline" style="color:var(--dk-red);"></i>
                    {{ get_static_option('home_promo_tag') ?: __('Special Offer') }}
                </span>
                <h2 class="dk-section-title" style="font-size:clamp(28px,4vw,46px);">
                    {!! get_static_option('home_promo_title') ?: (__('Trade Accounts &') . ' <span>' . __('Fleet Pricing') . '</span>') !!}
                </h2>
                <p style="color:var(--dk-silver);font-size:15px;line-height:1.7;margin-bottom:28px;max-width:500px;">
                    {{ get_static_option('home_promo_text') ?: __('Garages, dealerships, and fleets get dedicated pricing, credit accounts, and priority support. Apply for a trade account in minutes.') }}
                </p>
                <a href="{{ theme_shop_url() }}" class="dk-btn dk-btn-red">
                    <i class="mdi mdi-store"></i>
                    {{ get_static_option('home_promo_cta') ?: __('Shop Now') }}
                </a>
            </div>
            <div class="col-lg-5 text-center">
                @php $promo_img_id = get_static_option('home_promo_image'); @endphp
                @if($promo_img_id)
                    {!! render_image_markup_by_attachment_id($promo_img_id, 'img-fluid', 'full') !!}
                @else
                    <div style="font-size:90px;line-height:1;opacity:.6;">
                        <i class="mdi mdi-car-wrench"></i><i class="mdi mdi-cog"></i><i class="mdi mdi-wrench"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════
     LATEST BLOG POSTS (only if blog module active)
═══════════════════════════════════════════ --}}
@php
    $recent_blogs = collect();
    try {
        $recent_blogs = \Modules\Blog\Entities\Blog::where('status', 1)->latest()->take(3)->get();
    } catch (\Throwable $e) {}
@endphp
@if($recent_blogs->count())
<section style="background:var(--dk-surface);border-top:1px solid var(--dk-border);padding:56px 0 72px;">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <span class="dk-section-tag">{{ __('Workshop Bulletin') }}</span>
                <h2 class="dk-section-title mb-0">{{ __('Latest') }} <span>{{ __('Articles') }}</span></h2>
            </div>
            @php $blog_page_slug = get_page_slug(get_static_option('blog_page'), 'blog_page'); @endphp
            @if($blog_page_slug)
                <a href="{{ dynamicRoute($blog_page_slug) }}" class="dk-btn dk-btn-outline dk-btn-sm">
                    {{ __('All Articles') }} <i class="mdi mdi-arrow-right"></i>
                </a>
            @endif
        </div>
        <div class="row g-3">
            @foreach($recent_blogs as $post)
            @php
                $b_img = $post->image ? get_attachment_image_by_id($post->image, 'full') : null;
                $b_url = dynamicRoute($post->slug ?? '');
            @endphp
            <div class="col-md-4">
                <a href="{{ $b_url }}" class="dk-blog-card">
                    <div class="dk-blog-card-img">
                        @if(!empty($b_img['img_url']))
                            <img src="{{ $b_img['img_url'] }}" alt="{{ $post->title }}" loading="lazy">
                        @else
                            <div class="dk-blog-card-placeholder"><i class="mdi mdi-newspaper-variant-outline"></i></div>
                        @endif
                    </div>
                    <div class="dk-blog-card-body">
                        <div class="dk-blog-card-meta">
                            <i class="mdi mdi-calendar-outline"></i>
                            {{ optional($post->created_at)->format('M d, Y') }}
                        </div>
                        <h3 class="dk-blog-card-title">{{ \Illuminate\Support\Str::words($post->title, 10) }}</h3>
                        <p class="dk-blog-card-excerpt">{{ \Illuminate\Support\Str::words(strip_tags($post->description ?? ''), 18) }}</p>
                        <span class="dk-blog-card-link">{{ __('Read More') }} <i class="mdi mdi-arrow-right"></i></span>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════
     NEWSLETTER
═══════════════════════════════════════════ --}}
<section class="dk-section-newsletter">
    <div class="container">
        <div class="dk-newsletter-wrap">
            <span class="dk-section-tag">
                <i class="mdi mdi-email-fast-outline" style="color:var(--dk-red);"></i>
                {{ __('Workshop Bulletin') }}
            </span>
            <h3 class="dk-newsletter-title">{{ __('TOOLS, TIPS &') }} <span>{{ __('TRADE DEALS') }}</span></h3>
            <p class="dk-newsletter-sub">{{ __('Weekly deals on parts, workshop guides, and trade member exclusives.') }}</p>
            <form class="dk-newsletter-bar newsletter-form footer-widget" method="POST" action="{{ theme_newsletter_subscribe_url() }}">
                @csrf
                <div class="form-message-show" style="margin-bottom:8px;font-size:13px;"></div>
                <div class="dk-newsletter-input-group">
                    <input type="email" name="email" class="email" placeholder="{{ __('Your email address…') }}" required>
                    <button type="submit" class="newsletter-submit-btn dk-btn dk-btn-red">
                        {{ __('Subscribe') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@endif
@endsection
