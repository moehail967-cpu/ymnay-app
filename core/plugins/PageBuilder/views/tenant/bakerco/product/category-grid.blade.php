@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">

        @if(!empty($data['title']))
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <span class="bk-section-tag d-inline-block mb-2">{{ __('Categories') }}</span>
                    <h2 class="bk-section-title mb-0">{{ $data['title'] }}</h2>
                </div>
                <a href="{{ $shop_url }}" style="color:var(--bk-rose);font-weight:700;text-decoration:none;"
                   onmouseover="this.style.color='var(--bk-rose-deep)'" onmouseout="this.style.color='var(--bk-rose)'">
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
                           class="bk-cat-card d-block text-center text-decoration-none">
                            <div class="bk-cat-icon">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}"
                                         style="width:56px; height:56px; object-fit:cover; border-radius:50%;">
                                @else
                                    <span style="font-size:36px; line-height:1;">🥐</span>
                                @endif
                            </div>
                            <div class="bk-cat-name">{{ $category->name }}</div>
                            <div class="bk-cat-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--bk-muted);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
