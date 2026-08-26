@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px;">
    <div class="container">

        @if(!empty($data['title']))
            <div class="ch-sec-heading">
                <h2 class="ch-sec-title">{{ $data['title'] }}</h2>
                <a href="{{ $shop_url }}" class="ch-view-all">
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
                           class="ch-cuisine-card">
                            <div class="ch-cuisine-img">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}">
                                @else
                                    <i class="mdi mdi-food" style="font-size:32px; color:var(--ch-muted);"></i>
                                @endif
                            </div>
                            <div class="ch-cuisine-name">{{ $category->name }}</div>
                            <div class="ch-cuisine-count">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--ch-muted);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
