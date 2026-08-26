@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<style>
    .vl-sec-heading {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 36px;
        gap: 16px;
    }
    .vl-sec-title {
        font-family: var(--vl-font-head);
        font-size: clamp(26px, 3vw, 38px);
        font-weight: 700;
        color: var(--vl-dark);
        margin: 0;
    }
    .vl-view-all {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: var(--vl-champagne);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        transition: color .2s;
    }
    .vl-view-all:hover { color: var(--vl-plum); }
    .vl-cat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        text-decoration: none;
        padding: 24px 16px;
        background: #fff;
        border: 1px solid var(--vl-border);
        border-radius: 16px;
        transition: all .25s ease;
    }
    .vl-cat-card:hover {
        border-color: var(--vl-plum);
        box-shadow: 0 8px 28px rgba(92,31,107,.12);
        transform: translateY(-4px);
    }
    .vl-cat-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        background: var(--vl-plum-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }
    .vl-cat-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .vl-cat-name {
        font-family: var(--vl-font-head);
        font-size: 15px;
        font-weight: 700;
        color: var(--vl-dark);
        margin-bottom: 4px;
    }
    .vl-cat-count {
        font-size: 12px;
        color: var(--vl-muted);
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--vl-bg, #F8F5F2);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="vl-sec-heading">
                <h2 class="vl-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="vl-view-all">
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
                           class="vl-cat-card">
                            <div class="vl-cat-img">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <i class="mdi mdi-hanger" style="font-size:28px; color:var(--vl-plum);"></i>
                                @endif
                            </div>
                            <div class="vl-cat-name">{{ $category->name }}</div>
                            <div class="vl-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--vl-muted);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
