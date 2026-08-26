@php
    $pt = !empty($data['padding_top']) ? (int)$data['padding_top'] : 80;
    $pb = !empty($data['padding_bottom']) ? (int)$data['padding_bottom'] : 80;
@endphp

<section style="padding-top:{{$pt}}px; padding-bottom:{{$pb}}px; background:#f8f9fa;">
    <div class="container">
        @if(!empty($data['title']))
            <div class="text-center mb-5">
                <h2 style="font-size:2rem; font-weight:700; color:#1a1a2e; margin-bottom:.5rem;">{{ $data['title'] }}</h2>
                <div style="width:56px; height:4px; background:var(--sectionC,#0d6efd); margin:0 auto; border-radius:2px;"></div>
            </div>
        @endif

        @if($data['categories']->isNotEmpty())
            <div class="row g-3 justify-content-center">
                @foreach($data['categories'] as $category)
                    @php
                        $img = get_attachment_image_by_id($category->image ?? null);
                        $img_url = !empty($img) ? $img['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                    @endphp
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="{{ route('tenant.shop') }}"
                           class="d-flex flex-column align-items-center gap-2 p-3 text-decoration-none"
                           style="border-radius:12px; background:#fff; border:1.5px solid #e9ecef; transition:all .3s; text-align:center;">
                            <div style="width:64px; height:64px; border-radius:50%; overflow:hidden; background:#f0f0f0; flex-shrink:0;">
                                <img src="{{ $img_url }}" alt="{{ $category->name }}"
                                     class="w-100 h-100" style="object-fit:cover;">
                            </div>
                            <span style="font-size:.85rem; font-weight:600; color:#1a1a2e; line-height:1.3;">
                                {{ $category->name }}
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>
