@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<style>
    .ms-sec-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 36px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .ms-sec-title {
        font-family: var(--ms-font-head, 'Lora', Georgia, serif);
        font-size: clamp(22px, 3vw, 32px);
        font-weight: 700;
        color: var(--ms-dark, #2A2416);
        position: relative;
        padding-bottom: 12px;
    }
    .ms-sec-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 32px;
        height: 2px;
        background: var(--ms-accent, #C4956A);
    }
    .ms-view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--ms-olive, #7D8C5A);
        font-size: 13px;
        font-weight: 600;
        letter-spacing: .4px;
        text-decoration: none;
        text-transform: uppercase;
        transition: gap .2s;
        padding-bottom: 12px;
    }
    .ms-view-all:hover { gap: 10px; color: var(--ms-olive-deep, #5A6840); }

    .ms-cat-card {
        display: flex;
        flex-direction: column;
        text-decoration: none;
        background: #fff;
        border: 1px solid var(--ms-border, #DDD5C4);
        border-radius: 2px;
        overflow: hidden;
        transition: all .25s;
        height: 100%;
    }
    .ms-cat-card:hover {
        border-color: var(--ms-olive, #7D8C5A);
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(125,140,90,.15);
    }
    .ms-cat-img {
        width: 100%;
        aspect-ratio: 4/3;
        overflow: hidden;
        background: var(--ms-olive-light, #EFF2E8);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ms-cat-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s;
    }
    .ms-cat-card:hover .ms-cat-img img { transform: scale(1.06); }
    .ms-cat-info {
        padding: 14px 16px;
        border-top: 1px solid var(--ms-border, #DDD5C4);
    }
    .ms-cat-name {
        font-family: var(--ms-font-head, 'Lora', Georgia, serif);
        font-size: 14px;
        font-weight: 600;
        color: var(--ms-dark, #2A2416);
        margin-bottom: 2px;
    }
    .ms-cat-count {
        font-size: 12px;
        color: var(--ms-muted, #6E6048);
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--ms-bg, #FAFAF5);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="ms-sec-heading">
                <h2 class="ms-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="ms-view-all">
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
                           class="ms-cat-card">
                            <div class="ms-cat-img">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <span style="font-size:32px;">🏠</span>
                                @endif
                            </div>
                            <div class="ms-cat-info">
                                <div class="ms-cat-name">{{ $category->name }}</div>
                                <div class="ms-cat-count">
                                    {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--ms-muted, #6E6048);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
