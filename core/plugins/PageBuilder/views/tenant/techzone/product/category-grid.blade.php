@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--tz-surface);">
    <div class="container">
        @if(!empty($data['title']))
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 style="font-size:24px; font-weight:900; color:var(--tz-text); margin:0;">{{ $data['title'] }}</h2>
                <a href="{{ route('tenant.shop') }}" style="font-size:13px; font-weight:700; color:var(--tz-blue); text-decoration:none;">{{ __('View All') }} <i class="mdi mdi-arrow-right"></i></a>
            </div>
        @endif
        @if($data['categories']->isNotEmpty())
            <div class="row g-3">
                @foreach($data['categories'] as $category)
                    @php
                        $img = get_attachment_image_by_id($category->image ?? null);
                        $img_url = !empty($img) ? $img['img_url'] : '';
                    @endphp
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('tenant.shop') }}?category={{ $category->slug ?? $category->id }}"
                           style="display:flex;flex-direction:column;align-items:center;text-align:center;padding:20px 12px;background:var(--tz-card);border:1px solid var(--tz-border);border-radius:var(--tz-radius-xl);transition:border-color .2s;text-decoration:none;">
                            <div style="width:60px;height:60px;border-radius:var(--tz-radius);background:var(--tz-blue-glow);display:flex;align-items:center;justify-content:center;margin-bottom:10px;overflow:hidden;">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <span style="font-size:24px;">💻</span>
                                @endif
                            </div>
                            <div style="font-size:12px; font-weight:700; color:var(--tz-text);">{{ $category->name }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
