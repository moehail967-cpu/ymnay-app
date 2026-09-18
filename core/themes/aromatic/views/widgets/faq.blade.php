<section class="ar-faq-section" style="padding-top:{{ $padding_top }}px;padding-bottom:{{ $padding_bottom }}px;">
    <div class="container">
        <div class="ar-section-head"><h2 class="ar-section-title">{{ $title }}</h2></div>
        <div class="ar-faq-list">
            @foreach($items as $item)
                <details class="ar-faq-item" {{ $loop->first ? 'open' : '' }}>
                    <summary>{{ $item['question'] }} <i class="las la-plus" aria-hidden="true"></i></summary>
                    <p>{{ $item['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
