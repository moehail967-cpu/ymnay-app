@php
    $uid    = 'bpauth' . substr(md5(uniqid()), 0, 8);
    $colors = ['#e8f4f0', '#ede8f8', '#fde8ec', '#e8eef8', '#fef3e8'];
@endphp
<section class="bp-authors-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="bp-slider-head">
            <h2 class="bp-section-title">{{ $title }}</h2>
            <div class="bp-slider-arrows">
                <button class="bp-arrow" id="{{ $uid }}-prev" aria-label="{{ __('Previous') }}">
                    <i class="las la-angle-left"></i>
                </button>
                <button class="bp-arrow" id="{{ $uid }}-next" aria-label="{{ __('Next') }}">
                    <i class="las la-angle-right"></i>
                </button>
            </div>
        </div>
        <div class="bp-slider-wrap" id="{{ $uid }}">
            <div class="bp-slider-track">
                @forelse($authors as $i => $author)
                @php $bg = $colors[$i % count($colors)]; @endphp
                <div class="bp-author-card" style="background:{{ $bg }};">
                    <div class="bp-author-info">
                        <div class="bp-author-name">{{ $author->name }}</div>
                        <div class="bp-author-count">{{ __('Books') }}</div>
                    </div>
                    <div class="bp-author-avatar">
                        @php $logo = $author->logo; @endphp
                        @if($logo && !empty($logo->path))
                            <img src="{{ $logo->path }}" alt="{{ $author->name }}" loading="lazy">
                        @else
                            <div class="bp-author-initial">{{ strtoupper(substr($author->name, 0, 1)) }}</div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-muted">{{ __('No authors yet.') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</section>
<script>
(function(){
    var wrap = document.getElementById('{{ $uid }}');
    var prev = document.getElementById('{{ $uid }}-prev');
    var next = document.getElementById('{{ $uid }}-next');
    if (!wrap) return;
    var scrollAmt = 270;
    if (prev) prev.addEventListener('click', function(){ wrap.scrollBy({ left: -scrollAmt, behavior: 'smooth' }); });
    if (next) next.addEventListener('click', function(){ wrap.scrollBy({ left:  scrollAmt, behavior: 'smooth' }); });
})();
</script>
