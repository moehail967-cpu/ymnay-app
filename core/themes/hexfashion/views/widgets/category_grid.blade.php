{{-- HexFashion: Category Grid --}}
<section class="hf-cat-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="hf-section-head">
            <h2 class="hf-section-title">{{ $title }}</h2>
        </div>
        <div class="hf-cat-grid">
            @forelse($categories as $category)
            @php
                $img_data = isset($category->logo) ? get_attachment_image_by_id($category->logo) : null;
                $img_url  = $img_data['img_url'] ?? null;
                $url      = theme_category_url($category->slug);
            @endphp
            <a href="{{ $url }}" class="hf-cat-card">
                <div class="hf-cat-img">
                    @if($img_url)
                        <img src="{{ $img_url }}" alt="{{ $category->name }}" loading="lazy">
                    @else
                        <div class="hf-cat-img-ph"><i class="las la-tshirt"></i></div>
                    @endif
                    <div class="hf-cat-overlay"></div>
                </div>
                <div class="hf-cat-name">
                    <span>{{ $category->name }}</span>
                    <i class="las la-arrow-right"></i>
                </div>
            </a>
            @empty
            <p class="text-muted py-3">{{ __('No categories found.') }}</p>
            @endforelse
        </div>
    </div>
</section>
<style>
.hf-cat-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; }
.hf-cat-card { display:flex; flex-direction:column; text-decoration:none; overflow:hidden; }
.hf-cat-img { position:relative; aspect-ratio:2/3; overflow:hidden; background:#f5f5f5; }
.hf-cat-img img { width:100%; height:100%; object-fit:cover; transition:transform .4s ease; }
.hf-cat-card:hover .hf-cat-img img { transform:scale(1.06); }
.hf-cat-img-ph { width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:5rem;color:#ccc; }
.hf-cat-overlay { position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,.4) 0%,transparent 60%); }
.hf-cat-name { display:flex;justify-content:space-between;align-items:center;padding:12px 0;font-size:14px;font-weight:700;color:#0a0a0a;text-transform:uppercase;letter-spacing:1px;border-bottom:2px solid #0a0a0a;margin-top:2px; }
.hf-cat-name i { font-size:16px; transition:transform .2s; }
.hf-cat-card:hover .hf-cat-name i { transform:translateX(4px); }
@media(max-width:768px) { .hf-cat-grid { grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px) { .hf-cat-grid { grid-template-columns:1fr; } }
</style>
