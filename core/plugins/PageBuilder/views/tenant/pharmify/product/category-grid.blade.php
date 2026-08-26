@php
    $pt       = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb       = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<style>
    .pf-cat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 24px 16px;
        background: #fff;
        border: 1px solid var(--pf-border, #DDE6EA);
        text-decoration: none;
        transition: all .2s;
    }
    .pf-cat-card:hover {
        border-color: var(--pf-teal, #00897B);
        box-shadow: 0 4px 20px rgba(0,137,123,.1);
        transform: translateY(-2px);
    }
    .pf-cat-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--pf-teal-light, #E0F2F1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .pf-cat-icon img { width: 100%; height: 100%; object-fit: cover; }
    .pf-cat-icon span { font-size: 28px; line-height: 1; }
    .pf-cat-name {
        font-size: 13px;
        font-weight: 600;
        color: var(--pf-dark, #1A2332);
        margin-bottom: 4px;
    }
    .pf-cat-count {
        font-size: 11px;
        color: var(--pf-muted, #607080);
    }
    .pf-sec-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 32px;
    }
    .pf-sec-title {
        font-size: clamp(20px, 2.5vw, 28px);
        font-weight: 800;
        color: var(--pf-dark, #1A2332);
        margin: 0;
    }
    .pf-view-all {
        font-size: 13px;
        font-weight: 600;
        color: var(--pf-teal, #00897B);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .pf-view-all:hover { color: var(--pf-teal-deep, #006358); }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--pf-bg, #F7FAFB);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="pf-sec-heading">
                <h2 class="pf-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="pf-view-all">
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
                           class="pf-cat-card">
                            <div class="pf-cat-icon">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <span>💊</span>
                                @endif
                            </div>
                            <div class="pf-cat-name">{{ $category->name }}</div>
                            <div class="pf-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--pf-muted, #607080);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
