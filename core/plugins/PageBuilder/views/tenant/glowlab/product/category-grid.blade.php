@php
    $pt       = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb       = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<style>
    .gl-cat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 28px 16px;
        background: #fff;
        border: 1px solid var(--gl-border, #E8DCC8);
        border-radius: 12px;
        text-decoration: none;
        transition: all .25s;
    }
    .gl-cat-card:hover {
        border-color: var(--gl-gold, #C9A44C);
        box-shadow: 0 6px 24px rgba(201,164,76,.15);
        transform: translateY(-3px);
    }
    .gl-cat-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: var(--gl-gold-light, #F5E9D0);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        overflow: hidden;
        border: 2px solid var(--gl-gold, #C9A44C);
    }
    .gl-cat-icon img { width: 100%; height: 100%; object-fit: cover; }
    .gl-cat-icon span { font-size: 30px; line-height: 1; }
    .gl-cat-name {
        font-size: 13px;
        font-weight: 700;
        color: var(--gl-dark, #1A1208);
        margin-bottom: 4px;
    }
    .gl-cat-count {
        font-size: 11px;
        color: var(--gl-muted, #6B5E4A);
    }
    .gl-sec-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 36px;
    }
    .gl-sec-title {
        font-size: clamp(20px, 2.5vw, 30px);
        font-weight: 800;
        color: var(--gl-dark, #1A1208);
        margin: 0;
    }
    .gl-view-all {
        font-size: 13px;
        font-weight: 600;
        color: var(--gl-gold-deep, #A8852E);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .gl-view-all:hover { color: var(--gl-gold, #C9A44C); }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--gl-bg, #FAFAFA);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="gl-sec-heading">
                <h2 class="gl-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="gl-view-all">
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
                           class="gl-cat-card">
                            <div class="gl-cat-icon">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <span>✨</span>
                                @endif
                            </div>
                            <div class="gl-cat-name">{{ $category->name }}</div>
                            <div class="gl-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--gl-muted, #6B5E4A);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
