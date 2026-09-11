@php
    $pt = !empty($data['padding_top'])    ? (int)$data['padding_top']    : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
    $shop_url = route('tenant.shop');
@endphp

<section style="background:var(--tc-bg);padding-top:{{$pt}}px;padding-bottom:{{$pb}}px;">
    <div class="container">

        @if(!empty($data['title']))
            <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:36px;flex-wrap:wrap;">
                <div>
                    <div style="display:inline-block;width:40px;height:4px;background:var(--tc-terra);border-radius:2px;margin-bottom:10px;"></div>
                    <h2 style="font-size:clamp(22px,3vw,32px);font-weight:900;color:var(--tc-dark);margin:0;letter-spacing:-.01em;">
                        {{ $data['title'] }}
                    </h2>
                </div>
                <a href="{{ $shop_url }}"
                   style="display:inline-flex;align-items:center;gap:6px;color:var(--tc-olive);font-weight:700;font-size:14px;text-decoration:none;border:2px solid var(--tc-olive);padding:8px 18px;border-radius:6px;transition:background .2s,color .2s;"
                   onmouseover="this.style.background='var(--tc-olive)';this.style.color='#fff'"
                   onmouseout="this.style.background='transparent';this.style.color='var(--tc-olive)'">
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
                           style="display:flex;flex-direction:column;align-items:center;gap:12px;background:#fff;border:2px solid var(--tc-border);border-radius:10px;padding:20px 12px;text-decoration:none;transition:border-color .2s,transform .2s,box-shadow .2s;"
                           onmouseover="this.style.borderColor='var(--tc-terra)';this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 24px rgba(196,124,90,.14)'"
                           onmouseout="this.style.borderColor='var(--tc-border)';this.style.transform='translateY(0)';this.style.boxShadow='none'">
                            <div style="width:64px;height:64px;border-radius:50%;background:var(--tc-olive-light);display:flex;align-items:center;justify-content:center;overflow:hidden;">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <i class="mdi mdi-hiking" style="font-size:28px;color:var(--tc-olive);"></i>
                                @endif
                            </div>
                            <span style="font-size:13px;font-weight:700;color:var(--tc-dark);text-align:center;line-height:1.3;">{{ $category->name }}</span>
                            <span style="font-size:11px;color:var(--tc-muted);">
                                {{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--tc-muted);">{{ __('No categories found.') }}</p>
        @endif

    </div>
</section>
