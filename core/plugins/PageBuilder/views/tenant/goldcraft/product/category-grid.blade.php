@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<style>
    .gc-sec-heading {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        margin-bottom: 36px;
        gap: 16px;
    }
    .gc-sec-title {
        font-family: var(--gc-font-head);
        font-size: clamp(26px, 3vw, 38px);
        font-weight: 700;
        color: var(--gc-dark);
        margin: 0;
    }
    .gc-view-all {
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--gc-rose);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        transition: color .2s;
    }
    .gc-view-all:hover { color: var(--gc-rose-deep); }
    .gc-cat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        text-decoration: none;
        padding: 28px 16px 22px;
        background: var(--gc-ivory);
        border: 1px solid var(--gc-border);
        border-radius: 12px;
        transition: all .25s ease;
    }
    .gc-cat-card:hover {
        border-color: var(--gc-rose);
        box-shadow: 0 8px 28px rgba(200,149,108,.15);
        transform: translateY(-4px);
        background: #fff;
    }
    .gc-cat-img {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        overflow: hidden;
        background: var(--gc-rose-light);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        border: 2px solid var(--gc-border);
    }
    .gc-cat-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .gc-cat-name {
        font-family: var(--gc-font-head);
        font-size: 15px;
        font-weight: 700;
        color: var(--gc-dark);
        margin-bottom: 4px;
    }
    .gc-cat-count {
        font-size: 12px;
        color: var(--gc-muted);
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--gc-bg, #FAF7F2);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="gc-sec-heading">
                <h2 class="gc-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="gc-view-all">
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
                           class="gc-cat-card">
                            <div class="gc-cat-img">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <i class="mdi mdi-diamond-stone" style="font-size:28px; color:var(--gc-rose);"></i>
                                @endif
                            </div>
                            <div class="gc-cat-name">{{ $category->name }}</div>
                            <div class="gc-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--gc-muted);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
