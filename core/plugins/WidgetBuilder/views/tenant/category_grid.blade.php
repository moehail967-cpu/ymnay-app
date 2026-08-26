@php
    $col_class = match((int)$columns) {
        2 => 'col-6',
        3 => 'col-6 col-md-4',
        4 => 'col-6 col-md-3',
        5 => 'col-6 col-md col-lg',
        default => 'col-6 col-md-4 col-lg-2',
    };
@endphp

<section class="xgpb-category-grid" style="padding-top:{{$padding_top}}px; padding-bottom:{{$padding_bottom}}px;">
    <div class="container">
        @if(!empty($title))
            <div class="xgpb-section-header d-flex align-items-center justify-content-between mb-5">
                <div>
                    <h2 class="xgpb-section-title">{{ $title }}</h2>
                    @if(!empty($subtitle))<p class="xgpb-section-subtitle mb-0">{{ $subtitle }}</p>@endif
                </div>
                @if(!empty($view_all_text))
                    <a href="{{ $view_all_url }}" class="btn btn-outline-primary">{{ $view_all_text }}</a>
                @endif
            </div>
        @endif

        @if($categories->isNotEmpty())
            <div class="row g-3 justify-content-center">
                @foreach($categories as $cat)
                    @php
                        $img_data = get_attachment_image_by_id($cat->image ?? null);
                        $img_url  = !empty($img_data) ? $img_data['img_url'] : global_asset('assets/common/img/placeholder.jpg');
                    @endphp
                    <div class="{{ $col_class }}">
                        <a href="{{ route('tenant.shop') }}" class="xgpb-cat-card d-flex flex-column align-items-center gap-2 p-3 text-decoration-none">
                            <div class="xgpb-cat-img-wrap">
                                <img src="{{ $img_url }}" alt="{{ $cat->name }}" class="w-100 h-100" loading="lazy" style="object-fit:cover;">
                            </div>
                            <span class="xgpb-cat-name">{{ $cat->name }}</span>
                            @if($show_count && isset($cat->products_count))
                                <span class="xgpb-cat-count text-muted small">{{ $cat->products_count }} {{ __('items') }}</span>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-muted py-5">{{ __('No categories found.') }}</p>
        @endif
    </div>
</section>

<style>
.xgpb-cat-card { border-radius: 12px; background: #fff; border: 1.5px solid #e9ecef; transition: all .3s; text-align: center; }
.xgpb-cat-card:hover { border-color: var(--main-color, #0d6efd); box-shadow: 0 4px 16px rgba(0,0,0,.1); transform: translateY(-3px); }
.xgpb-cat-img-wrap { width: 72px; height: 72px; border-radius: 50%; overflow: hidden; background: #f0f0f0; flex-shrink: 0; }
.xgpb-cat-name { font-size: .875rem; font-weight: 600; color: #1a1a2e; line-height: 1.3; }
</style>
