@php
    $pt       = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb       = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<style>
    .lg-cat-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 28px 16px;
        background: var(--lg-card, #141414);
        border: 1px solid var(--lg-border, #2A2410);
        text-decoration: none;
        transition: all .25s;
    }
    .lg-cat-card:hover {
        border-color: var(--lg-gold, #C9A84C);
        box-shadow: 0 4px 24px rgba(201,168,76,.12);
        transform: translateY(-3px);
    }
    .lg-cat-icon {
        width: 72px;
        height: 72px;
        border: 1px solid var(--lg-border, #2A2410);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        overflow: hidden;
        background: #1A1600;
    }
    .lg-cat-icon img { width: 100%; height: 100%; object-fit: cover; }
    .lg-cat-icon span { font-size: 30px; line-height: 1; }
    .lg-cat-name {
        font-size: 12px;
        font-weight: 600;
        color: var(--lg-dark, #E8E0D0);
        margin-bottom: 4px;
        letter-spacing: .06em;
        text-transform: uppercase;
        font-family: var(--lg-font-head, 'Cinzel', Georgia, serif);
    }
    .lg-cat-count {
        font-size: 11px;
        color: var(--lg-muted, #8A7E6E);
    }
    .lg-sec-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 36px;
    }
    .lg-sec-title {
        font-size: clamp(20px, 2.5vw, 30px);
        font-weight: 700;
        color: var(--lg-dark, #E8E0D0);
        margin: 0;
        font-family: var(--lg-font-head, 'Cinzel', Georgia, serif);
        letter-spacing: .05em;
    }
    .lg-view-all {
        font-size: 11px;
        font-weight: 600;
        color: var(--lg-gold, #C9A84C);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: .1em;
        text-transform: uppercase;
    }
    .lg-view-all:hover { color: var(--lg-gold-light, #F0D98C); }
</style>

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background: var(--lg-surface, #111111);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="lg-sec-heading">
                <h2 class="lg-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="lg-view-all">
                    {{ __('View All') }} <i class="mdi mdi-arrow-right"></i>
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
                           class="lg-cat-card">
                            <div class="lg-cat-icon">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <span>💎</span>
                                @endif
                            </div>
                            <div class="lg-cat-name">{{ $category->name }}</div>
                            <div class="lg-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('pieces') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--lg-muted, #8A7E6E);">{{ __('No collections found.') }}</p>
        @endif

    </div>
</section>
