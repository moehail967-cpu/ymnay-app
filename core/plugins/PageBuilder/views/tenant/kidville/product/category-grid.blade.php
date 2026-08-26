@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');

    $kv_colors = [
        ['bg' => '#FFEBEE', 'icon' => '#E53935'],
        ['bg' => '#E3F2FD', 'icon' => '#1565C0'],
        ['bg' => '#FFFDE7', 'icon' => '#F9A825'],
        ['bg' => '#E8F5E9', 'icon' => '#43A047'],
        ['bg' => '#F3E5F5', 'icon' => '#7B1FA2'],
        ['bg' => '#FFF3E0', 'icon' => '#E65100'],
    ];
@endphp

<style>
    .kv-sec-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 36px;
        gap: 16px;
    }
    .kv-sec-title {
        font-size: clamp(24px, 3vw, 36px);
        font-weight: 900;
        color: var(--kv-dark);
        margin: 0;
    }
    .kv-view-all {
        font-size: 13px;
        font-weight: 800;
        color: var(--kv-blue);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
        transition: color .2s;
    }
    .kv-view-all:hover { color: var(--kv-red); }
    .kv-cat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        text-decoration: none;
        padding: 24px 14px 18px;
        border-radius: 20px;
        border: 2px solid transparent;
        transition: all .25s ease;
    }
    .kv-cat-card:hover {
        border-color: var(--kv-red);
        box-shadow: 0 8px 28px rgba(229,57,53,.15);
        transform: translateY(-6px) rotate(-1deg);
    }
    .kv-cat-img {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }
    .kv-cat-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .kv-cat-name {
        font-size: 14px;
        font-weight: 800;
        color: var(--kv-dark);
        margin-bottom: 4px;
    }
    .kv-cat-count {
        font-size: 12px;
        color: var(--kv-muted);
    }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--kv-bg, #FFFEF8);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="kv-sec-heading">
                <h2 class="kv-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="kv-view-all">
                    {{ __('See All') }} <i class="mdi mdi-arrow-right"></i>
                </a>
            </div>
        @endif

        @if($data['categories']->isNotEmpty())
            <div class="row g-3">
                @foreach($data['categories'] as $index => $category)
                    @php
                        $img      = get_attachment_image_by_id($category->image ?? null);
                        $img_url  = !empty($img) ? $img['img_url'] : '';
                        $colorSet = $kv_colors[$index % count($kv_colors)];
                    @endphp
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('tenant.shop') }}?category={{ $category->slug ?? $category->id }}"
                           class="kv-cat-card"
                           style="background:{{ $colorSet['bg'] }};">
                            <div class="kv-cat-img" style="background:{{ $colorSet['bg'] }}; border:3px solid {{ $colorSet['icon'] }}30;">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <i class="mdi mdi-toy-brick" style="font-size:30px; color:{{ $colorSet['icon'] }};"></i>
                                @endif
                            </div>
                            <div class="kv-cat-name">{{ $category->name }}</div>
                            <div class="kv-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--kv-muted);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
