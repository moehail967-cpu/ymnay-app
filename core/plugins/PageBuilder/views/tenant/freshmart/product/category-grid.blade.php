@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--fm-bg);">
    <div class="container">

        @if(!empty($data['title']))
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <span style="display:inline-block; background:var(--fm-green-light); color:var(--fm-green); border:1px solid var(--fm-border); padding:4px 14px; border-radius:50px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; margin-bottom:8px;">
                        {{ __('Categories') }}
                    </span>
                    <h2 class="fm-cat-section-title mb-0"
                        style="font-size:clamp(20px,2.5vw,28px); font-weight:800; color:var(--fm-dark); font-family:var(--fm-font-head);">
                        {{ $data['title'] }}
                    </h2>
                </div>
                <a href="{{ $shop_url }}"
                   style="color:var(--fm-green);font-weight:700;text-decoration:none;"
                   onmouseover="this.style.color='var(--fm-green-deep)'" onmouseout="this.style.color='var(--fm-green)'">
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
                           class="fm-cat-card d-block text-center text-decoration-none"
                           style="background:#fff; border:1px solid var(--fm-border); border-radius:16px; padding:20px 12px; transition:all .2s;"
                           onmouseover="this.style.borderColor='var(--fm-green)';this.style.boxShadow='0 4px 16px rgba(46,125,50,.12)'"
                           onmouseout="this.style.borderColor='var(--fm-border)';this.style.boxShadow='none'">
                            <div style="width:64px; height:64px; border-radius:50%; background:var(--fm-green-light); display:flex; align-items:center; justify-content:center; margin:0 auto 12px;">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}"
                                         style="width:44px; height:44px; object-fit:cover; border-radius:50%;">
                                @else
                                    <span style="font-size:28px; line-height:1;">🥦</span>
                                @endif
                            </div>
                            <div style="font-size:14px; font-weight:700; color:var(--fm-dark); margin-bottom:4px;">
                                {{ $category->name }}
                            </div>
                            <div style="font-size:12px; color:var(--fm-muted);">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--fm-muted);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
