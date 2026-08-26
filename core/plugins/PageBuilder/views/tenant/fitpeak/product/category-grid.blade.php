@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<style>
    .fp-sec-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .fp-sec-title {
        font-size: clamp(20px, 2.5vw, 28px);
        font-weight: 900;
        color: var(--fp-text, #E0E0E0);
        text-transform: uppercase;
        letter-spacing: -0.5px;
        position: relative;
        padding-left: 14px;
    }
    .fp-sec-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 100%;
        background: var(--fp-green, #00FF41);
        border-radius: 2px;
    }
    .fp-view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--fp-green, #00FF41);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-decoration: none;
        transition: gap .2s;
    }
    .fp-view-all:hover { gap: 10px; color: var(--fp-green, #00FF41); }

    .fp-cat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        text-decoration: none;
        background: var(--fp-card, #161616);
        border: 1px solid var(--fp-border, #1A3A1A);
        border-radius: 4px;
        padding: 24px 14px 20px;
        transition: all .25s;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .fp-cat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--fp-green, #00FF41);
        transform: scaleX(0);
        transition: transform .25s;
    }
    .fp-cat-card:hover {
        border-color: var(--fp-green-dim, #007A1F);
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,255,65,.08);
    }
    .fp-cat-card:hover::after { transform: scaleX(1); }
    .fp-cat-img {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        overflow: hidden;
        background: var(--fp-surface, #111111);
        border: 1px solid var(--fp-border, #1A3A1A);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        flex-shrink: 0;
    }
    .fp-cat-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: grayscale(20%);
    }
    .fp-cat-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--fp-text, #E0E0E0);
        text-transform: uppercase;
        letter-spacing: .5px;
        margin-bottom: 4px;
    }
    .fp-cat-count {
        font-size: 11px;
        color: var(--fp-green, #00FF41);
        font-weight: 600;
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--fp-surface, #111111);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="fp-sec-heading">
                <h2 class="fp-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="fp-view-all">
                    {{ __('See All') }} <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>
        @endif

        @if($data['categories']->isNotEmpty())
            <div class="row g-3">
                @foreach($data['categories'] as $category)
                    @php
                        $img     = get_attachment_image_by_id($category->image ?? null);
                        $img_url = !empty($img) ? $img['img_url'] : '';
                    @endphp
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('tenant.shop') }}?category={{ $category->slug ?? $category->id }}"
                           class="fp-cat-card">
                            <div class="fp-cat-img">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <span style="font-size:26px;">💪</span>
                                @endif
                            </div>
                            <div class="fp-cat-name">{{ $category->name }}</div>
                            <div class="fp-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--fp-muted, #707070);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
