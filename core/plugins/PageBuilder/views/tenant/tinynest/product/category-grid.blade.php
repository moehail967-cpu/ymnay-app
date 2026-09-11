@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<style>
    .tn-sec-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .tn-sec-title {
        font-size: clamp(22px, 3vw, 30px);
        font-weight: 800;
        color: var(--tn-dark, #2D1B2E);
        position: relative;
        padding-bottom: 10px;
    }
    .tn-sec-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: var(--tn-blush, #F2A7C3);
        border-radius: 2px;
    }
    .tn-view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--tn-blush, #F2A7C3);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: gap .2s;
    }
    .tn-view-all:hover { gap: 10px; color: var(--tn-blush, #F2A7C3); }

    .tn-cat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        text-decoration: none;
        background: #fff;
        border: 1.5px solid var(--tn-border, #ECCFE0);
        border-radius: 20px;
        padding: 24px 16px 20px;
        transition: all .25s;
        height: 100%;
    }
    .tn-cat-card:hover {
        border-color: var(--tn-blush, #F2A7C3);
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(242,167,195,.2);
    }
    .tn-cat-img {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        overflow: hidden;
        background: var(--tn-blush-light, #FDE8F1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        flex-shrink: 0;
    }
    .tn-cat-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .tn-cat-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--tn-dark, #2D1B2E);
        margin-bottom: 4px;
        line-height: 1.3;
    }
    .tn-cat-count {
        font-size: 12px;
        color: var(--tn-muted, #7A5E6E);
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--tn-bg, #FFF9FC);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="tn-sec-heading">
                <h2 class="tn-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="tn-view-all">
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
                           class="tn-cat-card">
                            <div class="tn-cat-img">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <span style="font-size:28px;">🍼</span>
                                @endif
                            </div>
                            <div class="tn-cat-name">{{ $category->name }}</div>
                            <div class="tn-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--tn-muted, #7A5E6E);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
