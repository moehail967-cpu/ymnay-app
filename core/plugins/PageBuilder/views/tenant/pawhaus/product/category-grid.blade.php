@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:var(--ph-bg);">
    <div class="container">
        @if(!empty($data['title']))
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 style="font-size:26px; font-weight:800; color:var(--ph-dark); margin:0;">{{ $data['title'] }}</h2>
                <a href="{{ route('tenant.shop') }}" style="font-size:13px; font-weight:700; color:var(--ph-terra); text-decoration:none;">{{ __('See All') }} <i class="mdi mdi-arrow-right"></i></a>
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
                           style="display:flex;flex-direction:column;align-items:center;text-align:center;padding:20px 12px;background:#fff;border:1.5px solid var(--ph-border);border-radius:var(--ph-radius);transition:all .25s;text-decoration:none;">
                            <div style="width:64px;height:64px;border-radius:50%;background:rgba(200,112,64,.1);display:flex;align-items:center;justify-content:center;margin-bottom:10px;overflow:hidden;">
                                @if(!empty($img_url))
                                    <img src="{{ $img_url }}" alt="{{ $category->name }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <span style="font-size:28px;">🐾</span>
                                @endif
                            </div>
                            <div style="font-size:13px; font-weight:700; color:var(--ph-dark);">{{ $category->name }}</div>
                            <div style="font-size:11px; color:var(--ph-muted); margin-top:3px;">{{ $category->products()->where('status_id', 1)->count() }} {{ __('items') }}</div>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-5" style="color:var(--ph-muted);">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
